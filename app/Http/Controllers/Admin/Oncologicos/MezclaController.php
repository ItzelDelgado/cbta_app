<?php

namespace App\Http\Controllers\Admin\Oncologicos;

use App\Http\Controllers\Controller;
use App\Models\Oncologicos\InspeccionMezcla;
use App\Models\Oncologicos\MedicineOnco;
use App\Models\Oncologicos\MedicinePresentation;
use App\Models\Oncologicos\Mezcla;
use App\Models\Oncologicos\MezclaMedicamento;
use App\Models\Oncologicos\SolicitudOnco;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Exception;
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

    public function show($id)
    {
        // Mezcla con solicitud y medicamentos cargados
        $mezcla = Mezcla::with(['solicitud', 'medicamentos'])->findOrFail($id);

        // IDs de medicamentos que participan en ESTA mezcla
        $medIds = $mezcla->medicamentos->pluck('medicamento_id')->unique()->values();

        $infoAdicional = [];

        if ($medIds->isNotEmpty()) {
            // Traer info de medicine_oncos + medicines_catalog SOLO para esos IDs
            $medicamentos = DB::table('medicine_oncos as mo')
                ->join('medicines_catalog as mc', 'mo.catalog_id', '=', 'mc.id')
                ->whereIn('mo.id', $medIds)
                ->select(
                    'mo.id as id',
                    'mc.denominacion',
                    DB::raw('mc.denominacion_comercial as presentacion'),
                    'mc.id as catalog_id'
                )
                ->get();

            foreach ($medicamentos as $med) {
                // Diluyentes asociados al catálogo
                $diluyentes = DB::table('diluent_medicine_catalog')
                    ->join('diluents', 'diluent_medicine_catalog.diluent_id', '=', 'diluents.id')
                    ->where('diluent_medicine_catalog.medicine_catalog_id', $med->catalog_id)
                    ->select(
                        'diluents.id',
                        DB::raw('diluents.denominacion_generica as name')
                    )
                    ->get();

                // Vías asociadas al catálogo
                $vias = DB::table('administration_route_medicine_catalog')
                    ->join('administration_routes', 'administration_route_medicine_catalog.administration_route_id', '=', 'administration_routes.id')
                    ->where('administration_route_medicine_catalog.medicine_catalog_id', $med->catalog_id)
                    ->select('administration_routes.id', 'administration_routes.name')
                    ->get();

                $infoAdicional[$med->id] = [
                    'denominacion' => $med->denominacion,
                    'presentacion' => $med->presentacion, // por si luego quieres mostrarla
                    'diluyentes'   => $diluyentes,
                    'vias'         => $vias,
                ];
            }
        }

        return view('admin.oncologicos.mezclas.show', compact('mezcla', 'infoAdicional'));
    }


    public function edit($id)
    {
        // 👇 IMPORTANTE: cargamos también presentacionesUsadas
        $mezcla = Mezcla::with(['medicamentos.presentacionesUsadas', 'solicitud'])->findOrFail($id);

        $solicitud = $mezcla->solicitud;
        $user      = Auth::user();
        $listaId   = $user->medicine_list_id; // puede ser null

        // Base: mo + mc y LEFT JOIN a la lista (mml)
        $query = DB::table('medicine_oncos as mo')
            ->join('medicines_catalog as mc', 'mo.catalog_id', '=', 'mc.id')
            ->leftJoin('medicine_medicine_lists as mml', function ($join) use ($listaId) {
                $join->on('mml.medicine_id', '=', 'mo.id');
                if ($listaId) {
                    $join->where('mml.medicine_list_id', '=', $listaId);
                }
            })
            ->select(
                'mo.id as id',
                DB::raw('COALESCE(mml.precio, mo.precio) as precio'),
                'mc.denominacion',
                DB::raw('mc.denominacion_comercial AS presentacion'),
                'mc.id as catalog_id',
                'mc.requires_infusor as requires_infusor'
            );

        if ($listaId) {
            $query->where('mml.medicine_list_id', $listaId);
        }

        // Medicamentos de la lista
        $medicamentos = $query->get()->map(function ($m) {
            $m->requires_infusor = (int) ($m->requires_infusor ?? 0);
            return $m;
        });

        // Completar con medicamentos que ya tiene la mezcla
        $idsCatalogo = $medicamentos->pluck('id')->all();

        foreach ($mezcla->medicamentos as $mm) {
            if (!in_array($mm->medicamento_id, $idsCatalogo, true)) {
                $extra = DB::table('medicine_oncos as mo')
                    ->join('medicines_catalog as mc', 'mo.catalog_id', '=', 'mc.id')
                    ->where('mo.id', $mm->medicamento_id)
                    ->select(
                        'mo.id as id',
                        DB::raw('mo.precio as precio'),
                        'mc.denominacion',
                        DB::raw('mc.denominacion_comercial AS presentacion'),
                        'mc.id as catalog_id',
                        'mc.requires_infusor as requires_infusor'
                    )
                    ->first();

                if ($extra) {
                    $extra->requires_infusor = (int) ($extra->requires_infusor ?? 0);
                    $medicamentos->push($extra);
                    $idsCatalogo[] = $extra->id;
                }
            }
        }

        // Info adicional (diluyentes y vías)
        $infoAdicional = [];
        foreach ($medicamentos as $med) {
            $diluyentes = DB::table('diluent_medicine_catalog')
                ->join('diluents', 'diluent_medicine_catalog.diluent_id', '=', 'diluents.id')
                ->where('diluent_medicine_catalog.medicine_catalog_id', $med->catalog_id)
                ->select(
                    'diluents.id',
                    DB::raw('diluents.denominacion_generica as name')
                )
                ->get();

            $vias = DB::table('administration_route_medicine_catalog')
                ->join('administration_routes', 'administration_route_medicine_catalog.administration_route_id', '=', 'administration_routes.id')
                ->where('administration_route_medicine_catalog.medicine_catalog_id', $med->catalog_id)
                ->select('administration_routes.id', 'administration_routes.name')
                ->get();

            $infoAdicional[$med->id] = [
                'denominacion'     => $med->denominacion,
                'presentacion'     => $med->presentacion,
                'precio'           => $med->precio,
                'catalog_id'       => $med->catalog_id, // para el front
                'diluyentes'       => $diluyentes,
                'vias'             => $vias,
                'requires_infusor' => (int) $med->requires_infusor,
            ];
        }

        // Presentaciones por catálogo
        $catalogIds = $medicamentos->pluck('catalog_id')->unique()->values();

        $presentacionesPorCatalogo = MedicinePresentation::whereIn('catalog_id', $catalogIds)
            ->where('is_available', 1)
            ->with(['batches' => function ($q) {
                $q->where('is_current', true);
            }])
            ->get()
            ->groupBy('catalog_id')
            ->map(function ($group) {
                return $group->map(function ($p) {
                    $batch = $p->batches->first();
                    return [
                        'id'                   => $p->id,
                        'presentacion'         => $p->presentacion,
                        'cantidad_medicamento' => $p->cantidad_medicamento,
                        'volumen_diluyente'    => $p->volumen_diluyente,
                        'precio_frasco'        => $p->precio_frasco,
                        'lote'                 => $batch->lote ?? null,
                        'caducidad'            => $batch->caducidad ?? null,
                        'batch_id'             => $batch->id ?? null,
                    ];
                })->values();
            });

        // Infusores activos
        $infusors = DB::table('infusors')
            ->select('id', 'nombre_generico', 'nombre_comercial')
            ->where('is_active', true)
            ->orderBy('nombre_generico')
            ->orderBy('nombre_comercial')
            ->get();

        return view('admin.oncologicos.mezclas.edit', [
            'mezcla'                    => $mezcla,
            'solicitud'                 => $solicitud,
            'medicamentos'              => $medicamentos,
            'infoAdicional'             => $infoAdicional,
            'infusors'                  => $infusors,
            'presentacionesPorCatalogo' => $presentacionesPorCatalogo,
        ]);
    }



    public function update(Request $request, $id)
    {

        // ... (helpers y bloques 'preparada' / 'entregada' se quedan igual que ya los tienes) ...

        // ---------- Edición normal ----------
        $request->validate([
            'mezcla_json'      => 'required|json',
            'paciente_nombre'  => 'required|string',
            'servicio'         => 'required|string',
            'registro'         => 'required|string',
            'sexo'             => 'nullable|in:M,F',
            'fecha_nacimiento' => 'nullable|date',
            'peso'             => 'nullable|numeric',
            'piso'             => 'nullable|string',
            'cama'             => 'nullable|string',
            'diagnostico'      => 'nullable|string',
            'medico_nombre'    => 'nullable|string',
            'medico_cedula'    => 'nullable|string',
            'fecha_entrega'    => 'nullable|date',
            'observaciones'    => 'nullable|string',
        ]);

        $mezcla = Mezcla::with('solicitud')->findOrFail($id);
        $mezclaData = json_decode($request->mezcla_json, true);

        DB::beginTransaction();
        try {
            // 1) Mezcla: volumen, tiempo, set/infusor
            $volumenDilucion = (float) ($mezclaData['volumen_dilucion'] ?? 0);
            $tiempoInfusion  = $mezclaData['tiempo_infusion'] ?? null;

            if ($volumenDilucion <= 0) {
                throw new \Exception("La mezcla requiere un volumen de dilución válido (> 0).");
            }
            if ($tiempoInfusion === null || $tiempoInfusion === '') {
                throw new \Exception("La mezcla requiere un tiempo de infusión.");
            }

            $setInfusion = !empty($mezclaData['set_infusion']);
            $infusorId   = !empty($mezclaData['infusor_id']) ? (int)$mezclaData['infusor_id'] : null;

            if ($setInfusion && $infusorId) {
                throw new \Exception("Selecciona set de infusión o un infusor, no ambos.");
            }

            $medsPayload = $mezclaData['medicamentos'] ?? [];
            if (!is_array($medsPayload) || count($medsPayload) === 0) {
                throw new \Exception("La mezcla debe contener al menos un medicamento.");
            }

            // Validar mismo diluyente y misma vía
            $refDil = $medsPayload[0]['diluyente_id'] ?? null;
            $refVia = $medsPayload[0]['via_administracion_id'] ?? null;
            foreach ($medsPayload as $m) {
                if (($m['diluyente_id'] ?? null) !== $refDil ||
                    ($m['via_administracion_id'] ?? null) !== $refVia
                ) {
                    throw new \Exception("Todos los medicamentos deben tener el mismo diluyente y la misma vía de administración.");
                }
            }

            // Validar infusor
            if ($infusorId) {
                $infusor = DB::table('infusors')->where('id', $infusorId)->where('is_active', true)->first();
                if (!$infusor) {
                    throw new \Exception("El infusor seleccionado no existe o no está activo.");
                }

                $hayMedQueAdmiteInfusor = false;
                foreach ($medsPayload as $m) {
                    $mo = DB::table('medicine_oncos as mo')
                        ->join('medicines_catalog as mc', 'mo.catalog_id', '=', 'mc.id')
                        ->where('mo.id', (int)$m['medicamento_id'])
                        ->select('mc.requires_infusor')
                        ->first();
                    if ($mo && (int)$mo->requires_infusor === 1) {
                        $hayMedQueAdmiteInfusor = true;
                        break;
                    }
                }
                if (!$hayMedQueAdmiteInfusor) {
                    throw new \Exception("Para seleccionar un infusor, la mezcla debe contener al menos un medicamento que lo admita.");
                }
            }

            $mezcla->volumen_dilucion = $volumenDilucion;
            $mezcla->tiempo_infusion  = $tiempoInfusion;
            $mezcla->set_infusion     = $setInfusion;
            $mezcla->infusor_id       = $infusorId;
            $mezcla->save();

            // 2) Solicitud asociada
            if ($mezcla->solicitud) {
                $mezcla->solicitud->nombre_paciente   = $request->paciente_nombre;
                $mezcla->solicitud->servicio          = $request->servicio;
                $mezcla->solicitud->registro_paciente = $request->registro;
                $mezcla->solicitud->sexo              = $request->sexo;
                $mezcla->solicitud->fecha_nacimiento  = $request->fecha_nacimiento;
                $mezcla->solicitud->peso              = $request->peso;
                $mezcla->solicitud->piso              = $request->piso;
                $mezcla->solicitud->cama              = $request->cama;
                $mezcla->solicitud->diagnostico       = $request->diagnostico;
                $mezcla->solicitud->nombre_medico     = $request->medico_nombre;
                $mezcla->solicitud->cedula_medico     = $request->medico_cedula;
                $mezcla->solicitud->fecha_entrega     = $request->fecha_entrega;
                $mezcla->solicitud->observaciones     = $request->observaciones;
                $mezcla->solicitud->save();
            }

            // 3) Medicamentos: reconstruimos la relación
            //    (esto también borra mezcla_medicamento_presentaciones por el ON DELETE CASCADE)
            $mezcla->medicamentos()->delete();

            foreach ($medsPayload as $m) {
                $medicamentoId = (int) $m['medicamento_id'];
                $nombre        = $m['nombre'] ?? '';
                $diluyenteId   = $m['diluyente_id'] ?? null;
                $viaAdminId    = $m['via_administracion_id'] ?? null;
                $dosis         = (float) ($m['dosis'] ?? 0);

                $medicine = MedicineOnco::with('catalog')->find($medicamentoId);
                if (!$medicine || !$medicine->catalog) {
                    throw new \Exception("No se encontró información del catálogo para el medicamento ID {$medicamentoId}.");
                }
                $catalog = $medicine->catalog;

                $concentracion = $volumenDilucion > 0 ? $dosis / $volumenDilucion : 0;
                if ($concentracion < (float)$catalog->conc_min || $concentracion > (float)$catalog->conc_max) {
                    throw new \Exception(
                        "La concentración de '{$catalog->denominacion}' está fuera del rango permitido ({$catalog->conc_min} - {$catalog->conc_max}). " .
                            "Dosis: {$dosis}, Volumen: {$volumenDilucion}."
                    );
                }

                $dosisML = null;
                if ((float)$catalog->cantidad_medicamento > 0) {
                    $dosisML = ($dosis * (float)$catalog->volumen_diluyente) / (float)$catalog->cantidad_medicamento;
                    $dosisML = round($dosisML, 2);
                }

                // Creamos el registro de mezcla_medicamentos y guardamos la instancia
                $mm = $mezcla->medicamentos()->create([
                    'medicamento_id'        => $medicamentoId,
                    'nombre_medicamento'    => $nombre,
                    'dosis'                 => $dosis,
                    'dosis_ml'              => $dosisML,
                    'diluyente_id'          => $diluyenteId,
                    'via_administracion_id' => $viaAdminId,
                    // charge_by / precio_mg_snapshot si en algún momento los quieres llenar
                ]);

                // 3.b) Guardar las presentaciones usadas (si vienen en el JSON)
                $presentaciones = $m['presentaciones'] ?? [];
                foreach ($presentaciones as $pres) {
                    $batchId  = isset($pres['batch_id']) ? (int)$pres['batch_id'] : null;
                    $unidades = isset($pres['frascos']) ? (int)$pres['frascos'] : 0;

                    if (!$batchId || $unidades <= 0) {
                        continue;
                    }

                    $batch = DB::table('medicine_batches as mb')
                        ->join('medicine_presentations as mp', 'mb.medicine_presentation_id', '=', 'mp.id')
                        ->where('mb.id', $batchId)
                        ->select(
                            'mb.id',
                            'mb.lote',
                            'mb.caducidad',
                            'mp.precio_frasco'
                        )
                        ->first();

                    if (!$batch) {
                        throw new \Exception("No se encontró el lote (batch) con ID {$batchId}.");
                    }

                    $precioFrasco = $batch->precio_frasco ?? 0;
                    $subtotal     = $precioFrasco ? $precioFrasco * $unidades : null;

                    DB::table('mezcla_medicamento_presentaciones')->insert([
                        'mezcla_medicamento_id' => $mm->id,
                        'medicine_batch_id'     => $batch->id,
                        'unidades_usadas'       => $unidades,
                        'lote_usado'            => $batch->lote,
                        'caducidad_usada'       => $batch->caducidad,
                        'precio_frasco_snapshot' => $precioFrasco,
                        'subtotal'              => $subtotal,
                        'created_at'            => now(),
                        'updated_at'            => now(),
                    ]);
                }
            }

            // 4) si la acción es aprobar
            if ($request->accion === 'aprobar') {
                $mezcla->estado = 'aprobada';
                $mezcla->save();

                $user = auth()->user();
                $preparoNombre = $user?->name
                    ?? ($user?->nombre ?? null)
                    ?? $user?->email
                    ?? 'Usuario';

                $inspeccion = InspeccionMezcla::firstOrCreate(
                    ['mezcla_id' => $mezcla->id],
                    [
                        'fecha_inspeccion' => Carbon::today()->toDateString(),
                        'hora_inspeccion'  => Carbon::now()->format('H:i:s'),
                        'reviso_nombre'    => '',
                        'aprobo_nombre'    => '',
                    ]
                );

                $inspeccion->preparo_nombre = $preparoNombre;
                $inspeccion->save();
            }

            DB::commit();

            return redirect()
                ->route('admin.oncologicos.mezclas.index', $mezcla->solicitud->id)
                ->with(
                    'success',
                    $request->accion === 'aprobar'
                        ? 'Mezcla aprobada correctamente.'
                        : 'Mezcla y solicitud actualizadas correctamente.'
                );
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al actualizar la mezcla: ' . $e->getMessage()]);
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
        // Mezcla + solicitud + hospital + inspección
        $mezcla = Mezcla::with(['solicitud.user.hospital', 'inspeccion'])
            ->findOrFail($mezcla->id);

        // Medicamentos (ordenados por nombre genérico)
        $medicamentos = DB::table('mezcla_medicamentos as mm')
            ->join('medicine_oncos as mo', 'mm.medicamento_id', '=', 'mo.id')
            ->join('medicines_catalog as mc', 'mo.catalog_id', '=', 'mc.id')
            ->leftJoin('diluents as d', 'mm.diluyente_id', '=', 'd.id')
            ->leftJoin('administration_routes as ar', 'mm.via_administracion_id', '=', 'ar.id')
            ->where('mm.mezcla_id', $mezcla->id)
            ->select(
                'mc.lote',
                'mc.caducidad',
                'mc.denominacion',              // genérica
                'mc.denominacion_comercial',
                'mc.presentacion',
                'mm.dosis',
                'mm.dosis_ml',                  // volumen del medicamento en mL
                'mm.precio_unitario',
                'mm.nombre_medicamento',
                'd.denominacion_generica as diluyente',
                'ar.name as via'
            )
            ->orderBy('mc.denominacion')
            ->get()
            ->map(function ($m) use ($mezcla) {
                $m->volumen_total = $mezcla->volumen_dilucion;
                return $m;
            });

        // Fechas preparación/límite
        $aprobada = DB::table('solicitud_aprobadas')
            ->where('solicitud_id', $mezcla->solicitud_id)
            ->first();

        $fechaPreparacion = optional($aprobada)->fecha_hora_preparacion
            ? Carbon::parse($aprobada->fecha_hora_preparacion)
            : Carbon::parse($mezcla->created_at);

        $fechaLimiteUso = optional($aprobada)->fecha_hora_limite_uso
            ? Carbon::parse($aprobada->fecha_hora_limite_uso)
            : $fechaPreparacion->copy()->addHours(48);

        // Hospital (del usuario dueño de la solicitud)
        $hospital = optional($mezcla->solicitud->user->hospital)->name ?? 'No asignado';

        // Nombres desde inspección (si existen)
        $aproboNombre  = optional($mezcla->inspeccion)->aprobo_nombre;
        $revisoNombre  = optional($mezcla->inspeccion)->reviso_nombre;
        $preparoNombre = optional($mezcla->inspeccion)->preparo_nombre;
        $liberoNombre  = optional($mezcla->inspeccion)->libero_nombre;

        $pdf = Pdf::loadView('pdfs.oncologicos.orden-de-preparacion', [
            'mezcla'            => $mezcla,
            'medicamentos'      => $medicamentos,
            'fecha_preparacion' => $fechaPreparacion,
            'fecha_limite_uso'  => $fechaLimiteUso,
            'hospital'          => $hospital,
            'aprobo_nombre'     => $aproboNombre,
            'reviso_nombre'     => $revisoNombre,
            'preparo_nombre'    => $preparoNombre,   // ⬅️ agregado
            'libero_nombre'     => $liberoNombre,    // ⬅️ agregado
        ])->setPaper('letter', 'portrait');

        return $pdf->stream("orden-preparacion-{$mezcla->id}.pdf");
    }



    public function inspeccion(Mezcla $mezcla)
    {
        // Carga mezcla + solicitud + inspección (inspección llega con booleans reales por los casts)
        $mezcla = Mezcla::with(['solicitud', 'inspeccion'])->findOrFail($mezcla->id);

        $pdf = Pdf::loadView('pdfs.oncologicos.inspeccion', [
            'mezcla'      => $mezcla,
            'solicitud'   => $mezcla->solicitud,
            'inspeccion'  => $mezcla->inspeccion, // <- ya es el modelo, no Query Builder
        ])->setPaper('letter', 'portrait');

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
