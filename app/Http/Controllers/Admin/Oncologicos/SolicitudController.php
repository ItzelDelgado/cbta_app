<?php

namespace App\Http\Controllers\Admin\Oncologicos;

use App\Http\Controllers\Controller;
use App\Models\Oncologicos\Mezcla;
use App\Models\Oncologicos\MezclaMedicamento;
use App\Models\Oncologicos\SolicitudOnco;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
        // Validación general
        $request->validate([
            'paciente_nombre' => 'required|string|max:255',
            'paciente_apellido' => 'required|string|max:255',
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
            // Guardar la solicitud principal
            $solicitud = SolicitudOnco::create([
                'user_id' => auth()->id(),
                'servicio' => $request->servicio,
                'nombre_paciente' => $request->paciente_nombre,
                'sexo' => $request->sexo,
                'edad' => null, // calcula si es necesario
                'peso' => $request->peso,
                'cama' => $request->cama,
                'piso' => $request->piso,
                'registro_paciente' => $request->registro,
                'fecha_nacimiento' => $request->fecha_nacimiento,
                'diagnostico' => $request->diagnostico,
                'fecha_solicitud' => now()->toDateString(),
                'horario_entrega' => \Carbon\Carbon::parse($request->fecha_entrega)->format('H:i:s'),
                'observaciones' => $request->observaciones,
                'nombre_medico' => $request->medico_nombre,
                'cedula_medico' => $request->medico_cedula,
                'estado' => 'pendiente',
                'remision' => null,
            ]);

            // Guardar mezclas y medicamentos
            foreach ($mezclas as $mezclaData) {
                $mezcla = Mezcla::create([
                    'solicitud_id' => $solicitud->id,
                    'volumen_dilucion' => $mezclaData['volumen_dilucion'],
                    'tiempo_infusion' => $mezclaData['tiempo_infusion'],
                    'estado' => 'pendiente',
                ]);

                foreach ($mezclaData['medicamentos'] as $medicamento) {
                    MezclaMedicamento::create([
                        'mezcla_id' => $mezcla->id,
                        'medicamento_id' => $medicamento['medicamento_id'],
                        'nombre_medicamento' => $medicamento['nombre'],
                        'dosis' => $medicamento['dosis'],
                        'precio_unitario' => 0, // puedes calcularlo si tienes precios
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('admin.oncologicos.solicitudes.index')
                            ->with('success', 'Solicitud registrada correctamente.');

        } catch (\Throwable $e) {
            DB::rollBack();

            // 🔍 Mostrar el mensaje real del error (solo para desarrollo)
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }




}
