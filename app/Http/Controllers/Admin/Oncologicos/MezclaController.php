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

    public function index($id)
    {
        $solicitud = SolicitudOnco::with([
            'hospital',
            'mezclas' => fn($q) => $q->orderBy('id'),
        ])->findOrFail($id);

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
        $mezcla = Mezcla::with([
            'solicitud',
            'medicamentos',
            // si quieres tener a la mano presentaciones usadas:
            'medicamentos.presentacionesUsadas.batch.presentation',
        ])->findOrFail($id);

        // En tu mezcla_medicamentos.medicamento_id guardas medicine_oncos.id
        $medIds = $mezcla->medicamentos->pluck('medicamento_id')->filter()->unique()->values();

        // Para diluyentes/vías (catálogo vivo) necesitamos catalog_id.
        // OJO: aunque mostremos snapshots, para traer las listas permitidas del catálogo
        // ocupamos el catalog_id actual del medicine_oncos.
        $catalogIdPorMedicineOnco = collect();

        if ($medIds->isNotEmpty()) {
            $catalogIdPorMedicineOnco = DB::table('medicine_oncos as mo')
                ->whereIn('mo.id', $medIds)
                ->pluck('mo.catalog_id', 'mo.id'); // [medicine_oncos.id => catalog_id]
        }

        $infoAdicional = [];

        foreach ($mezcla->medicamentos as $mm) {

            $medicineOncoId = $mm->medicamento_id; // medicine_oncos.id o null
            $catalogId = $medicineOncoId ? (int) ($catalogIdPorMedicineOnco->get($medicineOncoId) ?? 0) : 0;

            // ✅ Nombre mostrado: snapshot primero
            $denomSnap = trim((string) ($mm->denominacion_snapshot ?? ''));
            $marcaSnap = trim((string) ($mm->marca_snapshot ?? ''));

            // fallback si aún no hay snapshots en registros viejos
            $fallbackNombre = trim((string) ($mm->nombre_medicamento ?? 'Medicamento'));

            $nombreFinal = '—';
            if ($denomSnap !== '') {
                $nombreFinal = $marcaSnap !== '' ? "{$denomSnap} ({$marcaSnap})" : $denomSnap;
            } elseif ($fallbackNombre !== '') {
                $nombreFinal = $fallbackNombre;
            }

            // ✅ Presentación: snapshot primero (de la primera presentación usada)
            // (si hay varias, muestra la primera; luego si quieres te hago variante para listar todas)
            $presentacionSnap = null;

            $firstUsed = ($mm->presentacionesUsadas ?? collect())->first();
            if ($firstUsed) {
                $presentacionSnap = trim((string) ($firstUsed->presentacion_snapshot ?? ''));
            }

            // fallback viejo: sacar presentacion desde joins (por si no hay snapshot todavía)
            if (!$presentacionSnap) {
                $presentacionSnap = DB::table('mezcla_medicamentos as mm2')
                    ->join('mezcla_medicamento_presentaciones as mmp', 'mmp.mezcla_medicamento_id', '=', 'mm2.id')
                    ->join('medicine_batches as mb', 'mb.id', '=', 'mmp.medicine_batch_id')
                    ->join('medicine_presentations as mp', 'mp.id', '=', 'mb.medicine_presentation_id')
                    ->where('mm2.id', $mm->id)
                    ->orderBy('mmp.id')
                    ->value('mp.presentacion');
                $presentacionSnap = $presentacionSnap ? trim((string) $presentacionSnap) : null;
            }

            // ✅ Diluyentes/vías permitidas (catálogo vivo, solo para mostrar opciones/UI)
            // Si un día quieres 100% inmutable, aquí también haríamos snapshot de texto.
            $diluyentes = collect();
            $vias = collect();

            if ($catalogId > 0) {
                $diluyentes = DB::table('diluent_medicine_catalog as dmc')
                    ->join('diluents as d', 'dmc.diluent_id', '=', 'd.id')
                    ->where('dmc.medicine_catalog_id', $catalogId)
                    ->select('d.id', DB::raw('d.denominacion_generica as name'))
                    ->get();

                $vias = DB::table('administration_route_medicine_catalog as armc')
                    ->join('administration_routes as ar', 'armc.administration_route_id', '=', 'ar.id')
                    ->where('armc.medicine_catalog_id', $catalogId)
                    ->select('ar.id', 'ar.name')
                    ->get();
            }

            // ✅ Valores “técnicos” snapshot
            $requiresInfusor = (int) ($mm->requires_infusor_snapshot ?? 0);

            $concMin = $mm->conc_min_snapshot;
            $concMax = $mm->conc_max_snapshot;

            $infoAdicional[$mm->id] = [
                // esto lo usa tu vista como “denominacion”
                'denominacion' => $nombreFinal,

                // esto lo usa tu vista como “presentacion”
                'presentacion' => $presentacionSnap ?: '—',

                // listas (vivas)
                'diluyentes'   => $diluyentes,
                'vias'         => $vias,

                // snapshots técnicos
                'requires_infusor' => $requiresInfusor,
                'conc_min'          => $concMin,
                'conc_max'          => $concMax,
            ];
        }

        return view('admin.oncologicos.mezclas.show', compact('mezcla', 'infoAdicional'));
    }


    public function edit($id)
    {
        $user = Auth::user();

        // ✅ Cargar mezcla completa
        $mezcla = Mezcla::with([
            'solicitud.hospital',
            'infusor',
            'diluentPresentation.diluent',

            'medicamentos.medicamentoOnco.catalog',
            'medicamentos.diluyente',
            'medicamentos.viaAdministracion',
            'medicamentos.presentacionesUsadas.batch.presentation',
        ])->findOrFail($id);

        $solicitud = $mezcla->solicitud;

        // =====================================================
        // ✅ Seguridad por hospital
        // =====================================================
        if (
            $user->hospital_id &&
            $solicitud?->hospital_id &&
            (int) $user->hospital_id !== (int) $solicitud->hospital_id
        ) {
            abort(403, 'No autorizado para editar mezclas de otro hospital.');
        }

        $listaId = $user->medicine_list_id; // puede ser null

        // =====================================================
        // 1) CATÁLOGOS activos (medicines_catalog)
        // =====================================================
        $catalogos = DB::table('medicines_catalog as mc')
            ->where('mc.state', true)
            ->select('mc.id', 'mc.denominacion', 'mc.requires_infusor')
            ->orderBy('mc.denominacion')
            ->get()
            ->map(function ($m) {
                $m->requires_infusor = (int) ($m->requires_infusor ?? 0);
                return $m;
            });

        // =====================================================
        // 2) PRESENTACIONES POR CATÁLOGO (✅ con BATCHES)
        //    ✅ lote/caducidad vienen de medicine_batches
        // =====================================================
        $basePresentacionesQuery = DB::table('medicine_presentations as mp')
            ->join('medicine_batches as mb', 'mb.medicine_presentation_id', '=', 'mp.id')
            ->where('mp.is_available', 1)
            ->select(
                // mp
                'mp.id',
                'mp.catalog_id',
                'mp.presentacion',
                'mp.marca',
                'mp.cantidad_medicamento',
                'mp.volumen_diluyente',
                'mp.precio_frasco',

                // mb (lo que tu JS necesita)
                DB::raw('mb.id as batch_id'),
                DB::raw('mb.lote as lote'),
                DB::raw('mb.caducidad as caducidad')
            )
            ->orderBy('mp.presentacion')
            ->orderBy('mb.caducidad');

        if ($listaId) {
            $presentaciones = (clone $basePresentacionesQuery)
                ->join('medicine_list_presentation as mlp', 'mlp.medicine_presentation_id', '=', 'mp.id')
                ->where('mlp.medicine_list_id', $listaId)
                ->addSelect(
                    'mlp.charge_by',
                    'mlp.precio',
                    'mlp.precio_mg_override'
                )
                ->get();

            $presentacionesPorCatalogo = $presentaciones
                ->groupBy('catalog_id')
                ->map(fn($rows) => $rows->values());
        } else {
            $presentaciones = (clone $basePresentacionesQuery)->get();

            $presentacionesPorCatalogo = $presentaciones
                ->groupBy('catalog_id')
                ->map(fn($rows) => $rows->values());
        }

        // =====================================================
        // 3) INFO ADICIONAL por catalog_id (diluyentes/vías/requires_infusor)
        //    ✅ Incluye catalog_id porque tu JS lo usa
        // =====================================================
        $infoAdicional = [];

        foreach ($catalogos as $cat) {
            $diluyentes = DB::table('diluent_medicine_catalog')
                ->join('diluents', 'diluent_medicine_catalog.diluent_id', '=', 'diluents.id')
                ->where('diluent_medicine_catalog.medicine_catalog_id', $cat->id)
                ->select('diluents.id', DB::raw('diluents.denominacion_generica as name'))
                ->get();

            $vias = DB::table('administration_route_medicine_catalog')
                ->join(
                    'administration_routes',
                    'administration_route_medicine_catalog.administration_route_id',
                    '=',
                    'administration_routes.id'
                )
                ->where('administration_route_medicine_catalog.medicine_catalog_id', $cat->id)
                ->select('administration_routes.id', 'administration_routes.name')
                ->get();

            $infoAdicional[$cat->id] = [
                'catalog_id'        => $cat->id, // ✅ IMPORTANTE para tu JS
                'denominacion'      => $cat->denominacion,
                'diluyentes'        => $diluyentes,
                'vias'              => $vias,
                'requires_infusor'  => (int) $cat->requires_infusor,
            ];
        }

        // =====================================================
        // 4) GARANTIZAR que el edit conserve lo que ya trae la mezcla
        //    - catálogos inactivos o fuera de lista
        //    - presentaciones con batches para esos catálogos
        // =====================================================
        $catalogIdsEnMezcla = $mezcla->medicamentos
            ->map(fn($mm) => optional(optional($mm->medicamentoOnco)->catalog)->id)
            ->filter()
            ->unique()
            ->values();

        foreach ($catalogIdsEnMezcla as $catId) {

            // 4.1) Si el catálogo no está en infoAdicional, lo agregamos
            if (!array_key_exists($catId, $infoAdicional)) {
                $cat = DB::table('medicines_catalog')
                    ->where('id', $catId)
                    ->select('id', 'denominacion', 'requires_infusor')
                    ->first();

                if ($cat) {
                    $diluyentes = DB::table('diluent_medicine_catalog')
                        ->join('diluents', 'diluent_medicine_catalog.diluent_id', '=', 'diluents.id')
                        ->where('diluent_medicine_catalog.medicine_catalog_id', $cat->id)
                        ->select('diluents.id', DB::raw('diluents.denominacion_generica as name'))
                        ->get();

                    $vias = DB::table('administration_route_medicine_catalog')
                        ->join(
                            'administration_routes',
                            'administration_route_medicine_catalog.administration_route_id',
                            '=',
                            'administration_routes.id'
                        )
                        ->where('administration_route_medicine_catalog.medicine_catalog_id', $cat->id)
                        ->select('administration_routes.id', 'administration_routes.name')
                        ->get();

                    $infoAdicional[$cat->id] = [
                        'catalog_id'        => $cat->id,
                        'denominacion'      => $cat->denominacion,
                        'diluyentes'        => $diluyentes,
                        'vias'              => $vias,
                        'requires_infusor'  => (int) ($cat->requires_infusor ?? 0),
                    ];
                }
            }

            // 4.2) Si no venían presentaciones para ese catálogo, anexamos (✅ con batches)
            if (!$presentacionesPorCatalogo->has($catId)) {

                $extras = DB::table('medicine_presentations as mp')
                    ->join('medicine_batches as mb', 'mb.medicine_presentation_id', '=', 'mp.id')
                    ->where('mp.catalog_id', $catId)
                    ->where('mp.is_available', 1)
                    ->select(
                        'mp.id',
                        'mp.catalog_id',
                        'mp.presentacion',
                        'mp.marca',
                        'mp.cantidad_medicamento',
                        'mp.volumen_diluyente',
                        'mp.precio_frasco',
                        DB::raw('mb.id as batch_id'),
                        DB::raw('mb.lote as lote'),
                        DB::raw('mb.caducidad as caducidad')
                    )
                    ->orderBy('mp.presentacion')
                    ->orderBy('mb.caducidad')
                    ->get();

                if ($extras->isNotEmpty()) {
                    $presentacionesPorCatalogo = $presentacionesPorCatalogo->put($catId, $extras->values());
                }
            }
        }

        // =====================================================
        // 5) PRESENTACIONES DE DILUYENTES
        // =====================================================
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

        // =====================================================
        // 6) INFUSORES
        // =====================================================
        $infusors = DB::table('infusors')
            ->select('id', 'nombre_generico', 'nombre_comercial')
            ->where('is_active', true)
            ->orderBy('nombre_generico')
            ->orderBy('nombre_comercial')
            ->get();

        return view('admin.oncologicos.mezclas.edit', [
            'mezcla'                           => $mezcla,
            'solicitud'                        => $solicitud,

            // ✅ para tu JS (const medicamentos = @json($medicamentos);)
            'medicamentos'                     => $catalogos,

            'catalogos'                        => $catalogos,
            'infoAdicional'                    => $infoAdicional,
            'presentacionesPorCatalogo'        => $presentacionesPorCatalogo,
            'infusors'                         => $infusors,
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
                }

                $mezcla->save();

                // ✅ Actualizar solicitud a "enproceso"
                if ($mezcla->solicitud && $mezcla->solicitud->estado === 'pendiente') {
                    $mezcla->solicitud->estado = 'enproceso';
                    $mezcla->solicitud->save();
                }

                // ✅ 3) Asegurar inspección y guardar quién preparó
                $inspeccion = InspeccionMezcla::firstOrCreate(
                    ['mezcla_id' => $mezcla->id],
                    [
                        'fecha_inspeccion' => now()->toDateString(),
                        'hora_inspeccion'  => now()->format('H:i:s'),
                        'reviso_nombre'    => '',
                        'aprobo_nombre'    => '',
                    ]
                );

                if ($inspeccion->preparo_nombre === '' || $inspeccion->preparo_nombre === null) {
                    $inspeccion->preparo_nombre = $preparoNombre;
                    $inspeccion->save();
                }
            });

            return redirect()
                ->route('admin.oncologicos.mezclas.index', $mezcla->solicitud->id)
                ->with('success', 'Mezcla marcada como preparada. Se registró el responsable en la inspección.');
        }

        if ($request->accion === 'entregada') {
            $mezcla = Mezcla::with('solicitud')->findOrFail($id);

            $user = auth()->user();
            $liberoNombre = $user?->name
                ?? ($user?->nombre ?? null)
                ?? $user?->email
                ?? 'Usuario';

            DB::transaction(function () use ($mezcla, $liberoNombre) {
                $mezcla->estado = 'entregada';
                $mezcla->save();

                $inspeccion = InspeccionMezcla::firstOrCreate(
                    ['mezcla_id' => $mezcla->id],
                    [
                        'fecha_inspeccion' => now()->toDateString(),
                        'hora_inspeccion'  => now()->format('H:i:s'),
                        'reviso_nombre'    => '',
                        'aprobo_nombre'    => '',
                    ]
                );

                $inspeccion->libero_nombre = $liberoNombre;
                $inspeccion->save();

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

        // ---------- Validación ----------
        $request->validate([
            'mezcla_json'      => 'required|json',

            'paciente_nombre'  => 'required|string|max:255',
            'servicio'         => 'required|string|max:255',
            'registro'         => 'required|string|max:255',

            'sexo'             => 'nullable|in:M,F',

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

            'fecha_entrega'    => 'nullable|date|after_or_equal:today',

            'observaciones'    => 'nullable|string|max:500',
        ], [
            'fecha_nacimiento.after'  => 'La fecha de nacimiento no puede ser mayor a 100 años.',
            'fecha_nacimiento.before' => 'La fecha de nacimiento debe ser anterior a hoy.',
            'fecha_entrega.after_or_equal' => 'La fecha de entrega no puede ser anterior a hoy.',
        ]);

        $mezcla     = Mezcla::with(['solicitud.user', 'medicamentos'])->findOrFail($id);
        $mezclaData = json_decode($request->mezcla_json, true);

        // Lista que define precios (dueño de la solicitud)
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
            $refDil = isset($medsPayload[0]['diluyente_id']) ? (string)$medsPayload[0]['diluyente_id'] : null;
            $refVia = isset($medsPayload[0]['via_administracion_id']) ? (string)$medsPayload[0]['via_administracion_id'] : null;

            foreach ($medsPayload as $m) {
                $d = isset($m['diluyente_id']) ? (string)$m['diluyente_id'] : null;
                $v = isset($m['via_administracion_id']) ? (string)$m['via_administracion_id'] : null;

                if ($d !== $refDil || $v !== $refVia) {
                    throw new \Exception("Todos los medicamentos deben tener el mismo diluyente y la misma vía de administración.");
                }
            }

            // Presentación de diluyente
            $diluentPresentationId = $mezclaData['diluent_presentation_id'] ?? null;
            if ($diluentPresentationId) {
                $dilPres = DiluentPresentation::where('id', (int)$diluentPresentationId)
                    ->where('is_active', true)
                    ->first();

                if (!$dilPres) {
                    throw new \Exception("La presentación de diluyente seleccionada no existe o no está activa.");
                }

                if ($refDil && (int)$dilPres->diluent_id !== (int)$refDil) {
                    throw new \Exception("La presentación de diluyente no pertenece al diluyente seleccionado en la mezcla.");
                }
            }

            // Validar infusor (si aplica)
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
            $mezcla->diluent_presentation_id = $diluentPresentationId ?: null;
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

            // 3) Medicamentos: borrar seguro (presentaciones + medicamentos)
            $mmIds = $mezcla->medicamentos()->pluck('id');
            if ($mmIds->isNotEmpty()) {
                DB::table('mezcla_medicamento_presentaciones')
                    ->whereIn('mezcla_medicamento_id', $mmIds)
                    ->delete();
            }
            $mezcla->medicamentos()->delete();

            // 4) Re-crear medicamentos + presentaciones + snapshots
            foreach ($medsPayload as $m) {
                $medicamentoId = (int) ($m['medicamento_id'] ?? 0);
                $nombre        = $m['nombre'] ?? '';
                $diluyenteId   = !empty($m['diluyente_id']) ? (int)$m['diluyente_id'] : null;
                $viaAdminId    = !empty($m['via_administracion_id']) ? (int)$m['via_administracion_id'] : null;
                $dosis         = (float) ($m['dosis'] ?? 0);

                if ($medicamentoId <= 0) {
                    throw new \Exception("Medicamento inválido en la mezcla (medicamento_id vacío).");
                }
                if ($dosis <= 0) {
                    throw new \Exception("La dosis debe ser mayor a 0 para el medicamento ID {$medicamentoId}.");
                }

                $medicine = MedicineOnco::with('catalog')->find($medicamentoId);
                if (!$medicine || !$medicine->catalog) {
                    throw new \Exception("No se encontró información del catálogo para el medicamento ID {$medicamentoId}.");
                }

                $catalog = $medicine->catalog;

                // Validación concentración (mg / mL)
                $concentracion = $volumenDilucion > 0 ? $dosis / $volumenDilucion : 0;

                $concMin = (float)($catalog->conc_min ?? 0);
                $concMax = (float)($catalog->conc_max ?? 0);

                if (($concMin > 0 || $concMax > 0) && ($concentracion < $concMin || $concentracion > $concMax)) {
                    throw new \Exception(
                        "La concentración de '{$catalog->denominacion}' está fuera del rango permitido ({$concMin} - {$concMax}). " .
                            "Dosis: {$dosis}, Volumen: {$volumenDilucion}."
                    );
                }

                // charge_by: prioridad payload -> default tabla
                $chargeBy = $m['charge_by'] ?? ($catalog->charge_by ?? 'mg');
                $chargeBy = strtolower(trim((string)$chargeBy));
                if (!in_array($chargeBy, ['mg', 'frasco'], true)) {
                    $chargeBy = 'mg';
                }

                // ✅ Crear mezcla_medicamentos + snapshots
                $mm = $mezcla->medicamentos()->create([
                    'medicamento_id'             => $medicamentoId,
                    'nombre_medicamento'         => $nombre,

                    'denominacion_snapshot'      => $catalog->denominacion ?? null,
                    'marca_snapshot'             => null,
                    'requires_infusor_snapshot'  => (bool)($catalog->requires_infusor ?? false),
                    'conc_min_snapshot'          => $catalog->conc_min ?? null,
                    'conc_max_snapshot'          => $catalog->conc_max ?? null,

                    'dosis'                      => $dosis,
                    'dosis_ml'                   => null,

                    'diluyente_id'               => $diluyenteId,
                    'via_administracion_id'      => $viaAdminId,

                    'charge_by'                  => $chargeBy,
                    'precio_mg_snapshot'         => null,
                ]);

                $presentaciones = $m['presentaciones'] ?? [];
                $totalMlAportado = 0.0;
                $marcaElegida = null;

                foreach ($presentaciones as $pres) {
                    $batchId  = isset($pres['batch_id']) ? (int)$pres['batch_id'] : null;
                    $unidades = isset($pres['frascos']) ? (int)$pres['frascos'] : 0;

                    if (!$batchId || $unidades <= 0) continue;

                    // ✅ batch + presentation + precio (lista/base) + snapshots
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

                            // ✅ snapshots (desde mp)
                            'mp.presentacion',
                            'mp.cantidad_medicamento',
                            'mp.volumen_diluyente',
                            'mp.legend',

                            // extras
                            'mp.marca',
                            'mp.volumen_diluyente as vol_ml',
                            'mp.precio_frasco as precio_frasco_base',
                            'mlp.precio as precio_lista'
                        )
                        ->first();

                    if (!$batch) {
                        throw new \Exception("No se encontró el lote (batch) con ID {$batchId}.");
                    }

                    // Marca snapshot (si existe)
                    if (!$marcaElegida && !empty($batch->marca)) {
                        $marcaElegida = $batch->marca;
                    }

                    $precioFrasco = (float)($batch->precio_lista ?? $batch->precio_frasco_base ?? 0);
                    $subtotal     = $precioFrasco * $unidades;

                    DB::table('mezcla_medicamento_presentaciones')->insert([
                        'mezcla_medicamento_id'        => $mm->id,
                        'medicine_batch_id'            => $batch->id,
                        'unidades_usadas'              => $unidades,

                        // ✅ snapshots faltantes
                        'presentacion_snapshot'        => $batch->presentacion ?? null,
                        'cantidad_medicamento_snapshot' => $batch->cantidad_medicamento ?? null,
                        'volumen_diluyente_snapshot'   => $batch->volumen_diluyente ?? null,
                        'legend_snapshot'              => $batch->legend ?? null,

                        'lote_usado'                   => $batch->lote,
                        'caducidad_usada'              => $batch->caducidad,
                        'precio_frasco_snapshot'       => $precioFrasco,
                        'subtotal'                     => $subtotal,
                        'created_at'                   => now(),
                        'updated_at'                   => now(),
                    ]);

                    $volMl = (float)($batch->vol_ml ?? 0);
                    if ($volMl > 0) {
                        $totalMlAportado += ($volMl * $unidades);
                    }
                }

                if ($marcaElegida) {
                    $mm->marca_snapshot = $marcaElegida;
                }

                if ($totalMlAportado > 0) {
                    $mm->dosis_ml = round($totalMlAportado, 2);
                }

                // ✅ Si cobro por mg -> guardo precio_mg_snapshot
                if ($chargeBy === 'mg') {
                    $precioMg = (float)($medicine->precio_mg ?? 0);
                    if ($precioMg <= 0) $precioMg = 0;
                    $mm->precio_mg_snapshot = $precioMg;
                }

                $mm->save();
            }

            // 5) Si la acción es aprobar
            if ($request->accion === 'aprobar') {
                $mezcla->estado = 'aprobada';
                $mezcla->save();

                $user = auth()->user();
                $aproboNombre = $user?->name
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

                $inspeccion->aprobo_nombre = $aproboNombre;
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
            'solicitud.hospital',
            'inspeccion',
            'medicamentos.medicamentoOnco.catalog',
            'medicamentos.diluyente',
            'medicamentos.viaAdministracion',
            'medicamentos.presentacionesUsadas.batch.presentation', // ✅ presentation vive en batch
            'diluentPresentation.diluent',
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

        $hospital = optional(optional($mezcla->solicitud)->hospital)->name ?? 'No asignado';

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

        $preparadaPor = $preparoNombre ?: '—';

        // ===== DILUYENTE BASE =====
        $diluyenteBase = optional(optional($mezcla->diluentPresentation)->diluent)->denominacion_generica ?? '—';

        // ===== fallback presentaciones =====
        $catalogIds = $mezcla->medicamentos
            ->map(fn($mm) => optional(optional($mm->medicamentoOnco)->catalog)->id)
            ->filter()
            ->unique()
            ->values();

        $presentacionFallbackPorCatalogo = DB::table('medicine_presentations')
            ->whereIn('catalog_id', $catalogIds)
            ->where('is_available', 1)
            ->orderBy('presentacion')
            ->get()
            ->groupBy('catalog_id')
            ->map(fn($rows) => $rows->first());

        // ==========================================================
        // ✅ CÁLCULOS
        // ==========================================================
        $volumenTotalSolicitado = (float) ($mezcla->volumen_dilucion ?? 0);

        $legendProteccion = null;
        $totalVolMedicamentos = 0.0;

        // ✅ Regla: si cualquier medicamento requiere infusor, ocultar "Extraer"
        $ocultarExtraer = $mezcla->medicamentos->contains(function ($mm) {
            return (bool) (optional(optional($mm->medicamentoOnco)->catalog)->requires_infusor ?? false);
        });

        // ===== MEDICAMENTOS (para tablas) =====
        $medicamentos = $mezcla->medicamentos->map(function ($mm) use (
            $mezcla,
            $volumenTotalSolicitado,
            &$legendProteccion,
            &$totalVolMedicamentos,
            $presentacionFallbackPorCatalogo
        ) {
            $presentaciones = $mm->presentacionesUsadas ?? collect();
            $firstPresUsed  = $presentaciones->first();

            // ✅ FIX: la presentación real viene desde batch->presentation
            $batch     = optional($firstPresUsed)->batch;
            $presModel = optional($batch)->presentation;

            $catalog   = optional(optional($mm->medicamentoOnco)->catalog);
            $catalogId = optional($catalog)->id;

            // ✅ fallback si NO hay presentación usada (ej. cobro por mg)
            if (!$presModel && $catalogId) {
                $fallback = $presentacionFallbackPorCatalogo->get($catalogId);
                if ($fallback) {
                    $presModel = (object) $fallback;
                }
            }

            $lote      = optional($firstPresUsed)->lote_usado ?? (optional($batch)->lote ?? null);
            $caducidad = optional($firstPresUsed)->caducidad_usada ?? (optional($batch)->caducidad ?? null);

            if (!$legendProteccion) {
                $candLegend = trim((string) optional($presModel)->legend);
                if ($candLegend !== '') $legendProteccion = $candLegend;
            }

            // ✅ Nombre para texto (Denominación + Marca)
            $denom = trim((string) (optional($catalog)->denominacion ?? ''));
            $marca = trim((string) (optional($presModel)->marca ?? ''));

            $fallbackNombre = $mm->nombre_medicamento ?? 'Medicamento';
            $fallbackNombre = trim(preg_replace('/\s*\(undefined\)\s*/i', '', $fallbackNombre));

            $nombreMed = $denom !== ''
                ? ($marca !== '' ? "{$denom} ({$marca})" : $denom)
                : $fallbackNombre;

            // ====== Volumen orden de preparación (mL) ======
            $volOrdenPrep = null;

            if (!is_null($mm->dosis_ml) && is_numeric($mm->dosis_ml) && (float)$mm->dosis_ml > 0) {
                $volOrdenPrep = (float) $mm->dosis_ml;
            } else {
                $dosisMg   = (float) ($mm->dosis ?? 0);
                $mgPres    = (float) (optional($presModel)->cantidad_medicamento ?? 0);
                $volPresMl = (float) (optional($presModel)->volumen_diluyente ?? 0);

                if ($dosisMg > 0 && $mgPres > 0 && $volPresMl > 0) {
                    $volOrdenPrep = ($dosisMg * $volPresMl) / $mgPres;
                }
            }

            if (is_null($volOrdenPrep) || $volOrdenPrep <= 0) {
                $volOrdenPrep = 0.0;
            }

            $totalVolMedicamentos += $volOrdenPrep;

            return (object) [
                'lote'      => $lote,
                'caducidad' => $caducidad,

                'denominacion' => optional($catalog)->denominacion ?? null,
                'presentacion' => optional($presModel)->presentacion ?? null,
                'dosis'         => $mm->dosis,

                'volumen_orden_preparacion' => $volOrdenPrep,
                // ⚠️ se asigna GLOBAL abajo
                'volumen_diluyente'         => null,
                'volumen_total'             => $mezcla->volumen_dilucion,

                'nombre_para_texto' => $nombreMed,
            ];
        })->values();

        if (!$legendProteccion) $legendProteccion = '—';

        // ✅ Diluyente restante GLOBAL (correcto)
        $extraerTotal = (float) $totalVolMedicamentos;

        $diluyenteRestante = $volumenTotalSolicitado - $extraerTotal;
        if ($diluyenteRestante < 0) $diluyenteRestante = 0.0;

        // Si quieres mostrarlo en tabla, pon el mismo valor en todas las filas
        $medicamentos->each(function ($m) use ($diluyenteRestante) {
            $m->volumen_diluyente = $diluyenteRestante;
        });

        $fmt = function ($n) {
            return rtrim(rtrim(number_format((float)$n, 2, '.', ''), '0'), '.');
        };

        $extraerTotalFmt          = $fmt($extraerTotal);
        $diluyenteRestanteFmt     = $fmt($diluyenteRestante);
        $volTotalFmt              = $fmt($volumenTotalSolicitado);

        // ===== Texto final "Agregar" =====
        $partesMed = $medicamentos
            ->filter(fn($m) => (float)($m->volumen_orden_preparacion ?? 0) > 0)
            ->map(function ($m) use ($fmt) {
                $v   = $fmt($m->volumen_orden_preparacion ?? 0);
                $nom = $m->nombre_para_texto ?? 'Medicamento';
                return "{$v} mL de {$nom}";
            })
            ->values()
            ->all();

        $detalleAgregar = [];
        if (count($partesMed) > 0) {
            // ✅ Si hay medicamento con infusor: NO "a restante", sino "y VOLUMEN TOTAL ... al infusor"
            if ($ocultarExtraer) {
                $detalleAgregar[] = implode(' + ', $partesMed) . " y {$volTotalFmt} mL de {$diluyenteBase} al infusor.";
            } else {
                $detalleAgregar[] = implode(' + ', $partesMed) . " a {$diluyenteRestanteFmt} mL de {$diluyenteBase}";
            }
        }

        // ===== Concentración final =====
        $dosisTotalMg    = $mezcla->medicamentos->sum(fn($m) => (float) ($m->dosis ?? 0));
        $volumenFinalMl  = (float) ($mezcla->volumen_dilucion ?? 0);

        $concentracionFinal = null;
        if ($dosisTotalMg > 0 && $volumenFinalMl > 0) {
            $concentracionFinal = $dosisTotalMg / $volumenFinalMl;
        }

        $concentracionFinalFmt = $concentracionFinal !== null
            ? rtrim(rtrim(number_format($concentracionFinal, 4, '.', ''), '0'), '.')
            : '—';

        $pdf = Pdf::loadView('pdfs.oncologicos.orden-de-preparacion', [
            'mezcla'               => $mezcla,
            'medicamentos'         => $medicamentos,
            'fecha_preparacion'    => $fechaPreparacion,
            'fecha_limite_uso'     => $fechaLimiteUso,
            'hospital'             => $hospital,

            'aprobo_nombre'        => $aproboNombre,
            'reviso_nombre'        => $revisoNombre,
            'preparo_nombre'       => $preparoNombre,
            'libero_nombre'        => $liberoNombre,

            'preparadaPor'         => $preparadaPor,

            'equipoInfusion'       => $equipoInfusion,
            'concentracion_final'  => $concentracionFinalFmt,

            'diluyente_base'       => $diluyenteBase,
            'extraer_ml'           => $extraerTotalFmt,
            'detalle_agregar'      => $detalleAgregar,
            'legend_proteccion'    => $legendProteccion,

            // ✅ NUEVO: para ocultar Extraer en Blade
            'ocultarExtraer'       => $ocultarExtraer,
        ])->setPaper('letter', 'portrait');

        return $pdf->stream("orden-preparacion-{$mezcla->id}.pdf");
    }


    public function inspeccion(Mezcla $mezcla)
    {
        // ✅ Cargar todo lo útil para el PDF
        $mezcla = Mezcla::with([
            'solicitud.hospital',
            'inspeccion',
        ])->findOrFail($mezcla->id);

        // ✅ Asegurar que exista inspección (para evitar null en la vista)
        $inspeccion = $mezcla->inspeccion ?: InspeccionMezcla::firstOrCreate(
            ['mezcla_id' => $mezcla->id],
            [
                'fecha_inspeccion' => now()->toDateString(),
                'hora_inspeccion'  => now()->format('H:i:s'),

                // 👇 si tu tabla tiene NOT NULL en estos campos (como en tu update)
                'reviso_nombre'    => '',
                'aprobo_nombre'    => '',
            ]
        );

        $pdf = Pdf::loadView('pdfs.oncologicos.inspeccion', [
            'mezcla'     => $mezcla,
            'solicitud'  => $mezcla->solicitud,
            'inspeccion' => $inspeccion,
            'hospital'   => optional(optional($mezcla->solicitud)->hospital)->name ?? '—', // por si lo ocupas en Blade
        ])->setPaper('letter', 'portrait');

        return $pdf->stream("inspeccion-mezcla-{$mezcla->id}.pdf");
    }


    public function etiqueta(Mezcla $mezcla)
    {
        // ✅ Bloquear clientes (maneja Cliente/cliente)
        if (auth()->user()?->hasRole('Cliente') || auth()->user()?->hasRole('cliente')) {
            abort(403, 'No autorizado');
        }

        $mezcla = Mezcla::with([
            'solicitud.hospital',               // ✅ Cliente (hospital)
            'inspeccion',                       // ✅ preparo_nombre
            'diluentPresentation.diluent',      // ✅ diluyente
            'medicamentos.presentacionesUsadas.batch.presentation', // ✅ marca/leyenda/temp/estabilidad
            'medicamentos.medicamentoOnco.catalog',                 // ✅ denominacion genérica
        ])->findOrFail($mezcla->id);

        $aprobada = DB::table('solicitud_aprobadas')
            ->where('solicitud_id', $mezcla->solicitud_id)
            ->first();

        // =========================================
        // ✅ Cliente = Hospital
        // =========================================
        $cliente = optional(optional($mezcla->solicitud)->hospital)->name ?? '—';

        // =========================================
        // ✅ Preparada por (SOLO este campo desde inspeccion_mezclas)
        // =========================================
        $preparadaPor = optional($mezcla->inspeccion)->preparo_nombre ?: '—';

        // =========================================
        // 0) Observaciones (para etiqueta)
        // =========================================
        $observaciones = $mezcla->solicitud->observaciones ?? null;

        // =========================================
        // 1) Medicamentos: nombre (denominación + marca) + dosis + lote + caducidad
        // =========================================
        $medicamentosTabla = $mezcla->medicamentos->map(function ($m) {

            $catalog = optional(optional($m->medicamentoOnco)->catalog);
            $denom   = trim((string) ($catalog->denominacion ?? ''));

            $presentaciones = $m->presentacionesUsadas ?? collect();
            $firstUsed = $presentaciones->first();

            // Presentation model (por relación cargada)
            $presentation = optional(optional($firstUsed)->batch)->presentation
                ?? optional($firstUsed)->presentation
                ?? null;

            $marca = trim((string) (optional($presentation)->marca ?? ''));

            // Nombre final
            if ($denom !== '') {
                $nombre = $marca !== '' ? "{$denom} ({$marca})" : $denom;
            } else {
                // Fallback: nombre_medicamento sin "(undefined)"
                $fallbackNombre = $m->nombre_medicamento ?? '—';
                $fallbackNombre = trim(preg_replace('/\s*\(undefined\)\s*/i', '', $fallbackNombre));
                $nombre = $fallbackNombre ?: '—';
            }

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

        // Defaults visuales
        if (!$chosenLegend) $chosenLegend = '—';

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
        // 4) Texto del diluyente + lote/cad (desde diluent_presentation)
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

            $diluyenteLote = $dp->lote ?? null;
            $diluyenteCad  = $dp->caducidad ?? null;
        }

        // ✅ PDF (7.5cm x 5cm) en puntos: 212.6 x 141.7
        // OJO: si pones 'landscape' Dompdf rota y te invierte el tamaño.
        // Para que quede ANCHO 7.5cm y ALTO 5cm, usa 'portrait' con este array:
        $customPaper = [0, 0, 212.6, 141.7];

        $pdf = Pdf::loadView('pdfs.oncologicos.etiqueta', [
            'mezcla'            => $mezcla,
            'solicitud'         => $mezcla->solicitud,
            'aprobada'          => $aprobada,

            'cliente'           => $cliente,
            'preparadaPor'      => $preparadaPor,

            'medicamentos'      => $medicamentosTabla,

            'diluyenteTexto'    => $diluyenteTexto,
            'diluyenteLote'     => $diluyenteLote,
            'diluyenteCad'      => $diluyenteCad,

            'observaciones'     => $observaciones,

            'fechaPreparacion'  => $fechaPreparacion,
            'fechaLimiteUso'    => $fechaLimiteUso,
            'legendEtiqueta'    => $chosenLegend,
            'tempMinEtiqueta'   => $chosenTempMin,
            'tempMaxEtiqueta'   => $chosenTempMax,
            'stabilityEtiqueta' => $chosenStabilityHours,
        ])->setPaper($customPaper, 'portrait'); // ✅ importante

        return $pdf->stream();
    }
}
