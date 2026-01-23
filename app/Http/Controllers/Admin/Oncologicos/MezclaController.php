<?php

namespace App\Http\Controllers\Admin\Oncologicos;

use App\Http\Controllers\Controller;
use App\Models\Oncologicos\DiluentPresentation;
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
        // Mezcla + medicamentos + presentaciones usadas + solicitud
        $mezcla = Mezcla::with([
            'medicamentos.presentacionesUsadas',
            'solicitud',
        ])->findOrFail($id);

        $solicitud = $mezcla->solicitud;
        $user      = Auth::user();

        // ⚠️ Lista asignada al usuario
        $listaId = $user->medicine_list_id; // puede ser null

        // ==============================
        // 1) OBTENER PRESENTACIONES DE LA LISTA
        // ==============================
        $presentacionesLista      = collect();
        $catalogIdsDesdeLista     = collect();

        if ($listaId) {
            // Presentaciones ligadas a la lista del usuario
            $presentacionesLista = MedicinePresentation::query()
                ->whereHas('lists', function ($q) use ($listaId) {
                    $q->where('medicine_list_id', $listaId);
                })
                ->with(['batches' => function ($q) {
                    $q->where('is_current', true);
                }])
                ->get();

            // Catálogos (genéricos) que sí están en la lista
            $catalogIdsDesdeLista = $presentacionesLista->pluck('catalog_id')->unique()->values();
        }

        // Si el usuario NO tiene lista, fallback a TODOS los catálogos
        if ($catalogIdsDesdeLista->isEmpty()) {
            $catalogIdsDesdeLista = DB::table('medicines_catalog')->pluck('id');
        }

        // ==============================
        // 2) MEDICAMENTOS (GENÉRICOS) PERMITIDOS
        // ==============================
        $query = DB::table('medicine_oncos as mo')
            ->join('medicines_catalog as mc', 'mo.catalog_id', '=', 'mc.id')
            ->select(
                'mo.id as id',
                DB::raw('mo.precio as precio'),
                'mc.denominacion',
                DB::raw('mc.denominacion_comercial AS presentacion'),
                'mc.id as catalog_id',
                'mc.requires_infusor as requires_infusor'
            )
            ->whereIn('mc.id', $catalogIdsDesdeLista);

        $medicamentos = $query->get()->map(function ($m) {
            $m->requires_infusor = (int) ($m->requires_infusor ?? 0);
            return $m;
        });

        // ==============================
        // 3) COMPLETAR CON MEDS YA USADOS EN LA MEZCLA
        // ==============================
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

        // ==============================
        // 4) INFO ADICIONAL (diluyentes, vías, etc.)
        // ==============================
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
                ->join(
                    'administration_routes',
                    'administration_route_medicine_catalog.administration_route_id',
                    '=',
                    'administration_routes.id'
                )
                ->where('administration_route_medicine_catalog.medicine_catalog_id', $med->catalog_id)
                ->select('administration_routes.id', 'administration_routes.name')
                ->get();

            $infoAdicional[$med->id] = [
                'denominacion'     => $med->denominacion,
                'presentacion'     => $med->presentacion,
                'precio'           => $med->precio,
                'catalog_id'       => $med->catalog_id,
                'diluyentes'       => $diluyentes,
                'vias'             => $vias,
                'requires_infusor' => (int) $med->requires_infusor,
            ];
        }

        // ==============================
        // 5) PRESENTACIONES DE MEDICAMENTOS POR CATÁLOGO
        // ==============================
        if ($listaId && $presentacionesLista->isNotEmpty()) {
            $presentacionesPorCatalogo = $presentacionesLista
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
        } else {
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
        }

        // ==============================
        // 6) PRESENTACIONES DE DILUYENTES
        // ==============================
        $diluentPresentationsPorDiluyente = DiluentPresentation::query()
            ->where('is_active', true)
            ->orderBy('volume_ml')
            ->get()
            ->groupBy('diluent_id')
            ->map(function ($group) {
                return $group->map(function ($p) {
                    return [
                        'id'                     => $p->id,
                        'diluent_id'             => $p->diluent_id,
                        'presentacion'           => $p->presentacion,
                        'volume_ml'              => $p->volume_ml,
                        'denominacion_comercial' => $p->denominacion_comercial,
                        'lote'                   => $p->lote,
                        'caducidad'              => $p->caducidad,
                    ];
                })->values();
            });

        // ==============================
        // 7) INFUSORES
        // ==============================
        $infusors = DB::table('infusors')
            ->select('id', 'nombre_generico', 'nombre_comercial')
            ->where('is_active', true)
            ->orderBy('nombre_generico')
            ->orderBy('nombre_comercial')
            ->get();

        return view('admin.oncologicos.mezclas.edit', [
            'mezcla'                           => $mezcla,
            'solicitud'                        => $solicitud,
            'medicamentos'                     => $medicamentos,
            'infoAdicional'                    => $infoAdicional,
            'infusors'                         => $infusors,
            'presentacionesPorCatalogo'        => $presentacionesPorCatalogo,
            'diluentPresentationsPorDiluyente' => $diluentPresentationsPorDiluyente,
        ]);
    }


    public function update(Request $request, $id)
    {
        // ---------- Helpers internos ----------
        $generarLotePorMezcla = function (Mezcla $mezcla) {
            if ($mezcla->lote) return; // no sobreescribir si ya existe

            $hoy = Carbon::today();

            // Consecutivo diario para LOTES: cuenta mezclas del día con lote ya asignado
            $conteoHoy = Mezcla::whereDate('created_at', $hoy)
                ->whereNotNull('lote')
                ->lockForUpdate()
                ->count();

            $consecutivo = str_pad($conteoHoy + 1, 3, '0', STR_PAD_LEFT);

            // Mes abreviado (ES) sin depender del locale
            $abbr = ['ENE', 'FEB', 'MAR', 'ABR', 'MAY', 'JUN', 'JUL', 'AGO', 'SEP', 'OCT', 'NOV', 'DIC'];
            $mesAbbr = $abbr[$hoy->month - 1];

            $dia  = $hoy->format('d');
            $anio = $hoy->format('y');

            // Lote por mezcla: L+DD+MES+YY+###
            $mezcla->lote = 'L' . $dia . $mesAbbr . $anio . $consecutivo;
        };

        $asegurarRemisionPorSolicitud = function (SolicitudOnco $solicitud) {
            if ($solicitud->remision) return $solicitud->remision; // reutiliza

            $hoy = Carbon::today();

            // Consecutivo diario para REMISIONES: cuenta solicitudes del día con remisión ya asignada
            $conteoHoy = SolicitudOnco::whereDate('created_at', $hoy)
                ->whereNotNull('remision')
                ->lockForUpdate()
                ->count();

            $consecutivo = str_pad($conteoHoy + 1, 3, '0', STR_PAD_LEFT);

            $abbr = ['ENE', 'FEB', 'MAR', 'ABR', 'MAY', 'JUN', 'JUL', 'AGO', 'SEP', 'OCT', 'NOV', 'DIC'];
            $mesAbbr = $abbr[$hoy->month - 1];

            $dia  = $hoy->format('d');
            $anio = $hoy->format('y');

            // Remisión por solicitud: R+DD+MES+YY+###
            $solicitud->remision = 'R' . $dia . $mesAbbr . $anio . $consecutivo;
            $solicitud->save();

            return $solicitud->remision;
        };

        // ---------- Acciones rápidas ----------
        if ($request->accion === 'preparada') {
            $mezcla = Mezcla::with('solicitud')->findOrFail($id);
            $user   = auth()->user();

            // Obtén un nombre legible del usuario autenticado
            $preparoNombre = $user?->name
                ?? ($user?->nombre ?? null)
                ?? $user?->email
                ?? 'Usuario';

            DB::transaction(function () use ($mezcla, $generarLotePorMezcla, $asegurarRemisionPorSolicitud, $preparoNombre) {
                $mezcla->estado = 'preparada';

                // 1) LOTE por mezcla
                $generarLotePorMezcla($mezcla);

                // 2) REMISIÓN por solicitud (todas las mezclas comparten)
                if ($mezcla->solicitud) {
                    $remision = $asegurarRemisionPorSolicitud($mezcla->solicitud);
                    $mezcla->remision = $remision;

                    // Poner solicitud en "enproceso" si al menos una mezcla ya fue preparada
                    if ($mezcla->solicitud->estado === 'pendiente') {
                        if ($mezcla->solicitud->mezclas()->where('estado', 'preparada')->exists()) {
                            $mezcla->solicitud->estado = 'enproceso';
                            $mezcla->solicitud->save();
                        }
                    }
                }

                $mezcla->save();
            });

            return redirect()
                ->route('admin.oncologicos.mezclas.index', $mezcla->solicitud->id)
                ->with('success', 'Mezcla marcada como preparada. Se registró el responsable en la inspección.');
        }

        if ($request->accion === 'entregada') {
            $mezcla = Mezcla::with('solicitud')->findOrFail($id);

            // Nombre del usuario activo que libera
            $user = auth()->user();
            $liberoNombre = $user?->name
                ?? ($user?->nombre ?? null)
                ?? $user?->email
                ?? 'Usuario';

            DB::transaction(function () use ($mezcla, $liberoNombre) {
                $mezcla->estado = 'entregada';
                $mezcla->save();

                // Asegurar que exista 1 sola inspección por mezcla y actualizar libero_nombre
                $inspeccion = InspeccionMezcla::firstOrCreate(
                    ['mezcla_id' => $mezcla->id],
                    [
                        // mínimos para cumplir NOT NULL si nunca se creó antes
                        'fecha_inspeccion' => now()->toDateString(),
                        'hora_inspeccion'  => now()->format('H:i:s'),
                        'reviso_nombre'    => '',
                        'aprobo_nombre'    => '',
                    ]
                );

                // Guardar quién liberó
                $inspeccion->libero_nombre = $liberoNombre;
                $inspeccion->save();

                // Si todas las mezclas están entregadas, finaliza la solicitud
                if ($mezcla->solicitud) {
                    $todasEntregadas = $mezcla->solicitud->mezclas()
                        ->where('estado', '!=', 'entregada')
                        ->doesntExist();

                    if ($todasEntregadas) {
                        $mezcla->solicitud->estado = 'finalizada';
                        $mezcla->solicitud->save();
                    }
                }
            });

            return redirect()
                ->route('admin.oncologicos.mezclas.index', $mezcla->solicitud->id)
                ->with('success', 'Mezcla marcada como entregada y liberador registrado.');
        }


        $request->validate([
            'mezcla_json'      => 'required|json',

            'paciente_nombre'  => 'required|string|max:255',
            'servicio'         => 'required|string|max:255',
            'registro'         => 'required|string|max:255',

            'sexo'             => 'nullable|in:M,F',

            // 👇 MISMA REGLA DE EDAD (PERO NULLABLE)
            'fecha_nacimiento' => [
                'nullable',
                'date',
                'after:' . Carbon::now()->subYears(100)->format('Y-m-d'),
                'before:' . Carbon::today()->format('Y-m-d'),
            ],

            'peso'             => 'nullable|numeric|min:1|max:500',
            'piso'             => 'nullable|string|max:50',
            'cama'             => 'nullable|string|max:50',
            'diagnostico'      => 'nullable|string|max:255',
            'medico_nombre'    => 'nullable|string|max:255',
            'medico_cedula'    => 'nullable|string|max:255',

            // 👇 Entrega válida solo si viene
            'fecha_entrega'    => 'nullable|date|after_or_equal:today',

            'observaciones'    => 'nullable|string|max:500',
        ], [
            // ✨ Mensajes claros (solo disparan si el campo viene)
            'fecha_nacimiento.after'  => 'La fecha de nacimiento no puede ser mayor a 100 años.',
            'fecha_nacimiento.before' => 'La fecha de nacimiento debe ser anterior a hoy.',
            'fecha_entrega.after_or_equal' => 'La fecha de entrega no puede ser anterior a hoy.',
        ]);

        $mezcla     = Mezcla::with('solicitud.user')->findOrFail($id);
        $mezclaData = json_decode($request->mezcla_json, true);

        // Lista que define los precios (del dueño de la solicitud)
        $listaId = optional(optional($mezcla->solicitud)->user)->medicine_list_id;

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

            // 🔹 Nueva parte: presentación de diluyente
            $diluentPresentationId = $mezclaData['diluent_presentation_id'] ?? null;
            if ($diluentPresentationId) {
                $dilPres = DiluentPresentation::where('id', (int)$diluentPresentationId)
                    ->where('is_active', true)
                    ->first();

                if (!$dilPres) {
                    throw new \Exception("La presentación de diluyente seleccionada no existe o no está activa.");
                }

                // Si hay diluyente fijo en la mezcla, asegura que coincidan
                if ($refDil && (int)$dilPres->diluent_id !== (int)$refDil) {
                    throw new \Exception("La presentación de diluyente no pertenece al diluyente seleccionado en la mezcla.");
                }
            }

            // Validar infusor
            if ($infusorId) {
                $infusor = DB::table('infusors')
                    ->where('id', $infusorId)
                    ->where('is_active', true)
                    ->first();

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

            $mezcla->volumen_dilucion        = $volumenDilucion;
            $mezcla->tiempo_infusion         = $tiempoInfusion;
            $mezcla->set_infusion            = $setInfusion;
            $mezcla->infusor_id              = $infusorId;
            $mezcla->diluent_presentation_id = $diluentPresentationId ?: null; // 🔹 se guarda aquí
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

                $mm = $mezcla->medicamentos()->create([
                    'medicamento_id'        => $medicamentoId,
                    'nombre_medicamento'    => $nombre,
                    'dosis'                 => $dosis,
                    'dosis_ml'              => $dosisML,
                    'diluyente_id'          => $diluyenteId,
                    'via_administracion_id' => $viaAdminId,
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
                        ->leftJoin('medicine_list_presentation as mlp', function ($join) use ($listaId) {
                            $join->on('mlp.medicine_presentation_id', '=', 'mp.id');
                            if ($listaId) {
                                $join->where('mlp.medicine_list_id', '=', $listaId);
                            }
                        })
                        ->where('mb.id', $batchId)
                        ->select(
                            'mb.id',
                            'mb.lote',
                            'mb.caducidad',
                            'mp.precio_frasco as precio_frasco_base',
                            'mlp.precio as precio_lista'
                        )
                        ->first();

                    if (!$batch) {
                        throw new \Exception("No se encontró el lote (batch) con ID {$batchId}.");
                    }

                    $precioFrasco = $batch->precio_lista ?? $batch->precio_frasco_base ?? 0;
                    $subtotal     = $precioFrasco ? $precioFrasco * $unidades : null;

                    DB::table('mezcla_medicamento_presentaciones')->insert([
                        'mezcla_medicamento_id'  => $mm->id,
                        'medicine_batch_id'      => $batch->id,
                        'unidades_usadas'        => $unidades,
                        'lote_usado'             => $batch->lote,
                        'caducidad_usada'        => $batch->caducidad,
                        'precio_frasco_snapshot' => $precioFrasco,
                        'subtotal'               => $subtotal,
                        'created_at'             => now(),
                        'updated_at'             => now(),
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
        $mezcla = Mezcla::with([
            'solicitud.user.hospital',
            'inspeccion',
            'medicamentos.medicamentoOnco.catalog',
            'medicamentos.diluyente',
            'medicamentos.viaAdministracion',
            'medicamentos.presentacionesUsadas.batch.presentation',
            'diluentPresentation.diluent', // ✅ para nombre real del diluyente
            'infusor',
        ])->findOrFail($mezcla->id);

        // ===== FECHAS =====
        $aprobada = DB::table('solicitud_aprobadas')
            ->where('solicitud_id', $mezcla->solicitud_id)
            ->first();

        $fechaPreparacion = optional($aprobada)->fecha_hora_preparacion
            ? Carbon::parse($aprobada->fecha_hora_preparacion)
            : Carbon::parse($mezcla->created_at);

        $fechaLimiteUso = optional($aprobada)->fecha_hora_limite_uso
            ? Carbon::parse($aprobada->fecha_hora_limite_uso)
            : $fechaPreparacion->copy()->addHours(48);

        $hospital = optional($mezcla->solicitud->user->hospital)->name ?? 'No asignado';

        // ===== EQUIPO INFUSIÓN/INFUSOR =====
        $esSetInfusion = (bool) $mezcla->set_infusion;

        if ($esSetInfusion) {
            $equipoInfusion = (object) [
                'tipo'             => 'set',
                'lote'             => null,
                'caducidad'        => null,
                'nombre_comercial' => 'Set de infusión',
                'nombre_generico'  => 'Set de infusión',
            ];
        } else {
            $inf = $mezcla->infusor;
            $equipoInfusion = (object) [
                'tipo'             => 'infusor',
                'lote'             => optional($inf)->lote,
                'caducidad'        => optional($inf)->caducidad,
                'nombre_comercial' => optional($inf)->nombre_comercial,
                'nombre_generico'  => optional($inf)->nombre_generico,
            ];
        }

        // ===== NOMBRES INSPECCIÓN =====
        $aproboNombre  = optional($mezcla->inspeccion)->aprobo_nombre;
        $revisoNombre  = optional($mezcla->inspeccion)->reviso_nombre;
        $preparoNombre = optional($mezcla->inspeccion)->preparo_nombre;
        $liberoNombre  = optional($mezcla->inspeccion)->libero_nombre;

        // ===== DILUYENTE BASE =====
        $diluyenteBase = optional(optional($mezcla->diluentPresentation)->diluent)->denominacion_generica
            ?? '—';

        // ==========================================================
        // ✅ CÁLCULOS:
        // Volumen orden de preparación (mL):
        //   dosis(mg) * volumen_presentacion(ml) / mg_presentacion(mg)
        //
        // Volumen diluyente (mL):
        //   volumen_total_solicitado - volumen_medicamento
        // ==========================================================

        $volumenTotalSolicitado = (float) ($mezcla->volumen_dilucion ?? 0);

        $legendProteccion = null;
        $totalVolMedicamentos = 0.0;

        // ===== MEDICAMENTOS (para tablas) =====
        $medicamentos = $mezcla->medicamentos->map(function ($mm) use ($mezcla, $volumenTotalSolicitado, &$legendProteccion, &$totalVolMedicamentos) {

            $presentaciones = $mm->presentacionesUsadas ?? collect();
            $firstPresUsed  = $presentaciones->first();

            $batch     = optional($firstPresUsed)->batch;
            $presModel = optional($firstPresUsed)->presentation;
            $catalog   = optional(optional($mm->medicamentoOnco)->catalog);

            $lote      = optional($firstPresUsed)->lote_usado ?? ($batch->lote ?? null);
            $caducidad = optional($firstPresUsed)->caducidad_usada ?? ($batch->caducidad ?? null);

            // ✅ Leyenda (primera que exista)
            if (!$legendProteccion) {
                $candLegend = trim((string) optional($presModel)->legend);
                if ($candLegend !== '') $legendProteccion = $candLegend;
            }

            // Nombre para usar también en texto "Agregar"
            $nombreMed = $mm->nombre_medicamento
                ?? ($catalog->denominacion ?? null)
                ?? 'Medicamento';

            // ====== Volumen de orden de preparación (mL) ======
            // Preferimos dosis_ml si ya viene calculado, pero si no, aplicamos la fórmula.
            $volOrdenPrep = null;

            if (!is_null($mm->dosis_ml) && is_numeric($mm->dosis_ml) && (float)$mm->dosis_ml > 0) {
                $volOrdenPrep = (float) $mm->dosis_ml;
            } else {
                $dosisMg     = (float) ($mm->dosis ?? 0);
                $mgPres      = (float) (optional($presModel)->cantidad_medicamento ?? 0); // mg de la presentación
                $volPresMl   = (float) (optional($presModel)->volumen_diluyente ?? 0);    // mL de la presentación

                if ($dosisMg > 0 && $mgPres > 0 && $volPresMl > 0) {
                    $volOrdenPrep = ($dosisMg * $volPresMl) / $mgPres;
                }
            }

            if (is_null($volOrdenPrep) || $volOrdenPrep <= 0) {
                $volOrdenPrep = 0.0;
            }

            // ====== Volumen del diluyente (mL) ======
            $volDiluyente = $volumenTotalSolicitado - $volOrdenPrep;
            if ($volDiluyente < 0) $volDiluyente = 0.0;

            $totalVolMedicamentos += $volOrdenPrep;

            return (object) [
                'lote'                   => $lote,
                'caducidad'              => $caducidad,
                'denominacion_comercial' => $catalog->denominacion_comercial ?? null,
                'denominacion'           => $catalog->denominacion ?? null,
                'presentacion'           => optional($presModel)->presentacion ?? null,
                'dosis'                  => $mm->dosis, // mg
                // ✅ nuevos campos para tu PDF:
                'volumen_orden_preparacion' => $volOrdenPrep,   // mL
                'volumen_diluyente'         => $volDiluyente,   // mL
                'volumen_total'             => $mezcla->volumen_dilucion, // mL
                // para texto
                'nombre_para_texto'         => $nombreMed,
            ];
        })->values();

        if (!$legendProteccion) $legendProteccion = '—';

        // ===== Texto final de "Extraer / Agregar" sin repetir el diluyente por medicamento =====
        // Extraer = suma de volúmenes de medicamento
        $extraerTotal = (float) $totalVolMedicamentos;

        // Diluyente restante = volumen total - suma medicamentos
        $diluyenteRestante = $volumenTotalSolicitado - $extraerTotal;
        if ($diluyenteRestante < 0) $diluyenteRestante = 0.0;

        $fmt = function ($n) {
            return rtrim(rtrim(number_format((float)$n, 2, '.', ''), '0'), '.');
        };

        $extraerTotalFmt = $fmt($extraerTotal);
        $diluyenteRestanteFmt = $fmt($diluyenteRestante);

        // Armamos UNA sola línea "Agregar: ..."
        $partesMed = $medicamentos
            ->filter(fn($m) => (float)($m->volumen_orden_preparacion ?? 0) > 0)
            ->map(function ($m) use ($fmt) {
                $v = $fmt($m->volumen_orden_preparacion ?? 0);
                $nom = $m->nombre_para_texto ?? 'Medicamento';
                return "{$v} mL de {$nom}";
            })
            ->values()
            ->all();

        $detalleAgregar = [];
        if (count($partesMed) > 0) {
            $detalleAgregar[] = implode(' + ', $partesMed) . " a {$diluyenteRestanteFmt} mL de {$diluyenteBase}";
        }

        $dosisTotalMg = $mezcla->medicamentos
            ->sum(fn($m) => (float) ($m->dosis ?? 0));

        $volumenFinalMl = (float) ($mezcla->volumen_dilucion ?? 0);

        $concentracionFinal = null;

        if ($dosisTotalMg > 0 && $volumenFinalMl > 0) {
            $concentracionFinal = $dosisTotalMg / $volumenFinalMl;
        }

        // Formato bonito
        $concentracionFinalFmt = $concentracionFinal !== null
            ? rtrim(rtrim(number_format($concentracionFinal, 4, '.', ''), '0'), '.')
            : '—';

        $pdf = Pdf::loadView('pdfs.oncologicos.orden-de-preparacion', [
            'mezcla'            => $mezcla,
            'medicamentos'      => $medicamentos,
            'fecha_preparacion' => $fechaPreparacion,
            'fecha_limite_uso'  => $fechaLimiteUso,
            'hospital'          => $hospital,
            'aprobo_nombre'     => $aproboNombre,
            'reviso_nombre'     => $revisoNombre,
            'preparo_nombre'    => $preparoNombre,
            'libero_nombre'     => $liberoNombre,
            'equipoInfusion'    => $equipoInfusion,
            'concentracion_final' => $concentracionFinalFmt,

            // ✅ Para sección "Cálculos y forma de preparación"
            'diluyente_base'    => $diluyenteBase,
            'extraer_ml'        => $extraerTotalFmt,
            'detalle_agregar'   => $detalleAgregar,
            'legend_proteccion' => $legendProteccion,
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
        $mezcla = Mezcla::with([
            'solicitud',
            'diluentPresentation.diluent',
            'medicamentos.presentacionesUsadas.batch.presentation',
            'medicamentos.medicamentoOnco.catalog',
        ])->findOrFail($mezcla->id);

        $aprobada = DB::table('solicitud_aprobadas')
            ->where('solicitud_id', $mezcla->solicitud_id)
            ->first();

        // =========================================
        // 0) Observaciones (para etiqueta)
        // =========================================
        $observaciones = $mezcla->solicitud->observaciones ?? null;

        // =========================================
        // 1) Medicamentos: nombre + dosis + lote + caducidad (de la presentación usada)
        // =========================================
        $medicamentosTabla = $mezcla->medicamentos->map(function ($m) {

            $nombre = optional(optional($m->medicamentoOnco)->catalog)->denominacion
                ?? $m->nombre_medicamento
                ?? '—';

            $presentaciones = $m->presentacionesUsadas ?? collect();
            $firstUsed = $presentaciones->first();

            $batch = optional($firstUsed)->batch;

            $lote = optional($firstUsed)->lote_usado
                ?? optional($batch)->lote
                ?? null;

            $cad = optional($firstUsed)->caducidad_usada
                ?? optional($batch)->caducidad
                ?? null;

            return (object) [
                'nombre' => $nombre,
                'dosis'  => $m->dosis ?? 0,
                'lote'   => $lote,
                'cad'    => $cad,
            ];
        });

        // =========================================
        // 2) Medicamento más restrictivo (menor stability_hours)
        // =========================================
        $chosenLegend = null;
        $chosenTempMin = null;
        $chosenTempMax = null;
        $chosenStabilityHours = null;

        foreach ($mezcla->medicamentos as $m) {

            $presentaciones = $m->presentacionesUsadas ?? collect();
            $firstUsed = $presentaciones->first();

            $presentation = optional(optional($firstUsed)->batch)->presentation
                ?? optional($firstUsed)->presentation
                ?? null;

            if (!$presentation) continue;

            $stability = isset($presentation->stability_hours) ? (int) $presentation->stability_hours : null;
            if (!$stability || $stability <= 0) continue;

            if ($chosenStabilityHours === null || $stability < $chosenStabilityHours) {
                $chosenStabilityHours = $stability;
                $chosenLegend = $presentation->legend ?? null;
                $chosenTempMin = $presentation->temp_min_c ?? null;
                $chosenTempMax = $presentation->temp_max_c ?? null;
            }
        }

        // =========================================
        // 3) Fechas preparación / límite
        // =========================================
        if (!empty($aprobada?->fecha_hora_preparacion)) {
            $fechaPreparacion = \Carbon\Carbon::parse($aprobada->fecha_hora_preparacion);
        } else {
            $fechaPreparacion = $mezcla->updated_at
                ? \Carbon\Carbon::parse($mezcla->updated_at)
                : null;
        }

        $fechaLimiteUso = null;
        if ($fechaPreparacion && $chosenStabilityHours) {
            $fechaLimiteUso = $fechaPreparacion->copy()->addHours($chosenStabilityHours);
        }

        // =========================================
        // 4) Texto del diluyente + lote/cad (si existen en tu tabla)
        // =========================================
        $diluyenteTexto = '—';
        $diluyenteLote = null;
        $diluyenteCad  = null;

        if ($mezcla->diluentPresentation) {
            $dp = $mezcla->diluentPresentation;

            $nombreDil = optional($dp->diluent)->denominacion_generica;
            $volumen   = $dp->volume_ml ?? null;
            $present   = $dp->presentacion ?? null;

            if ($nombreDil && $volumen) {
                $volFmt = rtrim(rtrim(number_format((float)$volumen, 2, '.', ''), '0'), '.');
                $diluyenteTexto = $nombreDil . ' de ' . $volFmt . ' ml';
            } elseif ($present) {
                $diluyenteTexto = $present;
            } elseif ($nombreDil) {
                $diluyenteTexto = $nombreDil;
            }

            // ⚠️ Estos campos dependen de tu BD.
            // Si tu diluentPresentation NO tiene lote/caducidad, quedarán null y en la vista verás "—".
            $diluyenteLote = $dp->lote ?? null;
            $diluyenteCad  = $dp->caducidad ?? null;
        }

        // ✅ PDF (9cm x 13cm)
        $customPaper = [0, 0, 368.50, 255.12];

        $pdf = Pdf::loadView('pdfs.oncologicos.etiqueta', [
            'mezcla'            => $mezcla,
            'solicitud'         => $mezcla->solicitud,
            'aprobada'          => $aprobada,

            // tabla meds (incluye lote/cad)
            'medicamentos'      => $medicamentosTabla,

            // diluyente + lote/cad
            'diluyenteTexto'    => $diluyenteTexto,
            'diluyenteLote'     => $diluyenteLote,
            'diluyenteCad'      => $diluyenteCad,

            // observaciones
            'observaciones'     => $observaciones,

            // fechas + leyenda + condiciones
            'fechaPreparacion'  => $fechaPreparacion,
            'fechaLimiteUso'    => $fechaLimiteUso,
            'legendEtiqueta'    => $chosenLegend,
            'tempMinEtiqueta'   => $chosenTempMin,
            'tempMaxEtiqueta'   => $chosenTempMax,
            'stabilityEtiqueta' => $chosenStabilityHours,
        ])->setPaper($customPaper, 'landscape');

        return $pdf->stream();
    }
}
