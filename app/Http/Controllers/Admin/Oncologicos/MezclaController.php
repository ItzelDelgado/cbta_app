<?php

namespace App\Http\Controllers\Admin\Oncologicos;

use App\Http\Controllers\Controller;
use App\Models\Oncologicos\Mezcla;
use App\Models\Oncologicos\MezclaMedicamento;
use App\Models\Oncologicos\SolicitudOnco;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MezclaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        $solicitud = SolicitudOnco::with(['user.hospital', 'mezclas'])
            ->findOrFail($id);

        return view('admin.oncologicos.mezclas.index', compact('solicitud'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.oncologicos.mezclas.create');
    }

    // Other methods can be added here as needed

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Logic to store the new mix
        // Validate and save the mix data
        // Redirect or return a response
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $mezcla = Mezcla::with(['solicitud', 'medicamentos'])->findOrFail($id);

        $user = Auth::user();
        $listaId = $user->medicine_list_id;

        // Obtener medicamentos del usuario
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

        // Obtener diluyentes y vías por catalog_id
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
                'denominacion' => $med->denominacion,
                'presentacion' => $med->presentacion,
                'diluyentes' => $diluyentes,
                'vias' => $vias
            ];
        }

        return view('admin.oncologicos.mezclas.show', compact('mezcla', 'infoAdicional'));
    }


    public function edit($id)
    {
        $mezcla = Mezcla::with(['medicamentos'])->findOrFail($id);

        $solicitud = $mezcla->solicitud; // para mostrar datos del paciente y solicitud asociada
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


        return view('admin.oncologicos.mezclas.edit', compact('mezcla', 'solicitud', 'medicamentos', 'infoAdicional'));
    }


    public function update(Request $request, $id)
    {
        if ($request->accion === 'preparada') {
            $mezcla = Mezcla::with('solicitud')->findOrFail($id);
            $mezcla->estado = 'preparada';

            // 🔢 Generar número de lote y remisión
            $fechaHoy = Carbon::today();
            $conteoHoy = Mezcla::whereDate('updated_at', $fechaHoy)
                ->whereNotNull('lote') // solo las que ya tienen lote
                ->count();

            $numero = str_pad($conteoHoy + 1, 3, '00', STR_PAD_LEFT);
            $fechaFormateada = $fechaHoy->format('dmy'); // ej. 080725

            $mezcla->lote = 'L' . $fechaFormateada . $numero;
            $mezcla->remision = 'R' . $fechaFormateada . $numero;


            $mezcla->save();

            // ✅ Verificar si la solicitud debe cambiar a "enproceso"
            $solicitud = $mezcla->solicitud;
            if ($solicitud && $solicitud->estado === 'pendiente') {
                $tienePreparadas = $solicitud->mezclas()->where('estado', 'preparada')->exists();
                if ($tienePreparadas) {
                    $solicitud->estado = 'enproceso';
                    $solicitud->save();
                }
            }

            return redirect()
                ->route('admin.oncologicos.mezclas.index', $mezcla->solicitud->id)
                ->with('success', 'Mezcla marcada como preparada con lote y remisión generados.');
        }

        if ($request->accion === 'entregada') {
            $mezcla = Mezcla::with('solicitud')->findOrFail($id);
            $mezcla->estado = 'entregada';
            $mezcla->save();

            // ✅ Verificar si todas las mezclas ya están entregadas para marcar la solicitud como finalizada
            $solicitud = $mezcla->solicitud;

            if ($solicitud) {
                $todasEntregadas = $solicitud->mezclas()->where('estado', '!=', 'entregada')->doesntExist();

                if ($todasEntregadas) {
                    $solicitud->estado = 'finalizada';
                    $solicitud->save();
                }
            }

            return redirect()
                ->route('admin.oncologicos.mezclas.index', $mezcla->solicitud->id)
                ->with('success', 'Mezcla marcada como entregada.');
        }

        $request->validate([
            'mezcla_json' => 'required|json',
            'paciente_nombre' => 'required|string',
            'servicio' => 'required|string',
            'registro' => 'required|string',
            'sexo' => 'nullable|in:M,F',
            'fecha_nacimiento' => 'nullable|date',
            'peso' => 'nullable|numeric',
            'piso' => 'nullable|string',
            'cama' => 'nullable|string',
            'diagnostico' => 'nullable|string',
            'medico_nombre' => 'nullable|string',
            'medico_cedula' => 'nullable|string',
            'fecha_entrega' => 'nullable|date',
            'observaciones' => 'nullable|string',
        ]);

        $mezcla = Mezcla::with('solicitud')->findOrFail($id);
        $mezclaData = json_decode($request->mezcla_json, true);

        $user = auth()->user();

        $precios = DB::table('medicine_medicine_lists')
            ->where('medicine_list_id', $user->medicine_list_id)
            ->pluck('precio', 'medicine_id')
            ->mapWithKeys(fn($valor, $clave) => [(int) $clave => $valor]);

        DB::beginTransaction();

        try {
            // 1. Actualizar mezcla
            $mezcla->volumen_dilucion = $mezclaData['volumen_dilucion'];
            $mezcla->tiempo_infusion = $mezclaData['tiempo_infusion'];
            $mezcla->save();

            // 2. Actualizar solicitud asociada
            $solicitud = $mezcla->solicitud;
            if ($solicitud) {
                $solicitud->nombre_paciente = $request->paciente_nombre;
                $solicitud->servicio = $request->servicio;
                $solicitud->registro_paciente = $request->registro;
                $solicitud->sexo = $request->sexo;
                $solicitud->fecha_nacimiento = $request->fecha_nacimiento;
                $solicitud->peso = $request->peso;
                $solicitud->piso = $request->piso;
                $solicitud->cama = $request->cama;
                $solicitud->diagnostico = $request->diagnostico;
                $solicitud->nombre_medico = $request->medico_nombre;
                $solicitud->cedula_medico = $request->medico_cedula;
                $solicitud->fecha_entrega = $request->fecha_entrega;
                $solicitud->observaciones = $request->observaciones;
                $solicitud->save();
            }

            // 3. Eliminar medicamentos anteriores
            $mezcla->medicamentos()->delete();

            // 4. Insertar medicamentos actualizados
            foreach ($mezclaData['medicamentos'] as $med) {
                $medicamentoId = (int) $med['medicamento_id'];
                $precio = $precios[$medicamentoId] ?? 0;

                $mezcla->medicamentos()->create([
                    'medicamento_id' => $medicamentoId,
                    'nombre_medicamento' => $med['nombre'] ?? '',
                    'dosis' => $med['dosis'],
                    'diluyente_id' => $med['diluyente_id'],
                    'via_administracion_id' => $med['via_administracion_id'],
                    'precio_unitario' => $precio,
                ]);
            }

            // 5. Si viene una acción para aprobar
            if ($request->accion === 'aprobar') {
                $mezcla->estado = 'aprobada';
                $mezcla->save();
            }

            DB::commit();

            return redirect()
                ->route('admin.oncologicos.mezclas.index', $solicitud->id)
                ->with('success', 'Mezcla y solicitud actualizadas correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->withErrors(['error' => 'Error al actualizar la mezcla: ' . $e->getMessage()]);
        }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // Logic to delete a specific mix
        // Find the mix by ID and delete it
        // Redirect or return a response
    }

    public function ordenPreparacion(Mezcla $mezcla)
    {
        // Mezcla y solicitud asociada
        $mezcla = Mezcla::with('solicitud')->findOrFail($mezcla->id);

        // Medicamentos de la mezcla con info del catálogo y lote/caducidad
        $medicamentos = DB::table('mezcla_medicamentos')
            ->join('medicine_oncos', 'mezcla_medicamentos.medicamento_id', '=', 'medicine_oncos.id')
            ->join('medicines_catalog', 'medicine_oncos.catalog_id', '=', 'medicines_catalog.id')
            ->leftJoin('diluents', 'mezcla_medicamentos.diluyente_id', '=', 'diluents.id')
            ->leftJoin('administration_routes', 'mezcla_medicamentos.via_administracion_id', '=', 'administration_routes.id')
            ->where('mezcla_medicamentos.mezcla_id', $mezcla->id)
            ->select(
                'medicine_oncos.lote',
                'medicine_oncos.caducidad',
                'medicines_catalog.denominacion as denominacion_comercial',
                'medicines_catalog.presentacion',
                'mezcla_medicamentos.dosis',
                'mezcla_medicamentos.precio_unitario',
                'mezcla_medicamentos.nombre_medicamento',
                'diluents.name as diluyente',
                'administration_routes.name as via'
            )
            ->get();

        // Fecha de preparación (si la mezcla ya fue aprobada)
        $aprobada = DB::table('solicitud_aprobadas')
            ->where('solicitud_id', $mezcla->solicitud_id)
            ->first();

        // Preparar PDF con la vista y todos los datos
        $pdf = Pdf::loadView('pdfs.oncologicos.orden-de-preparacion', [
            'mezcla' => $mezcla,
            'medicamentos' => $medicamentos,
            'fecha_preparacion' => optional($aprobada)->fecha_hora_preparacion,
            'fecha_limite_uso' => optional($aprobada)->fecha_hora_limite_uso,
        ]);

        // return ([
        //     'mezcla' => $mezcla,
        //     'solicitud' => $mezcla->solicitud,
        //     'medicamentos' => $medicamentos,
        //     'fecha_preparacion' => optional($aprobada)->fecha_hora_preparacion,
        //     'fecha_limite_uso' => optional($aprobada)->fecha_hora_limite_uso,
        // ]);

        return $pdf->stream("orden-preparacion-{$mezcla->id}.pdf");
    }

    public function inspeccion(Mezcla $mezcla)
    {
        // Mezcla y solicitud relacionada
        $mezcla = Mezcla::with('solicitud')->findOrFail($mezcla->id);

        // Buscar inspección asociada
        $inspeccion = DB::table('inspeccion_mezclas')
            ->where('mezcla_id', $mezcla->id)
            ->first();

        // Preparar PDF
        $pdf = Pdf::loadView('pdfs.oncologicos.inspeccion', [
            'mezcla' => $mezcla,
            'solicitud' => $mezcla->solicitud,
            'inspeccion' => $inspeccion,
        ]);

        // return ([
        //     'mezcla' => $mezcla,
        //     'solicitud' => $mezcla->solicitud,
        //     'inspeccion' => $inspeccion
        // ]);

        return $pdf->stream("inspeccion-mezcla-{$mezcla->id}.pdf");
    }

    public function etiqueta(Mezcla $mezcla)
    {
        $mezcla = Mezcla::with('solicitud')->findOrFail($mezcla->id);

        // Buscar si la mezcla tiene aprobación con fechas
        $aprobada = DB::table('solicitud_aprobadas')
            ->where('solicitud_id', $mezcla->solicitud_id)
            ->first();

        $medicamentos = DB::table('mezcla_medicamentos')
            ->join('medicine_oncos', 'mezcla_medicamentos.medicamento_id', '=', 'medicine_oncos.id')
            ->join('medicines_catalog', 'medicine_oncos.catalog_id', '=', 'medicines_catalog.id')
            ->where('mezcla_medicamentos.mezcla_id', $mezcla->id)
            ->select(
                'medicines_catalog.denominacion as nombre',
                'mezcla_medicamentos.dosis'
            )
            ->get();

        $customPaper = [0, 0, 368.50, 255.12]; // 9cm x 13cm
        $pdf = Pdf::loadView('pdfs.oncologicos.etiqueta', [
            'mezcla' => $mezcla,
            'solicitud' => $mezcla->solicitud,
            'aprobada' => $aprobada,
            'medicamentos' => $medicamentos,
        ])->setPaper($customPaper, 'landscape');

        // return ([
        //      'mezcla' => $mezcla,
        //     'solicitud' => $mezcla->solicitud,
        //     'aprobada' => $aprobada,
        //     'medicamentos' => $medicamentos,
        // ]);


        return $pdf->stream();
    }
}
