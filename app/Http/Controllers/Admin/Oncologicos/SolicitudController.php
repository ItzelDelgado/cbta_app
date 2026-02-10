<?php

namespace App\Http\Controllers\Admin\Oncologicos;

use App\Exports\Oncologicos\SolicitudesOncoExport as OncologicosSolicitudesOncoExport;
use App\Http\Controllers\Controller;
use App\Models\Oncologicos\MedicineOnco;
use App\Models\Oncologicos\Mezcla;
use App\Models\Oncologicos\MezclaMedicamento;
use App\Models\Oncologicos\SolicitudOnco;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SolicitudesOncoExport;


class SolicitudController extends Controller
{
    public function index()
    {
        $solicitudes = SolicitudOnco::with([
            'hospital', // ✅ snapshot
            'user',     // solo si necesitas nombre del usuario
        ])
            ->orderByDesc('id')
            ->get();

        return view('admin.oncologicos.solicitudes.index', compact('solicitudes'));
    }


    public function create()
    {
        $user    = Auth::user();
        $listaId = $user->medicine_list_id; // puede ser null

        // ==============================
        // 1) CATÁLOGOS GENÉRICOS ACTIVOS
        // ==============================
        $catalogos = DB::table('medicines_catalog as mc')
            ->where('mc.state', true)
            ->select(
                'mc.id as id',
                'mc.denominacion',
                'mc.requires_infusor'
            )
            ->orderBy('mc.denominacion')
            ->get()
            ->map(function ($m) {
                $m->requires_infusor = (int) ($m->requires_infusor ?? 0);
                return $m;
            });

        // ✅ Alias para no romper la vista/JS actual
        $medicamentos = $catalogos;

        // ==============================
        // 2) PRESENTACIONES POR CATÁLOGO
        // ==============================
        $presentacionesPorCatalogo = collect();

        if ($listaId) {
            $presentaciones = DB::table('medicine_presentations as mp')
                ->join('medicine_list_presentation as mlp', 'mlp.medicine_presentation_id', '=', 'mp.id')
                ->where('mlp.medicine_list_id', $listaId)
                ->where('mp.is_available', 1)
                ->select(
                    'mp.id',
                    'mp.catalog_id',
                    'mp.presentacion',
                    'mp.marca',
                    'mp.cantidad_medicamento',
                    'mp.volumen_diluyente',
                    'mp.precio_frasco',
                    'mlp.charge_by',
                    'mlp.precio',
                    'mlp.precio_mg_override'
                )
                ->orderBy('mp.presentacion')
                ->get();

            $presentacionesPorCatalogo = $presentaciones
                ->groupBy('catalog_id')
                ->map(fn($rows) => $rows->values());
        } else {
            $presentaciones = DB::table('medicine_presentations as mp')
                ->where('mp.is_available', 1)
                ->select(
                    'mp.id',
                    'mp.catalog_id',
                    'mp.presentacion',
                    'mp.marca',
                    'mp.cantidad_medicamento',
                    'mp.volumen_diluyente',
                    'mp.precio_frasco'
                )
                ->orderBy('mp.presentacion')
                ->get();

            $presentacionesPorCatalogo = $presentaciones
                ->groupBy('catalog_id')
                ->map(fn($rows) => $rows->values());
        }

        // ==============================
        // 3) INFO ADICIONAL por catálogo
        // ==============================
        $infoAdicional = [];
        foreach ($catalogos as $cat) {
            $diluyentes = DB::table('diluent_medicine_catalog')
                ->join('diluents', 'diluent_medicine_catalog.diluent_id', '=', 'diluents.id')
                ->where('diluent_medicine_catalog.medicine_catalog_id', $cat->id)
                ->select(
                    'diluents.id',
                    DB::raw('diluents.denominacion_generica as name')
                )
                ->get();

            $vias = DB::table('administration_route_medicine_catalog')
                ->join('administration_routes', 'administration_route_medicine_catalog.administration_route_id', '=', 'administration_routes.id')
                ->where('administration_route_medicine_catalog.medicine_catalog_id', $cat->id)
                ->select('administration_routes.id', 'administration_routes.name')
                ->get();

            $infoAdicional[$cat->id] = [
                'diluyentes'       => $diluyentes,
                'vias'             => $vias,
                'requires_infusor' => (int) $cat->requires_infusor,
            ];
        }

        // ==============================
        // 4) INFUSORES
        // ==============================
        $infusors = DB::table('infusors')
            ->select('id', 'nombre_generico', 'nombre_comercial')
            ->where('is_active', true)
            ->orderBy('nombre_generico')
            ->orderBy('nombre_comercial')
            ->get();



        return view('admin.oncologicos.solicitudes.create', [
            // ✅ deja ambos para compatibilidad
            'catalogos'                 => $catalogos,
            'medicamentos'              => $medicamentos,

            'presentacionesPorCatalogo' => $presentacionesPorCatalogo,
            'infoAdicional'             => $infoAdicional,
            'infusors'                  => $infusors,
        ]);
    }


    public function store(Request $request)
    {


        $request->validate([
            'paciente_nombre'  => 'required|string|max:255',
            'servicio'         => 'required|string|max:255',
            'registro'         => 'required|string|max:255',
            'sexo'             => 'required|in:M,F',

            // 👇 VALIDACIÓN CORRECTA DE EDAD
            'fecha_nacimiento' => [
                'required',
                'date',
                'after:' . Carbon::now()->subYears(100)->format('Y-m-d'),
                'before:' . Carbon::today()->format('Y-m-d'),
            ],

            'peso'             => 'required|numeric|min:1|max:500',
            'piso'             => 'required|string|max:50',
            'cama'             => 'required|string|max:50',
            'diagnostico'      => 'required|string|max:255',
            'alergias'         => 'nullable|string|max:255',
            'medico_nombre'    => 'required|string|max:255',
            'medico_cedula'    => 'required|string|max:255',
            'fecha_entrega'    => 'required|date|after_or_equal:hoy',
            'observaciones'    => 'nullable|string|max:500',
            'mezclas'          => 'required|string',
        ], [
            // ✨ Mensajes claros para el usuario
            'fecha_nacimiento.after'  => 'La fecha de nacimiento no puede ser mayor a 100 años.',
            'fecha_nacimiento.before' => 'La fecha de nacimiento debe ser anterior a hoy.',
        ]);

        $mezclas = json_decode($request->mezclas, true);
        if (!is_array($mezclas)) {
            return back()
                ->withErrors(['mezclas' => 'El formato del campo mezclas no es válido.'])
                ->withInput();
        }

        DB::beginTransaction();
        try {
            $user = auth()->user();

            // Mapa de precios personalizados por CATALOGO,
            // a partir de la lista del usuario (si tiene)
            $precios = collect();
            if ($user->medicine_list_id) {
                $precios = DB::table('medicine_medicine_lists as mml')
                    ->join('medicine_oncos as mo', 'mml.medicine_id', '=', 'mo.id')
                    ->where('mml.medicine_list_id', $user->medicine_list_id)
                    ->pluck('mml.precio', 'mo.catalog_id'); // key = catalog_id
            }

            // Crear solicitud principal
            $solicitud = SolicitudOnco::create([
                'user_id'           => $user->id,
                'hospital_id'       => $user->hospital_id,
                'servicio'          => $request->servicio,
                'nombre_paciente'   => $request->paciente_nombre,
                'sexo'              => $request->sexo,
                'edad'              => null,
                'peso'              => $request->peso,
                'cama'              => $request->cama,
                'piso'              => $request->piso,
                'registro_paciente' => $request->registro,
                'fecha_nacimiento'  => $request->fecha_nacimiento,
                'diagnostico'       => $request->diagnostico,
                'alergias'          => $request->alergias ?? '',
                'fecha_entrega'     => $request->fecha_entrega,
                'observaciones'     => $request->observaciones,
                'nombre_medico'     => $request->medico_nombre,
                'cedula_medico'     => $request->medico_cedula,
                'estado'            => 'pendiente',
                'remision'          => null,
            ]);

            foreach ($mezclas as $index => $mezclaData) {
                // --- Validaciones por mezcla ---
                $volumen = isset($mezclaData['volumen_dilucion']) ? floatval($mezclaData['volumen_dilucion']) : null;
                $tiempo  = isset($mezclaData['tiempo_infusion'])   ? $mezclaData['tiempo_infusion'] : null;

                if ($volumen === null || $volumen <= 0) {
                    throw new \Exception("La Mezcla #" . ($index + 1) . " requiere un volumen de dilución válido (> 0).");
                }
                if ($tiempo === null || $tiempo === '') {
                    throw new \Exception("La Mezcla #" . ($index + 1) . " requiere un tiempo de infusión.");
                }

                $setInfusion = !empty($mezclaData['set_infusion']) ? (bool)$mezclaData['set_infusion'] : false;
                $infusorId   = !empty($mezclaData['infusor_id']) ? (int)$mezclaData['infusor_id'] : null;

                // Regla mutua (pero ambos opcionales)
                if ($setInfusion && $infusorId) {
                    throw new \Exception("Mezcla #" . ($index + 1) . ": selecciona set de infusión o un infusor, no ambos.");
                }

                // Validación medicamentos
                $meds = $mezclaData['medicamentos'] ?? [];
                if (!is_array($meds) || count($meds) === 0) {
                    throw new \Exception("La Mezcla #" . ($index + 1) . " no contiene medicamentos.");
                }

                $diluyenteRef = null;
                $viaRef       = null;
                $hayMedQueRequiereInfusor = false;

                foreach ($meds as $i => $medicamento) {
                    // 👉 AHORA medicamento_id viene del front como ID DE CATALOGO
                    $catalogId  = (int)($medicamento['medicamento_id'] ?? 0);
                    $diluyente  = $medicamento['diluyente_id'] ?? null;
                    $via        = $medicamento['via_administracion_id'] ?? null;

                    if ($catalogId <= 0) {
                        throw new \Exception("Mezcla #" . ($index + 1) . ": medicamento inválido.");
                    }

                    if ($i === 0) {
                        $diluyenteRef = $diluyente;
                        $viaRef       = $via;
                    } else {
                        if ($diluyente !== $diluyenteRef || $via !== $viaRef) {
                            throw new \Exception("Mezcla #" . ($index + 1) . ": todos los medicamentos deben tener el mismo diluyente y la misma vía de administración.");
                        }
                    }

                    // Buscar info de catálogo (conc_min, conc_max, requires_infusor, etc.)
                    $mc = DB::table('medicines_catalog as mc')
                        ->where('mc.id', $catalogId)
                        ->select(
                            'mc.requires_infusor',
                            'mc.denominacion',
                            'mc.conc_min',
                            'mc.conc_max',
                        )
                        ->first();

                    if (!$mc) {
                        throw new \Exception("No se encontró información del catálogo para el medicamento ID {$catalogId}.");
                    }

                    if ((int) $mc->requires_infusor === 1) {
                        $hayMedQueRequiereInfusor = true;
                    }
                }

                // Validar infusor (si viene)
                if ($infusorId) {
                    $infusor = DB::table('infusors')
                        ->where('id', $infusorId)
                        ->where('is_active', true)
                        ->first();

                    if (!$infusor) {
                        throw new \Exception("Mezcla #" . ($index + 1) . ": el infusor seleccionado no existe o no está activo.");
                    }

                    if (!$hayMedQueRequiereInfusor) {
                        throw new \Exception("Mezcla #" . ($index + 1) . ": para seleccionar un infusor, la mezcla debe contener al menos un medicamento que lo admita.");
                    }
                }

                // Crear la mezcla
                $mezcla = Mezcla::create([
                    'solicitud_id'     => $solicitud->id,
                    'volumen_dilucion' => $volumen,
                    'tiempo_infusion'  => $tiempo,
                    'estado'           => 'pendiente',
                    'set_infusion'     => $setInfusion,
                    'infusor_id'       => $infusorId,
                ]);

                // Guardar medicamentos de la mezcla
                foreach ($meds as $medicamento) {
                    $catalogId = (int)($medicamento['medicamento_id'] ?? 0);

                    // Asegurar que exista el registro en medicine_oncos para ese catálogo
                    $medicineOnco = MedicineOnco::firstOrCreate(
                        ['catalog_id' => $catalogId],
                        ['precio'     => 0] // default, se sobre-escribe con lista si aplica
                    );

                    // Obtener catálogo (relación) o desde la tabla directa
                    $catalog = $medicineOnco->catalog;
                    if (!$catalog) {
                        throw new \Exception("No se encontró información del catálogo para el medicamento ID {$catalogId}.");
                    }

                    // Validación de concentración
                    $dosis = isset($medicamento['dosis']) ? floatval($medicamento['dosis']) : 0;
                    $concentracion = $volumen > 0 ? $dosis / $volumen : 0;

                    if ($concentracion < $catalog->conc_min || $concentracion > $catalog->conc_max) {
                        throw new \Exception(
                            "La concentración de '{$catalog->denominacion}' está fuera del rango permitido ({$catalog->conc_min} - {$catalog->conc_max} mL). Dosis: {$dosis}, Volumen: {$volumen}."
                        );
                    }

                    // Dosis en mL
                    $dosisML = null;
                    if ($catalog->cantidad_medicamento > 0 && $catalog->volumen_diluyente > 0) {
                        $dosisML = ($dosis * $catalog->volumen_diluyente) / $catalog->cantidad_medicamento;
                    }

                    // Precio unitario: lista por catálogo, si no existe -> precio en medicine_oncos
                    $precioUnit = $precios[$catalogId] ?? $medicineOnco->precio ?? 0;

                    // =========================
                    // ✅ SNAPSHOT DE CATÁLOGO
                    // =========================
                    $denSnapshot  = $catalog->denominacion ?? null;
                    $reqInfSnap   = (bool) ($catalog->requires_infusor ?? false);
                    $concMinSnap  = $catalog->conc_min ?? null;
                    $concMaxSnap  = $catalog->conc_max ?? null;

                    // =========================
                    // ✅ Marca snapshot (si el front manda presentation_id)
                    // =========================
                    $marcaSnapshot = null;

                    $presentationId = $medicamento['medicine_presentation_id'] ?? null; // 👈 si lo mandas desde el front

                    if ($presentationId) {
                        $pres = DB::table('medicine_presentations')
                            ->where('id', (int)$presentationId)
                            ->where('catalog_id', $catalogId) // seguridad
                            ->select('marca', 'cantidad_medicamento', 'volumen_diluyente')
                            ->first();

                        if ($pres) {
                            $marcaSnapshot = $pres->marca;

                            // ✅ dosis_ml usando presentación real
                            if ($pres->cantidad_medicamento > 0 && $pres->volumen_diluyente > 0) {
                                $dosisML = ($dosis * (float)$pres->volumen_diluyente) / (float)$pres->cantidad_medicamento;
                            }
                        }
                    }

                    MezclaMedicamento::create([
                        'mezcla_id'                  => $mezcla->id,
                        'medicamento_id'             => $medicineOnco->id, // ID de medicine_oncos
                        'nombre_medicamento'         => $medicamento['nombre'] ?? null,

                        'dosis'                      => $dosis,
                        'dosis_ml'                   => $dosisML,

                        'diluyente_id'               => $medicamento['diluyente_id'] ?? null,
                        'via_administracion_id'      => $medicamento['via_administracion_id'] ?? null,

                        // ✅ SNAPSHOTS
                        'denominacion_snapshot'      => $denSnapshot,
                        'marca_snapshot'             => $marcaSnapshot,
                        'requires_infusor_snapshot'  => $reqInfSnap,
                        'conc_min_snapshot'          => $concMinSnap,
                        'conc_max_snapshot'          => $concMaxSnap,
                    ]);
                }
            }

            DB::commit();
            return redirect()
                ->route('admin.oncologicos.solicitudes.index')
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
        $user = Auth::user();

        $solicitud = SolicitudOnco::with([
            'hospital',
            'user:id,name,lastname,medicine_list_id,hospital_id',
            'mezclas',
            'mezclas.medicamentos',
            'mezclas.medicamentos.medicamentoOnco.catalog',
            'mezclas.medicamentos.presentacionesUsadas.batch.presentation',
            'mezclas.medicamentos.diluyente',
            'mezclas.medicamentos.viaAdministracion',
            'mezclas.diluentPresentation.diluent',
            'mezclas.infusor',
        ])->findOrFail($id);

        // =========================
        // Seguridad por hospital
        // =========================
        $esAdmin = false; // si luego quieres roles, aquí lo cambias

        if (
            !$esAdmin &&
            $user->hospital_id &&
            $solicitud->hospital_id &&
            (int)$user->hospital_id !== (int)$solicitud->hospital_id
        ) {
            abort(403, 'No autorizado para editar solicitudes de otro hospital.');
        }

        $listaId = $user->medicine_list_id;

        // ==============================
        // 1) CATÁLOGOS GENÉRICOS ACTIVOS
        // ==============================
        $catalogos = DB::table('medicines_catalog as mc')
            ->where('mc.state', true)
            ->select(
                'mc.id as id',
                'mc.denominacion',
                'mc.requires_infusor'
            )
            ->orderBy('mc.denominacion')
            ->get()
            ->map(function ($m) {
                $m->requires_infusor = (int) ($m->requires_infusor ?? 0);
                return $m;
            });

        // ==============================
        // 2) PRESENTACIONES POR CATÁLOGO
        // ==============================
        if ($listaId) {
            $presentaciones = DB::table('medicine_presentations as mp')
                ->join('medicine_list_presentation as mlp', 'mlp.medicine_presentation_id', '=', 'mp.id')
                ->where('mlp.medicine_list_id', $listaId)
                ->where('mp.is_available', 1)
                ->select(
                    'mp.id',
                    'mp.catalog_id',
                    'mp.presentacion',
                    'mp.marca',
                    'mp.cantidad_medicamento',
                    'mp.volumen_diluyente',
                    'mp.precio_frasco',
                    'mlp.charge_by',
                    'mlp.precio',
                    'mlp.precio_mg_override'
                )
                ->orderBy('mp.presentacion')
                ->get();

            $presentacionesPorCatalogo = $presentaciones
                ->groupBy('catalog_id')
                ->map(fn($rows) => $rows->values());
        } else {
            $presentaciones = DB::table('medicine_presentations as mp')
                ->where('mp.is_available', 1)
                ->select(
                    'mp.id',
                    'mp.catalog_id',
                    'mp.presentacion',
                    'mp.marca',
                    'mp.cantidad_medicamento',
                    'mp.volumen_diluyente',
                    'mp.precio_frasco'
                )
                ->orderBy('mp.presentacion')
                ->get();

            $presentacionesPorCatalogo = $presentaciones
                ->groupBy('catalog_id')
                ->map(fn($rows) => $rows->values());
        }

        // ==============================
        // 3) INFO ADICIONAL por catálogo_id
        // ==============================
        $infoAdicional = [];

        foreach ($catalogos as $cat) {
            $diluyentes = DB::table('diluent_medicine_catalog')
                ->join('diluents', 'diluent_medicine_catalog.diluent_id', '=', 'diluents.id')
                ->where('diluent_medicine_catalog.medicine_catalog_id', $cat->id)
                ->select('diluents.id', DB::raw('diluents.denominacion_generica as name'))
                ->get();

            $vias = DB::table('administration_route_medicine_catalog')
                ->join('administration_routes', 'administration_route_medicine_catalog.administration_route_id', '=', 'administration_routes.id')
                ->where('administration_route_medicine_catalog.medicine_catalog_id', $cat->id)
                ->select('administration_routes.id', 'administration_routes.name')
                ->get();

            $infoAdicional[$cat->id] = [
                'catalog_id'        => $cat->id, // ✅ útil para JS si lo ocupas
                'diluyentes'        => $diluyentes,
                'vias'              => $vias,
                'requires_infusor'  => (int) $cat->requires_infusor,
            ];
        }

        // ==============================
        // 4) INFUSORES
        // ==============================
        $infusors = DB::table('infusors')
            ->select('id', 'nombre_generico', 'nombre_comercial')
            ->where('is_active', true)
            ->orderBy('nombre_generico')
            ->orderBy('nombre_comercial')
            ->get();

        // ==============================
        // 5) SNAPSHOTS de medicamentos
        // ==============================
        $medSnapshots = $solicitud->mezclas
            ->flatMap(fn($mezcla) => $mezcla->medicamentos)
            ->mapWithKeys(function ($mm) {
                $catalog = optional(optional($mm->medicamentoOnco)->catalog);

                return [
                    $mm->id => [
                        'denominacion'     => $mm->denominacion_snapshot
                            ?? ($catalog->denominacion ?? $mm->nombre_medicamento ?? '—'),
                        'marca'            => $mm->marca_snapshot ?? null,
                        'requires_infusor' => $mm->requires_infusor_snapshot
                            ?? (int) ($catalog->requires_infusor ?? 0),
                        'conc_min'         => $mm->conc_min_snapshot ?? ($catalog->conc_min ?? null),
                        'conc_max'         => $mm->conc_max_snapshot ?? ($catalog->conc_max ?? null),
                    ],
                ];
            });

        // ==============================
        // 6) ENVIAR A LA VISTA
        // ==============================
        return view('admin.oncologicos.solicitudes.edit', [
            'solicitud'                 => $solicitud,

            // ✅ tu JS seguramente espera esto como "medicamentos"
            'medicamentos'              => $catalogos,

            'catalogos'                 => $catalogos,
            'presentacionesPorCatalogo' => $presentacionesPorCatalogo,
            'infoAdicional'             => $infoAdicional,
            'infusors'                  => $infusors,

            'hospitalNombre'            => optional($solicitud->hospital)->name,
            'medSnapshots'              => $medSnapshots,
        ]);
    }



    public function update(Request $request, $id)
    {
        $request->validate([
            'paciente_nombre'  => 'required|string|max:255',
            'servicio'         => 'required|string|max:255',
            'registro'         => 'required|string|max:255',
            'sexo'             => 'required|in:M,F',
            'fecha_nacimiento' => [
                'required',
                'date',
                'after:' . Carbon::now()->subYears(100)->format('Y-m-d'),
                'before:' . Carbon::today()->format('Y-m-d'),
            ],
            'peso'             => 'required|numeric|min:1|max:200',
            'piso'             => 'required|string|max:50',
            'cama'             => 'required|string|max:50',
            'diagnostico'      => 'required|string|max:255',
            'alergias'         => 'nullable|string|max:255',
            'medico_nombre'    => 'required|string|max:255',
            'medico_cedula'    => 'required|string|max:255',
            'fecha_entrega'    => 'required|date|after_or_equal:today',
            'observaciones'    => 'nullable|string|max:500',
            'mezclas'          => 'required|string',
        ], [
            'fecha_nacimiento.after'  => 'La fecha de nacimiento no puede ser mayor a 100 años.',
            'fecha_nacimiento.before' => 'La fecha de nacimiento debe ser anterior a hoy.',
            'fecha_entrega.after_or_equal' => 'La fecha de entrega no puede ser anterior a hoy.',
        ]);

        $mezclas = json_decode($request->mezclas, true);
        if (!is_array($mezclas)) {
            return back()->withErrors(['mezclas' => 'El formato del campo mezclas no es válido.'])->withInput();
        }

        $user = auth()->user();

        DB::beginTransaction();
        try {
            $solicitud = SolicitudOnco::with([
                'mezclas.medicamentos',
                'mezclas.medicamentos.medicamentoOnco.catalog',
            ])->findOrFail($id);

            // =====================================================
            // ✅ Seguridad por hospital (snapshot)
            // =====================================================
            if ($user->hospital_id && $solicitud->hospital_id && (int)$user->hospital_id !== (int)$solicitud->hospital_id) {
                abort(403, 'No autorizado para editar solicitudes de otro hospital.');
            }

            // =====================================================
            // ✅ Asegurar snapshot (solo si está NULL)
            // =====================================================
            if (empty($solicitud->hospital_id) && !empty($user->hospital_id)) {
                $solicitud->hospital_id = $user->hospital_id;
                $solicitud->save();
            }

            // 1) Actualizar datos básicos
            $solicitud->update([
                'servicio'          => $request->servicio,
                'nombre_paciente'   => $request->paciente_nombre,
                'sexo'              => $request->sexo,
                'peso'              => $request->peso,
                'cama'              => $request->cama,
                'piso'              => $request->piso,
                'registro_paciente' => $request->registro,
                'fecha_nacimiento'  => $request->fecha_nacimiento,
                'diagnostico'       => $request->diagnostico,
                'alergias'          => $request->alergias ?? '',
                'fecha_entrega'     => $request->fecha_entrega,
                'observaciones'     => $request->observaciones,
                'nombre_medico'     => $request->medico_nombre,
                'cedula_medico'     => $request->medico_cedula,
            ]);

            // Particionamos
            $existentes = array_values(array_filter($mezclas, fn($m) => !empty($m['existente'])));
            $nuevas     = array_values(array_filter($mezclas, fn($m) => empty($m['existente'])));

            // =====================================================
            // 2) Actualizar set/infusor de mezclas existentes (POR ID)
            // =====================================================
            foreach ($existentes as $payload) {
                $mezclaId = (int)($payload['id'] ?? 0);
                if ($mezclaId <= 0) continue;

                $mezclaModelo = $solicitud->mezclas->firstWhere('id', $mezclaId);
                if (!$mezclaModelo) continue;

                $setInfusion = !empty($payload['set_infusion']);
                $infusorId   = !empty($payload['infusor_id']) ? (int)$payload['infusor_id'] : null;

                if ($setInfusion && $infusorId) {
                    throw new \Exception("Mezcla existente (ID {$mezclaId}): selecciona set de infusión o un infusor, no ambos.");
                }

                // ✅ Si trae infusor, validar que exista/activo y que la mezcla admita infusor
                if ($infusorId) {
                    $infusor = DB::table('infusors')
                        ->where('id', $infusorId)
                        ->where('is_active', true)
                        ->first();

                    if (!$infusor) {
                        throw new \Exception("Mezcla existente (ID {$mezclaId}): el infusor seleccionado no existe o no está activo.");
                    }

                    // ✅ IMPORTANTÍSIMO:
                    // Ya NO dependas del catálogo vivo si ya tienes snapshot.
                    // Usamos requires_infusor_snapshot, y si es NULL, caemos al catálogo.
                    $hayMedQueRequiereInfusor = $mezclaModelo->medicamentos->contains(function ($mm) {
                        if (!is_null($mm->requires_infusor_snapshot)) {
                            return (int)$mm->requires_infusor_snapshot === 1;
                        }
                        return (int)(optional(optional($mm->medicamentoOnco)->catalog)->requires_infusor ?? 0) === 1;
                    });

                    if (!$hayMedQueRequiereInfusor) {
                        throw new \Exception("Mezcla existente (ID {$mezclaId}): para seleccionar un infusor, la mezcla debe contener al menos un medicamento que lo admita.");
                    }
                }

                $mezclaModelo->update([
                    'set_infusion' => $setInfusion,
                    'infusor_id'   => $infusorId,
                ]);
            }

            // =====================================================
            // 3) Crear nuevas mezclas (MISMA LÓGICA QUE STORE)
            // - Front manda medicamento_id = catalog_id
            // - Guardamos snapshots inmutables en mezcla_medicamentos
            // =====================================================
            foreach ($nuevas as $index => $mezclaData) {
                $volumen = isset($mezclaData['volumen_dilucion']) ? (float)$mezclaData['volumen_dilucion'] : null;
                $tiempo  = $mezclaData['tiempo_infusion'] ?? null;

                if ($volumen === null || $volumen <= 0) {
                    throw new \Exception("La nueva mezcla #" . ($index + 1) . " requiere un volumen de dilución válido (> 0).");
                }
                if ($tiempo === null || $tiempo === '') {
                    throw new \Exception("La nueva mezcla #" . ($index + 1) . " requiere un tiempo de infusión.");
                }

                $setInfusion = !empty($mezclaData['set_infusion']);
                $infusorId   = !empty($mezclaData['infusor_id']) ? (int)$mezclaData['infusor_id'] : null;

                if ($setInfusion && $infusorId) {
                    throw new \Exception("Nueva mezcla #" . ($index + 1) . ": selecciona set de infusión o un infusor, no ambos.");
                }

                $meds = $mezclaData['medicamentos'] ?? [];
                if (!is_array($meds) || count($meds) === 0) {
                    throw new \Exception("La nueva mezcla #" . ($index + 1) . " no contiene medicamentos.");
                }

                // Validación diluyente/vía iguales + requires_infusor
                $diluyenteRef = null;
                $viaRef = null;
                $hayMedQueRequiereInfusor = false;

                // Guardamos también los mc consultados para no volver a pegarle a BD en el segundo foreach
                $mcPorCatalogId = [];

                foreach ($meds as $i => $medicamento) {
                    $catalogId = (int)($medicamento['medicamento_id'] ?? 0); // ✅ catalog_id
                    if ($catalogId <= 0) {
                        throw new \Exception("Nueva mezcla #" . ($index + 1) . ": medicamento inválido.");
                    }

                    $diluyente = $medicamento['diluyente_id'] ?? null;
                    $via       = $medicamento['via_administracion_id'] ?? null;

                    if ($i === 0) {
                        $diluyenteRef = $diluyente;
                        $viaRef       = $via;
                    } else {
                        if ($diluyente !== $diluyenteRef || $via !== $viaRef) {
                            throw new \Exception("Nueva mezcla #" . ($index + 1) . ": todos los medicamentos deben tener el mismo diluyente y la misma vía de administración.");
                        }
                    }

                    $mc = DB::table('medicines_catalog as mc')
                        ->where('mc.id', $catalogId)
                        ->select('mc.id', 'mc.denominacion', 'mc.conc_min', 'mc.conc_max', 'mc.requires_infusor')
                        ->first();

                    if (!$mc) {
                        throw new \Exception("No se encontró información del catálogo para el medicamento ID {$catalogId}.");
                    }

                    $mcPorCatalogId[$catalogId] = $mc;

                    if ((int)$mc->requires_infusor === 1) {
                        $hayMedQueRequiereInfusor = true;
                    }
                }

                if ($infusorId) {
                    $infusor = DB::table('infusors')->where('id', $infusorId)->where('is_active', true)->first();
                    if (!$infusor) {
                        throw new \Exception("Nueva mezcla #" . ($index + 1) . ": el infusor seleccionado no existe o no está activo.");
                    }
                    if (!$hayMedQueRequiereInfusor) {
                        throw new \Exception("Nueva mezcla #" . ($index + 1) . ": para seleccionar un infusor, la mezcla debe contener al menos un medicamento que lo admita.");
                    }
                }

                // Crear mezcla nueva
                $mezcla = Mezcla::create([
                    'solicitud_id'     => $solicitud->id,
                    'volumen_dilucion' => $volumen,
                    'tiempo_infusion'  => $tiempo,
                    'estado'           => 'pendiente',
                    'set_infusion'     => $setInfusion,
                    'infusor_id'       => $infusorId,
                ]);

                // Guardar medicamentos + snapshots
                foreach ($meds as $medicamento) {
                    $catalogId = (int)($medicamento['medicamento_id'] ?? 0); // ✅ catalog_id

                    // medicine_oncos debe existir
                    $medicineOnco = MedicineOnco::firstOrCreate(
                        ['catalog_id' => $catalogId],
                        ['precio' => 0]
                    );

                    // Catálogo consultado arriba
                    $mc = $mcPorCatalogId[$catalogId] ?? null;
                    if (!$mc) {
                        throw new \Exception("No se encontró información del catálogo para el medicamento ID {$catalogId}.");
                    }

                    // Dosis y concentración (usar snapshots del catálogo consultado)
                    $dosis = isset($medicamento['dosis']) ? (float)$medicamento['dosis'] : 0;
                    $conc  = $volumen > 0 ? $dosis / $volumen : 0;

                    if (!is_null($mc->conc_min) && !is_null($mc->conc_max)) {
                        if ($conc < (float)$mc->conc_min || $conc > (float)$mc->conc_max) {
                            throw new \Exception(
                                "La concentración de '{$mc->denominacion}' está fuera del rango permitido ({$mc->conc_min} - {$mc->conc_max}). Dosis: {$dosis}, Volumen: {$volumen}."
                            );
                        }
                    }

                    // dosis_ml (en tu flujo real se calcula al preparar con presentación; aquí sigue null)
                    $dosisML = null;

                    // ✅ snapshots: denominación / marca / requires / conc_min / conc_max
                    MezclaMedicamento::create([
                        'mezcla_id'                  => $mezcla->id,
                        'medicamento_id'             => $medicineOnco->id, // ✅ medicine_oncos.id
                        'nombre_medicamento'         => $medicamento['nombre'] ?? null,
                        'dosis'                      => $dosis,
                        'dosis_ml'                   => $dosisML,
                        'diluyente_id'               => $medicamento['diluyente_id'] ?? null,
                        'via_administracion_id'      => $medicamento['via_administracion_id'] ?? null,

                        // ✅ snapshots inmutables (NUEVOS CAMPOS)
                        'denominacion_snapshot'      => $mc->denominacion,
                        'marca_snapshot'             => $medicamento['marca'] ?? null, // si el front la manda; si no, queda null
                        'requires_infusor_snapshot'  => (int)($mc->requires_infusor ?? 0),
                        'conc_min_snapshot'          => $mc->conc_min,
                        'conc_max_snapshot'          => $mc->conc_max,
                    ]);
                }
            }

            DB::commit();

            return redirect()
                ->route('admin.oncologicos.solicitudes.index')
                ->with('success', 'Solicitud actualizada correctamente.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }




    public function solicitud(SolicitudOnco $solicitud)
    {
        $solicitud = SolicitudOnco::with([
            'hospital', // ✅ snapshot del hospital en la solicitud (si ya lo agregaste)
            'user.hospital', // (solo por fallback / compatibilidad)
            'mezclas',
            'mezclas.medicamentos',
            // ✅ fallback para registros viejos sin snapshot
            'mezclas.medicamentos.medicamentoOnco.catalog',
            'mezclas.medicamentos.diluyente',
            'mezclas.medicamentos.viaAdministracion',
        ])->findOrFail($solicitud->id);

        // ✅ Normalizar para la vista: usa snapshots primero, si no existen usa catálogo
        $solicitud->mezclas->each(function ($mezcla) {
            $mezcla->medicamentos->each(function ($mm) {

                // denominación
                $mm->denominacion_doc = $mm->denominacion_snapshot
                    ?? optional(optional($mm->medicamentoOnco)->catalog)->denominacion
                    ?? $mm->nombre_medicamento
                    ?? '—';

                // marca (si la guardas en mezcla_medicamentos; si normalmente viene de presentaciones,
                // aquí será null a menos que la captures en el front o la llenes al preparar)
                $mm->marca_doc = $mm->marca_snapshot ?? '—';

                // requires_infusor
                $mm->requires_infusor_doc = !is_null($mm->requires_infusor_snapshot)
                    ? (int) $mm->requires_infusor_snapshot
                    : (int) (optional(optional($mm->medicamentoOnco)->catalog)->requires_infusor ?? 0);

                // conc_min / conc_max
                $mm->conc_min_doc = $mm->conc_min_snapshot
                    ?? optional(optional($mm->medicamentoOnco)->catalog)->conc_min;

                $mm->conc_max_doc = $mm->conc_max_snapshot
                    ?? optional(optional($mm->medicamentoOnco)->catalog)->conc_max;
            });
        });

        // ✅ Hospital “oficial” del documento (snapshot primero)
        $hospitalDoc = optional($solicitud->hospital)->name
            ?? optional(optional($solicitud->user)->hospital)->name
            ?? '—';

        $pdf = Pdf::loadView('pdfs.oncologicos.solicitud', [
            'solicitud'   => $solicitud,
            'hospitalDoc' => $hospitalDoc, // opcional para imprimirlo en tu blade
        ]);

        return $pdf->stream("solicitud-{$solicitud->id}.pdf");
    }



    public function envio(SolicitudOnco $solicitud)
    {
        $solicitud_onco = SolicitudOnco::with([
            'hospital',          // ✅ snapshot del hospital de la solicitud
            'user.hospital',     // fallback por compatibilidad

            'mezclas',
            'mezclas.medicamentos',

            // ✅ fallback para registros viejos sin snapshot
            'mezclas.medicamentos.medicamentoOnco.catalog',

            'mezclas.medicamentos.diluyente',
            'mezclas.medicamentos.viaAdministracion',
        ])->findOrFail($solicitud->id);

        // ✅ Normalizar para la vista: usar snapshots primero
        $solicitud_onco->mezclas->each(function ($mezcla) {
            $mezcla->medicamentos->each(function ($mm) {

                // denominación
                $mm->denominacion_doc = $mm->denominacion_snapshot
                    ?? optional(optional($mm->medicamentoOnco)->catalog)->denominacion
                    ?? $mm->nombre_medicamento
                    ?? '—';

                // marca (si la guardas en mezcla_medicamentos)
                $mm->marca_doc = $mm->marca_snapshot ?? '—';

                // requires_infusor
                $mm->requires_infusor_doc = !is_null($mm->requires_infusor_snapshot)
                    ? (int) $mm->requires_infusor_snapshot
                    : (int) (optional(optional($mm->medicamentoOnco)->catalog)->requires_infusor ?? 0);

                // conc_min / conc_max
                $mm->conc_min_doc = $mm->conc_min_snapshot
                    ?? optional(optional($mm->medicamentoOnco)->catalog)->conc_min;

                $mm->conc_max_doc = $mm->conc_max_snapshot
                    ?? optional(optional($mm->medicamentoOnco)->catalog)->conc_max;
            });
        });

        // ✅ Hospital “oficial” del documento (snapshot primero)
        $hospitalDoc = optional($solicitud_onco->hospital)->name
            ?? optional(optional($solicitud_onco->user)->hospital)->name
            ?? '—';

        $fechaEnvio = now()->format('d/m/Y H:i');

        $pdf = Pdf::loadView('pdfs.oncologicos.envio', [
            'solicitud'   => $solicitud_onco,
            'mezclas'     => $solicitud_onco->mezclas,
            'fechaEnvio'  => $fechaEnvio,
            'hospitalDoc' => $hospitalDoc, // ✅ opcional en tu Blade
        ])->setPaper('letter', 'portrait');

        return $pdf->stream("envio-{$solicitud_onco->id}.pdf");
    }

    public function remision(SolicitudOnco $solicitud)
    {
        $solicitud_onco = SolicitudOnco::with([
            'hospital',
            'user.medicineList.distributor',
            'mezclas.infusor',

            'mezclas.medicamentos',
            'mezclas.medicamentos.presentacionesUsadas', // contiene snapshots por renglón
            // fallback viejo:
            'mezclas.medicamentos.medicamentoOnco.catalog',
            'mezclas.medicamentos.presentacionesUsadas.batch.presentation',

            'mezclas.medicamentos.diluyente',
        ])->findOrFail($solicitud->id);

        $distributor = optional(optional($solicitud_onco->user)->medicineList)->distributor;

        $lista       = optional($solicitud_onco->user)->medicineList;
        $listaCharge = $lista->charge_by ?? 'frasco';

        // Config por presentación (solo para fallback si no hay snapshot de precio)
        $cfgPorPresentacion = collect();
        if ($lista) {
            $cfgPorPresentacion = DB::table('medicine_list_presentation')
                ->where('medicine_list_id', $lista->id)
                ->get()
                ->keyBy('medicine_presentation_id');
        }

        $totalRemision = 0.0;

        foreach ($solicitud_onco->mezclas as $mezcla) {

            // =========================================================
            // 1) CALCULO MEDICAMENTOS (snapshot primero)
            // =========================================================
            foreach ($mezcla->medicamentos as $med) {

                // ✅ Nombre para PDF: snapshot -> catálogo -> nombre_medicamento
                $denom = $med->denominacion_snapshot
                    ?? optional(optional($med->medicamentoOnco)->catalog)->denominacion
                    ?? $med->nombre_medicamento
                    ?? '—';

                $marca = $med->marca_snapshot ?? null;
                $med->setAttribute('denominacion_doc', $denom);
                $med->setAttribute('marca_doc', $marca ?: '—');

                $presentaciones = $med->presentacionesUsadas ?? collect();

                $cantidad    = 0.0;
                $precioUnit  = 0.0;
                $subtotal    = 0.0;
                $unidadCobro = null;

                // ✅ 1A) SIN presentaciones usadas
                // (en remisión normalmente deberías traer presentaciones, pero lo dejamos robusto)
                if ($presentaciones->isEmpty()) {
                    $unidadCobro = $listaCharge === 'mg' ? 'mg' : 'frasco';
                    $cantidad    = $unidadCobro === 'mg' ? (float)($med->dosis ?? 0) : 1;

                    // si no hay presentaciones, no hay snapshot de precio por frasco
                    // y precio mg depende de lista -> fallback
                    if ($unidadCobro === 'mg') {
                        // fallback: precio mg de la lista (si existe)
                        // OJO: esto depende de lista viva, es solo para casos viejos sin snapshots.
                        $precioMgLista = 0.0;

                        // intenta inferir presentationId del primer batch (si existiera) — aquí no hay
                        $precioMgLista = 0.0;

                        $precioUnit = (float)$precioMgLista;
                        $subtotal   = $cantidad * $precioUnit;
                    } else {
                        $precioUnit = 0.0;
                        $subtotal   = 0.0;
                    }
                } else {

                    // ✅ 1B) CON presentaciones usadas: usar snapshot de subtotal/precio
                    // Si YA tienes subtotal en cada presentación usada: sumalo, y saca el unitario promedio.
                    $sumSubtotal = 0.0;
                    $sumUnidades = 0.0;

                    foreach ($presentaciones as $pu) {
                        $unidades = (float)($pu->unidades_usadas ?? 0);
                        if ($unidades <= 0) $unidades = 1;

                        // ✅ snapshot: subtotal del renglón (ya debe venir calculado al preparar)
                        $sub = $pu->subtotal;
                        if (!is_null($sub)) {
                            $sumSubtotal += (float)$sub;
                            $sumUnidades += $unidades;
                            continue;
                        }

                        // ✅ fallback: si no hay subtotal (registro viejo), intenta con precio_frasco_snapshot
                        $precioSnap = $pu->precio_frasco_snapshot;
                        if (!is_null($precioSnap)) {
                            $sumSubtotal += ((float)$precioSnap * $unidades);
                            $sumUnidades += $unidades;
                            continue;
                        }

                        // ✅ fallback final: lista viva por presentationId
                        $pid =
                            optional($pu->batch)->medicine_presentation_id
                            ?? optional(optional($pu->batch)->presentation)->id
                            ?? optional($pu->presentation)->id;

                        $cfgPres = $pid ? $cfgPorPresentacion->get($pid) : null;
                        $precioFrascoLista = (float)($cfgPres->precio ?? 0);

                        $sumSubtotal += ($precioFrascoLista * $unidades);
                        $sumUnidades += $unidades;
                    }

                    // Si tu lista cobra por mg, podrías calcular mg con dosis y precio mg override,
                    // pero en tu flujo de preparación normalmente cobras por frasco.
                    // Mantengo tu lógica original: deducimos unidad según cfg/lista,
                    // pero si hay snapshots, forzamos a "frasco" porque el subtotal viene por frascos usados.
                    // Si en tu sistema también preparas por mg, ajustamos en base a charge_by de snapshot (si lo guardas).
                    $unidadCobro = 'frasco';
                    $cantidad    = $sumUnidades;
                    $subtotal    = $sumSubtotal;
                    $precioUnit  = $sumUnidades > 0 ? ($sumSubtotal / $sumUnidades) : 0.0;
                }

                $precioUnit = round($precioUnit, 4);
                $subtotal   = round($subtotal, 2);

                $med->setAttribute('unidad_cobro', $unidadCobro);
                $med->setAttribute('cantidad_cobro', $cantidad);
                $med->setAttribute('precio_unitario_calculado', $precioUnit);
                $med->setAttribute('subtotal_calculado', $subtotal);

                $totalRemision += $subtotal;
            }

            // =========================================================
            // 2) SUMAR INFUSOR SI APLICA (snapshot primero)
            // =========================================================

            $mezclaUsaInfusor = (bool)($mezcla->set_infusion ?? false) || !empty($mezcla->infusor_id);

            // ✅ snapshot: si cualquier medicamento trae requires_infusor_snapshot = 1, aplica
            // fallback: catálogo vivo si snapshot null
            $requierePorRegla = $mezcla->medicamentos->contains(function ($med) {
                if (!is_null($med->requires_infusor_snapshot)) {
                    return (int)$med->requires_infusor_snapshot === 1;
                }
                return (int)(optional(optional($med->medicamentoOnco)->catalog)->requires_infusor ?? 0) === 1;
            });

            $infusorAplica = $mezclaUsaInfusor && $requierePorRegla && !empty($mezcla->infusor);

            if ($infusorAplica) {
                $precioInfusor = (float)($mezcla->infusor->precio ?? 0);

                $mezcla->setAttribute('infusor_aplica', true);
                $mezcla->setAttribute(
                    'infusor_nombre',
                    trim(($mezcla->infusor->nombre_generico ?? '') . ' ' . ($mezcla->infusor->nombre_comercial ?? '')) ?: 'Infusor'
                );
                $mezcla->setAttribute('infusor_precio', round($precioInfusor, 2));
                $mezcla->setAttribute('infusor_subtotal', round($precioInfusor, 2));

                $totalRemision += round($precioInfusor, 2);
            } else {
                $mezcla->setAttribute('infusor_aplica', false);
                $mezcla->setAttribute('infusor_precio', 0);
                $mezcla->setAttribute('infusor_subtotal', 0);
            }
        }

        $pdf = Pdf::loadView('pdfs.oncologicos.remision', [
            'solicitud'     => $solicitud_onco,
            'mezclas'       => $solicitud_onco->mezclas,
            'fechaEmision'  => now(),
            'totalRemision' => round($totalRemision, 2),
            'distributor'   => $distributor,
        ])->setPaper('letter', 'portrait');

        return $pdf->stream("remision-{$solicitud_onco->id}.pdf");
    }




    public function exportarExcel()
    {
        return Excel::download(new OncologicosSolicitudesOncoExport, 'solicitudes_oncologicas.xlsx');
    }
}
