<?php

namespace App\Http\Controllers\Admin\Oncologicos;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SolicitudController extends Controller
{
    public function index()
    {
        return view('admin.oncologicos.solicitudes.index');
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
        dd($request->all()); // Para depurar, eliminar en producción
        $request->validate([
            'paciente_nombre' => 'required|string',
            'paciente_apellido' => 'required|string',
            'servicio' => 'required|string',
            'registro' => 'required|string',
            'sexo' => 'required|in:M,F',
            'fecha_nacimiento' => 'required|date',
            'peso' => 'required|numeric',
            'piso' => 'required|string',
            'cama' => 'required|string',
            'diagnostico' => 'required|string',
            'medico_nombre' => 'required|string',
            'medico_cedula' => 'required|string',
            'fecha_entrega' => 'required|date',
        ]);

        DB::beginTransaction();

        try {
            $user = Auth::user();
            $edad = \Carbon\Carbon::parse($request->fecha_nacimiento)->age;
            $fecha = \Carbon\Carbon::parse($request->fecha_entrega);

            // 1. Paciente
            $paciente = DB::table('solicitud_patients')->insertGetId([
                'nombre_paciente' => $request->paciente_nombre,
                'apellidos_paciente' => $request->paciente_apellido,
                'servicio' => $request->servicio,
                'cama' => $request->cama,
                'piso' => $request->piso,
                'registro' => $request->registro,
                'diagnostico' => $request->diagnostico,
                'fecha_nacimiento' => $request->fecha_nacimiento,
                'edad' => $edad,
                'peso' => $request->peso,
                'sexo' => $request->sexo === 'M' ? 'Masculino' : 'Femenino',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // 2. Detalle
            $detalle = DB::table('solicitud_details')->insertGetId([
                'via_administracion' => 'Central', // o recibirlo desde el frontend si es dinámico
                'npt' => 'ADULT', // también configurable
                'nombre_medico' => $request->medico_nombre,
                'cedula' => $request->medico_cedula,
                'fecha_hora_entrega' => $request->fecha_entrega,
                'observaciones' => $request->observaciones,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // 3. Solicitud principal
            $solicitudId = DB::table('solicituds')->insertGetId([
                'user_id' => $user->id,
                'solicitud_patient_id' => $paciente,
                'solicitud_detail_id' => $detalle,
                'is_active' => true,
                'is_aprobada' => 'Pendiente',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // 4. Mezclas y medicamentos
            $mezclas = json_decode($request->mezclas, true); // este JSON debe venir del frontend
            foreach ($mezclas as $mezcla) {
                $mezclaId = DB::table('mezclas')->insertGetId([
                    'solicitud_id' => $solicitudId,
                    'volumen_dilucion' => $mezcla['volumen_dilucion'],
                    'tiempo_infusion' => $mezcla['tiempo_infusion'],
                    'created_at' => now(),
                ]);

                foreach ($mezcla['medicamentos'] as $medicamento) {
                    DB::table('mezcla_medicamentos')->insert([
                        'mezcla_id' => $mezclaId,
                        'medicamento_id' => $medicamento['medicamento_id'],
                        'nombre_medicamento' => $medicamento['nombre'],
                        'dosis' => $medicamento['dosis'],
                        'precio_unitario' => $medicamento['precio'],
                        'created_at' => now(),
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('admin.oncologicos.solicitudes.index')->with('success', 'Solicitud guardada exitosamente.');

        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error al guardar la solicitud: ' . $e->getMessage());
        }
    }




}
