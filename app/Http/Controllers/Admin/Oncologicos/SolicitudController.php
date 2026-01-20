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
        $solicitudes = SolicitudOnco::with(['user.hospital'])
            ->orderByDesc('id')
            ->get();


        return view('admin.oncologicos.solicitudes.index', compact('solicitudes'));
    }

    public function create()
    {
        $user    = Auth::user();
        $listaId = $user->medicine_list_id; // puede ser null

        // 1) Catálogo genérico + precios (lista > onco > 0)
        $medicamentos = DB::table('medicines_catalog as mc')
            ->leftJoin('medicine_oncos as mo', 'mo.catalog_id', '=', 'mc.id')
            ->leftJoin('medicine_medicine_lists as mml', function ($join) use ($listaId) {
                $join->on('mml.medicine_id', '=', 'mo.id');
                // si hay lista, se usa solo para el override de precio
                if ($listaId) {
                    $join->where('mml.medicine_list_id', '=', $listaId);
                }
            })
            ->where('mc.state', true) // solo genéricos activos
            ->select(
                // 👇 ESTE id es el que usará el front (catálogo genérico)
                'mc.id as id',
                'mc.denominacion',
                'mc.denominacion_comercial',
                'mc.requires_infusor',
                // por si lo necesitas después
                'mo.id as medicine_onco_id',
                // precio elegido: lista > onco > 0
                DB::raw('COALESCE(mml.precio, mo.precio, 0) as precio')
            )
            ->orderBy('mc.denominacion')
            ->get()
            ->map(function ($m) {
                $m->requires_infusor = (int) ($m->requires_infusor ?? 0);
                return $m;
            });

        // 2) infoAdicional por catálogo (llave = mc.id)
        $infoAdicional = [];
        foreach ($medicamentos as $med) {
            // importante: aquí usamos el ID de catálogo (mc.id) -> $med->id
            $diluyentes = DB::table('diluent_medicine_catalog')
                ->join('diluents', 'diluent_medicine_catalog.diluent_id', '=', 'diluents.id')
                ->where('diluent_medicine_catalog.medicine_catalog_id', $med->id)
                ->select(
                    'diluents.id',
                    DB::raw('diluents.denominacion_generica as name')
                )
                ->get();

            $vias = DB::table('administration_route_medicine_catalog')
                ->join('administration_routes', 'administration_route_medicine_catalog.administration_route_id', '=', 'administration_routes.id')
                ->where('administration_route_medicine_catalog.medicine_catalog_id', $med->id)
                ->select('administration_routes.id', 'administration_routes.name')
                ->get();

            // 🔑 clave = id de catálogo (coincide con <option value="..."> del select)
            $infoAdicional[$med->id] = [
                'diluyentes'       => $diluyentes,
                'vias'             => $vias,
                'requires_infusor' => (int) $med->requires_infusor,
            ];
        }

        // 3) Infusores activos
        $infusors = DB::table('infusors')
            ->select('id', 'nombre_generico', 'nombre_comercial')
            ->where('is_active', true)
            ->orderBy('nombre_generico')
            ->orderBy('nombre_comercial')
            ->get();

        return view('admin.oncologicos.solicitudes.create', [
            'medicamentos'  => $medicamentos,
            'infoAdicional' => $infoAdicional,
            'infusors'      => $infusors,
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

                    MezclaMedicamento::create([
                        'mezcla_id'             => $mezcla->id,
                        'medicamento_id'        => $medicineOnco->id, // 👈 se guarda el ID de medicine_oncos
                        'nombre_medicamento'    => $medicamento['nombre'],
                        'dosis'                 => $dosis,
                        'dosis_ml'              => $dosisML,
                        'diluyente_id'          => $medicamento['diluyente_id'] ?? null,
                        'via_administracion_id' => $medicamento['via_administracion_id'] ?? null,
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
        $solicitud = SolicitudOnco::with(['mezclas.medicamentos'])->findOrFail($id);

        $user    = Auth::user();
        $listaId = $user->medicine_list_id; // puede ser null

        // =========================
        // 1) Base query genérica
        // =========================
        $baseQuery = DB::table('medicine_oncos as mo')
            ->join('medicines_catalog as mc', 'mo.catalog_id', '=', 'mc.id')
            ->select(
                'mo.id as id',
                'mc.denominacion',
                DB::raw('mc.denominacion_comercial as presentacion'),
                'mc.id as catalog_id',
                'mc.requires_infusor as requires_infusor',
                DB::raw('NULL as lote'),
                DB::raw('NULL as caducidad')
            );

        // =========================
        // 2) Intentar usar la lista del usuario
        // =========================
        if ($listaId) {
            $medicamentos = (clone $baseQuery)
                ->join('medicine_medicine_lists as mml', function ($join) use ($listaId) {
                    $join->on('mml.medicine_id', '=', 'mo.id')
                        ->where('mml.medicine_list_id', '=', $listaId);
                })
                ->addSelect(DB::raw('mml.precio as precio'))
                ->get();
        } else {
            $medicamentos = collect();
        }

        // =========================
        // 3) Si la lista está vacía, fallback a TODOS los genéricos
        // =========================
        if ($medicamentos->isEmpty()) {
            $medicamentos = (clone $baseQuery)
                ->addSelect(DB::raw('mo.precio as precio'))
                ->get();
        }

        // Normalizar requires_infusor
        $medicamentos = $medicamentos->map(function ($m) {
            $m->requires_infusor = (int) ($m->requires_infusor ?? 0);
            return $m;
        });

        // =========================
        // 4) Info adicional (diluyentes + vías) por catálogo
        // =========================
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
                'diluyentes'       => $diluyentes,
                'vias'             => $vias,
                'requires_infusor' => (int) $med->requires_infusor,
            ];
        }

        // =========================
        // 5) Infusores activos
        // =========================
        $infusors = DB::table('infusors')
            ->select('id', 'nombre_generico', 'nombre_comercial')
            ->where('is_active', true)
            ->orderBy('nombre_generico')
            ->orderBy('nombre_comercial')
            ->get();

        // =========================
        // 6) Enviar a la vista
        // =========================
        return view('admin.oncologicos.solicitudes.edit', [
            'solicitud'     => $solicitud,
            'medicamentos'  => $medicamentos,
            'infoAdicional' => $infoAdicional,
            'infusors'      => $infusors,
        ]);
    }





    public function update(Request $request, $id)
    {

        $request->validate([
            'paciente_nombre'  => 'required|string|max:255',
            'servicio'         => 'required|string|max:255',
            'registro'         => 'required|string|max:255',
            'sexo'             => 'required|in:M,F',

            // 👇 MISMA VALIDACIÓN DE EDAD QUE EN STORE
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

            // 👇 Entrega no puede ser antes de hoy
            'fecha_entrega'    => 'required|date|after_or_equal:today',

            'observaciones'    => 'nullable|string|max:500',
            'mezclas'          => 'required|string',
        ], [
            // ✨ Mensajes de error amigables
            'fecha_nacimiento.after'  => 'La fecha de nacimiento no puede ser mayor a 100 años.',
            'fecha_nacimiento.before' => 'La fecha de nacimiento debe ser anterior a hoy.',
            'fecha_entrega.after_or_equal' => 'La fecha de entrega no puede ser anterior a hoy.',
        ]);

        $mezclas = json_decode($request->mezclas, true);
        if (!is_array($mezclas)) {
            return back()->withErrors(['mezclas' => 'El formato del campo mezclas no es válido.'])->withInput();
        }

        // Particionamos: existentes (traen bandera "existente") y nuevas (no la traen)
        $mezclasExistentesPayload = array_filter($mezclas, fn($m) => !empty($m['existente']));
        $nuevasMezclas            = array_filter($mezclas, fn($m) => empty($m['existente']));

        DB::beginTransaction();
        try {
            $solicitud = SolicitudOnco::with(['mezclas.medicamentos'])->findOrFail($id);

            // 1) Actualizar datos básicos de la solicitud
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
                'fecha_entrega'     => $request->fecha_entrega,
                'observaciones'     => $request->observaciones,
                'nombre_medico'     => $request->medico_nombre,
                'cedula_medico'     => $request->medico_cedula,
            ]);

            // 2) Actualizar set/infusor de mezclas EXISTENTES
            foreach ($mezclasExistentesPayload as $idx => $payload) {
                $mezclaModelo = $solicitud->mezclas[$idx] ?? null;
                if (!$mezclaModelo) {
                    continue;
                }

                $setInfusion = !empty($payload['set_infusion']) ? (bool)$payload['set_infusion'] : false;
                $infusorId   = !empty($payload['infusor_id']) ? (int)$payload['infusor_id'] : null;

                if ($setInfusion && $infusorId) {
                    throw new \Exception("Mezcla #" . ($idx + 1) . ": selecciona set de infusión o un infusor, no ambos.");
                }

                // Si trae infusor, validar que exista/activo y que al menos un medicamento admita infusor
                if ($infusorId) {
                    $infusor = DB::table('infusors')->where('id', $infusorId)->where('is_active', true)->first();
                    if (!$infusor) {
                        throw new \Exception("Mezcla #" . ($idx + 1) . ": el infusor seleccionado no existe o no está activo.");
                    }

                    $hayMedQueRequiereInfusor = $mezclaModelo->medicamentos->contains(function ($mm) {
                        $row = DB::table('medicine_oncos as mo')
                            ->join('medicines_catalog as mc', 'mo.catalog_id', '=', 'mc.id')
                            ->where('mo.id', $mm->medicamento_id)
                            ->select('mc.requires_infusor')
                            ->first();
                        return $row && (int)$row->requires_infusor === 1;
                    });

                    if (!$hayMedQueRequiereInfusor) {
                        throw new \Exception("Mezcla #" . ($idx + 1) . ": para seleccionar un infusor, la mezcla debe contener al menos un medicamento que lo admita.");
                    }
                }

                $mezclaModelo->update([
                    'set_infusion' => $setInfusion,
                    'infusor_id'   => $infusorId,
                ]);
            }

            // 3) Crear NUEVAS mezclas (igual lógica que en store)
            $mezclasExistentes = $solicitud->mezclas; // colección Eloquent

            foreach ($nuevasMezclas as $index => $mezclaData) {
                $volumen = isset($mezclaData['volumen_dilucion']) ? floatval($mezclaData['volumen_dilucion']) : null;
                $tiempo  = $mezclaData['tiempo_infusion'] ?? null;

                if ($volumen === null || $volumen <= 0) {
                    throw new \Exception("La nueva mezcla #" . ($index + 1) . " requiere un volumen de dilución válido (> 0).");
                }
                if ($tiempo === null || $tiempo === '') {
                    throw new \Exception("La nueva mezcla #" . ($index + 1) . " requiere un tiempo de infusión.");
                }

                $setInfusion = !empty($mezclaData['set_infusion']) ? (bool)$mezclaData['set_infusion'] : false;
                $infusorId   = !empty($mezclaData['infusor_id']) ? (int)$mezclaData['infusor_id'] : null;

                if ($setInfusion && $infusorId) {
                    throw new \Exception("Nueva mezcla #" . ($index + 1) . ": selecciona set de infusión o un infusor, no ambos.");
                }

                $meds = $mezclaData['medicamentos'] ?? [];
                if (!is_array($meds) || count($meds) === 0) {
                    throw new \Exception("La nueva mezcla #" . ($index + 1) . " no contiene medicamentos.");
                }

                // Evitar duplicar una mezcla idéntica ya existente
                $coincidencia = $mezclasExistentes->first(function ($mezclaExistente) use ($mezclaData) {
                    $coincidenMedicamentos = $mezclaExistente->medicamentos->pluck('medicamento_id')->sort()->values()->all() ===
                        collect($mezclaData['medicamentos'])->pluck('medicamento_id')->sort()->values()->all();

                    return $mezclaExistente->volumen_dilucion == $mezclaData['volumen_dilucion']
                        && $mezclaExistente->tiempo_infusion == $mezclaData['tiempo_infusion']
                        && $mezclaExistente->medicamentos->count() === count($mezclaData['medicamentos'])
                        && $coincidenMedicamentos;
                });
                if ($coincidencia) {
                    continue;
                }

                // Validación diluyente/vía iguales y requires_infusor cuando hay infusor
                $diluyenteRef = null;
                $viaRef = null;
                $hayMedQueRequiereInfusor = false;

                foreach ($meds as $i => $medicamento) {
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

                    $mo = DB::table('medicine_oncos as mo')
                        ->join('medicines_catalog as mc', 'mo.catalog_id', '=', 'mc.id')
                        ->where('mo.id', $medicamento['medicamento_id'])
                        ->select('mc.requires_infusor', 'mc.denominacion', 'mc.conc_min', 'mc.conc_max', 'mc.volumen_diluyente', 'mc.cantidad_medicamento')
                        ->first();

                    if (!$mo) {
                        throw new \Exception("No se encontró información del catálogo para el medicamento ID {$medicamento['medicamento_id']}.");
                    }
                    if ((int)$mo->requires_infusor === 1) {
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

                // Guardar medicamentos (concentración y dosis_ml)
                foreach ($meds as $medicamento) {
                    $medicine = MedicineOnco::with('catalog')->find($medicamento['medicamento_id']);
                    if (!$medicine || !$medicine->catalog) {
                        throw new \Exception("No se encontró información del catálogo para el medicamento ID {$medicamento['medicamento_id']}.");
                    }

                    $catalog = $medicine->catalog;

                    $dosis = isset($medicamento['dosis']) ? floatval($medicamento['dosis']) : 0;
                    $concentracion = $volumen > 0 ? $dosis / $volumen : 0;

                    if ($concentracion < $catalog->conc_min || $concentracion > $catalog->conc_max) {
                        throw new \Exception(
                            "La concentración de '{$catalog->denominacion}' está fuera del rango permitido ({$catalog->conc_min} - {$catalog->conc_max} mL). Dosis: {$dosis}, Volumen: {$volumen}."
                        );
                    }

                    $dosisML = null;
                    if ($catalog->cantidad_medicamento > 0) {
                        $dosisML = ($dosis * $catalog->volumen_diluyente) / $catalog->cantidad_medicamento;
                    }

                    MezclaMedicamento::create([
                        'mezcla_id'             => $mezcla->id,
                        'medicamento_id'        => $medicamento['medicamento_id'],
                        'nombre_medicamento'    => $medicamento['nombre'],
                        'dosis'                 => $dosis,
                        'dosis_ml'              => $dosisML,
                        'diluyente_id'          => $medicamento['diluyente_id'] ?? null,
                        'via_administracion_id' => $medicamento['via_administracion_id'] ?? null,
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
            'mezclas.medicamentos.medicamentoOnco.catalog',
            'mezclas.medicamentos.diluyente',
            'mezclas.medicamentos.viaAdministracion'
        ])->findOrFail($solicitud->id);

        $pdf = Pdf::loadView('pdfs.oncologicos.solicitud', [
            'solicitud' => $solicitud,
        ]);

        return $pdf->stream();
    }


    # PDF para solicitud de mezcla oncologicas
    public function envio(SolicitudOnco $solicitud)
    {
        // Eager load solo lo necesario en Onco
        $solicitud_onco = SolicitudOnco::with([
            'user.hospital',
            'mezclas.medicamentos.medicamentoOnco.catalog',
            'mezclas.medicamentos.diluyente',
            'mezclas.medicamentos.viaAdministracion',
        ])->findOrFail($solicitud->id);

        // Si en algún momento guardas explícitamente fecha/hora de preparación en onco,
        // cámbialo aquí. Por ahora tomamos created_at de cada mezcla.
        $fechaEnvio = now()->format('d/m/Y H:i');

        $pdf = Pdf::loadView('pdfs.oncologicos.envio', [
            'solicitud'  => $solicitud_onco,
            'mezclas'    => $solicitud_onco->mezclas,
            'fechaEnvio' => $fechaEnvio,
        ])->setPaper('letter', 'portrait');

        // return ([
        //     'solicitud' => $solicitud_onco,
        //     'mezclas' => $solicitud_onco->mezclas,
        //     'fechaEnvio' => $fechaEnvio,
        // ]);

        return $pdf->stream();
    }

    public function remision(SolicitudOnco $solicitud)
    {
        $solicitud_onco = SolicitudOnco::with([
            'user.hospital',
            'user.medicineList.distributor',
            'mezclas.infusor', // ✅ para precio del infusor
            'mezclas.medicamentos.medicamentoOnco.catalog',
            'mezclas.medicamentos.diluyente',
            'mezclas.medicamentos.presentacionesUsadas.batch.presentation',
        ])->findOrFail($solicitud->id);

        $distributor = optional(optional($solicitud_onco->user)->medicineList)->distributor;

        // ✅ Lista de precios asignada al usuario
        $lista       = optional($solicitud_onco->user)->medicineList;
        $listaCharge = $lista->charge_by ?? 'frasco';

        // ✅ Config por presentación (medicine_list_presentation)
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
            // ✅ 1) CALCULO MEDICAMENTOS (lo tuyo tal cual)
            // =========================================================
            foreach ($mezcla->medicamentos as $med) {

                $presentaciones = $med->presentacionesUsadas ?? collect();

                $cantidad    = 0.0;
                $precioUnit  = 0.0;
                $subtotal    = 0.0;
                $unidadCobro = null;

                if ($presentaciones->isEmpty()) {
                    $unidadCobro = $listaCharge === 'mg' ? 'mg' : 'frasco';
                    $cantidad    = $unidadCobro === 'mg' ? (float)($med->dosis ?? 0) : 1;
                    $precioUnit  = 0.0;
                    $subtotal    = 0.0;
                } else {

                    $first = $presentaciones->first();

                    $presentationId =
                        optional($first->batch)->medicine_presentation_id
                        ?? optional(optional($first->batch)->presentation)->id
                        ?? optional($first->presentation)->id;

                    $cfg = $presentationId ? $cfgPorPresentacion->get($presentationId) : null;

                    $chargeBy = $cfg->charge_by ?? $listaCharge;
                    $unidadCobro = $chargeBy === 'mg' ? 'mg' : 'frasco';

                    if ($chargeBy === 'frasco') {

                        $subtotal = 0.0;
                        $unidadesTotales = 0.0;

                        foreach ($presentaciones as $pu) {

                            $pid =
                                optional($pu->batch)->medicine_presentation_id
                                ?? optional(optional($pu->batch)->presentation)->id
                                ?? optional($pu->presentation)->id;

                            $cfgPres = $pid ? $cfgPorPresentacion->get($pid) : null;

                            $precioFrascoLista = (float)($cfgPres->precio ?? 0);
                            $unidades = (float)($pu->unidades_usadas ?? 0);
                            if ($unidades <= 0) $unidades = 1;

                            $unidadesTotales += $unidades;
                            $subtotal += ($precioFrascoLista * $unidades);
                        }

                        $cantidad = $unidadesTotales;
                        $precioUnit = $unidadesTotales > 0 ? ($subtotal / $unidadesTotales) : 0.0;
                    } else {

                        $dosis = (float)($med->dosis ?? 0);
                        $precioMgLista = (float)($cfg->precio_mg_override ?? 0);

                        $cantidad   = $dosis;
                        $precioUnit = $precioMgLista;
                        $subtotal   = $cantidad * $precioUnit;
                    }
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
            // ✅ 2) SUMAR INFUSOR SI SE REQUIERE (por mezcla)
            // =========================================================

            // A) ¿La mezcla usa infusor? (por flag set_infusion o por si trae infusor_id)
            $mezclaUsaInfusor = (bool)($mezcla->set_infusion ?? false) || !empty($mezcla->infusor_id);

            // B) ¿Algún medicamento requiere infusor? (requires_infusor del catálogo)
            $requierePorCatalogo = false;
            foreach ($mezcla->medicamentos as $med) {
                $requires = (bool) optional(optional($med->medicamentoOnco)->catalog)->requires_infusor;
                if ($requires) {
                    $requierePorCatalogo = true;
                    break;
                }
            }

            // C) Regla final
            $infusorAplica = $mezclaUsaInfusor && $requierePorCatalogo && !empty($mezcla->infusor);

            if ($infusorAplica) {
                $precioInfusor = (float)($mezcla->infusor->precio ?? 0);

                // Guardamos datos para la vista
                $mezcla->setAttribute('infusor_aplica', true);
                $mezcla->setAttribute('infusor_nombre', trim(($mezcla->infusor->nombre_generico ?? '') . ' ' . ($mezcla->infusor->nombre_comercial ?? '')) ?: 'Infusor');
                $mezcla->setAttribute('infusor_precio', round($precioInfusor, 2));
                $mezcla->setAttribute('infusor_subtotal', round($precioInfusor, 2));

                // Sumamos al total
                $totalRemision += round($precioInfusor, 2);
            } else {
                // Para que la vista no truene
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

        return $pdf->stream();
    }



    public function exportarExcel()
    {
        return Excel::download(new OncologicosSolicitudesOncoExport, 'solicitudes_oncologicas.xlsx');
    }
}
