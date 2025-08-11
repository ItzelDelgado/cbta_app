<?php

namespace App\Http\Controllers\Admin\Oncologicos;

use App\Http\Controllers\Controller;
use App\Models\Oncologicos\MedicineOnco;
use App\Models\Oncologicos\MedicinesCatalog;
use App\Models\Oncologicos\Mezcla;
use App\Models\Oncologicos\MezclaMedicamento;
use App\Models\Oncologicos\SolicitudOnco;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Validator;

class SolicitudController extends Controller
{
    public function index()
    {
        $solicitudes = SolicitudOnco::with(['user.hospital'])
            ->orderByDesc('id')
            ->get();


        return view('admin.oncologicos.solicitudes.index', compact('solicitudes'));
    }

    public function create()
    {
        $user = Auth::user();
        $listaId = $user->medicine_list_id;

        $medicamentos = DB::table('medicine_medicine_lists')
            ->join('medicine_oncos', 'medicine_medicine_lists.medicine_id', '=', 'medicine_oncos.id')
            ->join('medicines_catalog', 'medicine_oncos.catalog_id', '=', 'medicines_catalog.id')
            ->where('medicine_medicine_lists.medicine_list_id', $listaId)
            ->select(
                'medicine_oncos.id as id',
                'medicine_oncos.precio',
                'medicine_oncos.lote',
                'medicine_oncos.caducidad',
                'medicines_catalog.denominacion',
                'medicines_catalog.presentacion',
                'medicines_catalog.id as catalog_id'
            )
            ->get();

        // Cargar diluyentes y vías por catálogo
        $infoAdicional = [];

        foreach ($medicamentos as $med) {
            $diluyentes = DB::table('diluent_medicine_catalog')
                ->join('diluents', 'diluent_medicine_catalog.diluent_id', '=', 'diluents.id')
                ->where('diluent_medicine_catalog.medicine_catalog_id', $med->catalog_id)
                ->select('diluents.id', 'diluents.name')
                ->get();

            $vias = DB::table('administration_route_medicine_catalog')
                ->join('administration_routes', 'administration_route_medicine_catalog.administration_route_id', '=', 'administration_routes.id')
                ->where('administration_route_medicine_catalog.medicine_catalog_id', $med->catalog_id)
                ->select('administration_routes.id', 'administration_routes.name')
                ->get();

            $infoAdicional[$med->id] = [
                'diluyentes' => $diluyentes,
                'vias' => $vias
            ];
        }

        return view('admin.oncologicos.solicitudes.create', [
            'medicamentos' => $medicamentos,
            'infoAdicional' => $infoAdicional // ✅ Esto sí funciona con @json()
        ]);
    }

    public function store(Request $request)
    {
        // Validación general del formulario
        $request->validate([
            'paciente_nombre' => 'required|string|max:255',
            'servicio' => 'required|string|max:255',
            'registro' => 'required|string|max:255',
            'sexo' => 'required|in:M,F',
            'fecha_nacimiento' => 'required|date',
            'peso' => 'required|numeric|min:1',
            'piso' => 'required|string|max:50',
            'cama' => 'required|string|max:50',
            'diagnostico' => 'required|string|max:255',
            'medico_nombre' => 'required|string|max:255',
            'medico_cedula' => 'required|string|max:255',
            'fecha_entrega' => 'required|date',
            'observaciones' => 'nullable|string|max:500',
            'mezclas' => 'required|string'
        ]);

        $mezclas = json_decode($request->mezclas, true);
        if (!is_array($mezclas)) {
            return back()->withErrors(['mezclas' => 'El formato del campo mezclas no es válido.'])->withInput();
        }

        DB::beginTransaction();
        try {
            $user = auth()->user();

            // Obtener precios personalizados
            $precios = DB::table('medicine_medicine_lists')
                ->where('medicine_list_id', $user->medicine_list_id)
                ->pluck('precio', 'medicine_id');

            // Crear solicitud principal
            $solicitud = SolicitudOnco::create([
                'user_id' => $user->id,
                'servicio' => $request->servicio,
                'nombre_paciente' => $request->paciente_nombre,
                'sexo' => $request->sexo,
                'edad' => null,
                'peso' => $request->peso,
                'cama' => $request->cama,
                'piso' => $request->piso,
                'registro_paciente' => $request->registro,
                'fecha_nacimiento' => $request->fecha_nacimiento,
                'diagnostico' => $request->diagnostico,
                'fecha_entrega' => $request->fecha_entrega,
                'observaciones' => $request->observaciones,
                'nombre_medico' => $request->medico_nombre,
                'cedula_medico' => $request->medico_cedula,
                'estado' => 'pendiente',
                'remision' => null,
            ]);

            foreach ($mezclas as $mezclaData) {
                $mezcla = Mezcla::create([
                    'solicitud_id' => $solicitud->id,
                    'volumen_dilucion' => $mezclaData['volumen_dilucion'],
                    'tiempo_infusion' => $mezclaData['tiempo_infusion'],
                    'estado' => 'pendiente',
                ]);

                foreach ($mezclaData['medicamentos'] as $medicamento) {
                    $medicine = MedicineOnco::with('catalog')->find($medicamento['medicamento_id']);

                    if (!$medicine || !$medicine->catalog) {
                        throw new \Exception("No se encontró información del catálogo para el medicamento ID {$medicamento['medicamento_id']}.");
                    }

                    $catalog = $medicine->catalog;

                    if (!$catalog) {
                        throw new \Exception("No se encontró información del catálogo para el medicamento ID {$medicamento['medicamento_id']}.");
                    }

                    // Validar concentración
                    $volumen = floatval($mezclaData['volumen_dilucion']);
                    $dosis = floatval($medicamento['dosis']);
                    $concentracion = $volumen > 0 ? $dosis / $volumen : 0;

                    if ($concentracion < $catalog->conc_min || $concentracion > $catalog->conc_max) {
                        throw new \Exception("La concentración de '{$catalog->denominacion}' está fuera del rango permitido ({$catalog->conc_min} - {$catalog->conc_max} mL). Dosis: {$dosis}, Volumen: {$volumen}.");
                    }

                    $precio = $precios[$medicamento['medicamento_id']] ?? 0;

                    MezclaMedicamento::create([
                        'mezcla_id' => $mezcla->id,
                        'medicamento_id' => $medicamento['medicamento_id'],
                        'nombre_medicamento' => $medicamento['nombre'],
                        'dosis' => $dosis,
                        'diluyente_id' => $medicamento['diluyente_id'],
                        'via_administracion_id' => $medicamento['via_administracion_id'],
                        'precio_unitario' => $precio,
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('admin.oncologicos.solicitudes.index')
                ->with('success', 'Solicitud registrada correctamente.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }



    public function show($id)
    {
        // $solicitud = SolicitudOnco::with(['user.hospital', 'mezclas'])
        //     ->findOrFail($id);

        // return view('admin.oncologicos.solicitudes.show', compact('solicitud'));
    }

    public function edit($id)
    {
        $solicitud = SolicitudOnco::with(['mezclas.medicamentos'])->findOrFail($id);
        $user = Auth::user();
        $listaId = $user->medicine_list_id;

        $medicamentos = DB::table('medicine_medicine_lists')
            ->join('medicine_oncos', 'medicine_medicine_lists.medicine_id', '=', 'medicine_oncos.id')
            ->join('medicines_catalog', 'medicine_oncos.catalog_id', '=', 'medicines_catalog.id')
            ->where('medicine_medicine_lists.medicine_list_id', $listaId)
            ->select(
                'medicine_oncos.id as id',
                'medicine_oncos.precio',
                'medicine_oncos.lote',
                'medicine_oncos.caducidad',
                'medicines_catalog.denominacion',
                'medicines_catalog.presentacion',
                'medicines_catalog.id as catalog_id'
            )
            ->get();

        $infoAdicional = [];

        foreach ($medicamentos as $med) {
            $diluyentes = DB::table('diluent_medicine_catalog')
                ->join('diluents', 'diluent_medicine_catalog.diluent_id', '=', 'diluents.id')
                ->where('diluent_medicine_catalog.medicine_catalog_id', $med->catalog_id)
                ->select('diluents.id', 'diluents.name')
                ->get();

            $vias = DB::table('administration_route_medicine_catalog')
                ->join('administration_routes', 'administration_route_medicine_catalog.administration_route_id', '=', 'administration_routes.id')
                ->where('administration_route_medicine_catalog.medicine_catalog_id', $med->catalog_id)
                ->select('administration_routes.id', 'administration_routes.name')
                ->get();

            $infoAdicional[$med->id] = [
                'diluyentes' => $diluyentes,
                'vias' => $vias
            ];
        }

        return view('admin.oncologicos.solicitudes.edit', compact('solicitud', 'medicamentos', 'infoAdicional'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'paciente_nombre' => 'required|string|max:255',
            'servicio' => 'required|string|max:255',
            'registro' => 'required|string|max:255',
            'sexo' => 'required|in:M,F',
            'fecha_nacimiento' => 'required|date',
            'peso' => 'required|numeric|min:1',
            'piso' => 'required|string|max:50',
            'cama' => 'required|string|max:50',
            'diagnostico' => 'required|string|max:255',
            'medico_nombre' => 'required|string|max:255',
            'medico_cedula' => 'required|string|max:255',
            'fecha_entrega' => 'required|date',
            'observaciones' => 'nullable|string|max:500',
            'mezclas' => 'required|string'
        ]);

        $mezclas = json_decode($request->mezclas, true);
        if (!is_array($mezclas)) {
            return back()->withErrors(['mezclas' => 'El formato del campo mezclas no es válido.'])->withInput();
        }

        // Filtrar solo mezclas nuevas (sin la bandera "existente")
        $nuevasMezclas = array_filter($mezclas, function ($m) {
            return empty($m['existente']);
        });

        DB::beginTransaction();
        try {
            $solicitud = SolicitudOnco::findOrFail($id);

            $solicitud->update([
                'servicio' => $request->servicio,
                'nombre_paciente' => $request->paciente_nombre,
                'sexo' => $request->sexo,
                'peso' => $request->peso,
                'cama' => $request->cama,
                'piso' => $request->piso,
                'registro_paciente' => $request->registro,
                'fecha_nacimiento' => $request->fecha_nacimiento,
                'diagnostico' => $request->diagnostico,
                'fecha_entrega' => $request->fecha_entrega,
                'observaciones' => $request->observaciones,
                'nombre_medico' => $request->medico_nombre,
                'cedula_medico' => $request->medico_cedula,
            ]);

            $user = auth()->user();
            $precios = DB::table('medicine_medicine_lists')
                ->where('medicine_list_id', $user->medicine_list_id)
                ->pluck('precio', 'medicine_id');

            $mezclasExistentes = $solicitud->mezclas;

            foreach ($nuevasMezclas as $mezclaData) {
                $coincidencia = $mezclasExistentes->first(function ($mezclaExistente) use ($mezclaData) {
                    $coincidenMedicamentos = $mezclaExistente->medicamentos->pluck('medicamento_id')->sort()->values()->all() ===
                        collect($mezclaData['medicamentos'])->pluck('medicamento_id')->sort()->values()->all();

                    return $mezclaExistente->volumen_dilucion == $mezclaData['volumen_dilucion']
                        && $mezclaExistente->tiempo_infusion == $mezclaData['tiempo_infusion']
                        && $mezclaExistente->medicamentos->count() === count($mezclaData['medicamentos'])
                        && $coincidenMedicamentos;
                });

                if ($coincidencia) {
                    continue; // ya existe mezcla parecida
                }

                $mezcla = Mezcla::create([
                    'solicitud_id' => $solicitud->id,
                    'volumen_dilucion' => $mezclaData['volumen_dilucion'],
                    'tiempo_infusion' => $mezclaData['tiempo_infusion'],
                    'estado' => 'pendiente',
                ]);

                foreach ($mezclaData['medicamentos'] as $medicamento) {
                    $precio = $precios[$medicamento['medicamento_id']] ?? 0;

                    MezclaMedicamento::create([
                        'mezcla_id' => $mezcla->id,
                        'medicamento_id' => $medicamento['medicamento_id'],
                        'nombre_medicamento' => $medicamento['nombre'],
                        'dosis' => $medicamento['dosis'],
                        'diluyente_id' => $medicamento['diluyente_id'],
                        'via_administracion_id' => $medicamento['via_administracion_id'],
                        'precio_unitario' => $precio,
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('admin.oncologicos.mezclas.index', ['mezcla' => $solicitud->id])
                ->with('success', 'Solicitud actualizada correctamente.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }


    # PDF para solicitud de mezcla oncologicas
    public function solicitud(SolicitudOnco $solicitud)
    {
        $solicitud_onco = SolicitudOnco::findOrFail($solicitud->id);
        //return $solicitud_detalles;
        $pdf = Pdf::loadView('pdfs.oncologicos.solicitud', compact('solicitud_onco'));
        return $pdf->stream();
    }


    # PDF para solicitud de mezcla oncologicas
    public function envio(SolicitudOnco $solicitud)
    {
        $solicitud_onco = SolicitudOnco::findOrFail($solicitud->id);
        //return $solicitud_detalles;
        $pdf = Pdf::loadView('pdfs.oncologicos.envio', compact('solicitud_onco'));
        return $pdf->stream();
    }

    public function remision(SolicitudOnco $solicitud)
    {
        $solicitud_onco = SolicitudOnco::findOrFail($solicitud->id);
        //return $solicitud_detalles;
        $pdf = Pdf::loadView('pdfs.oncologicos.remision', compact('solicitud_onco'));
        return $pdf->stream();
    }
}
