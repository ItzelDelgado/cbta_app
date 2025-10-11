<?php

namespace App\Http\Controllers\Admin\Oncologicos;

use App\Http\Controllers\Controller;
use App\Models\Oncologicos\MedicineOnco;
use App\Models\Oncologicos\Mezcla;
use App\Models\Oncologicos\MezclaMedicamento;
use App\Models\Oncologicos\SolicitudOnco;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

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

        // Base query
        $query = DB::table('medicine_oncos as mo')
            ->leftJoin('medicine_medicine_lists as mml', function ($join) use ($listaId) {
                $join->on('mml.medicine_id', '=', 'mo.id');
                if ($listaId) {
                    $join->where('mml.medicine_list_id', '=', $listaId);
                }
            })
            ->join('medicines_catalog as mc', 'mo.catalog_id', '=', 'mc.id')
            ->select(
                'mo.id as id',
                DB::raw('COALESCE(mml.precio, mo.precio) as precio'),
                'mc.lote',
                'mc.caducidad',
                'mc.denominacion',
                'mc.presentacion',
                'mc.id as catalog_id',
                // 👇 NUEVO: bandera para habilitar infusor en front
                'mc.requires_infusor as requires_infusor'
            );

        // Si hay lista, limita a los de la lista
        if ($listaId) {
            $query->where('mml.medicine_list_id', $listaId);
        }

        // Traemos medicamentos y normalizamos requires_infusor a int
        $medicamentos = $query->get()->map(function ($m) {
            $m->requires_infusor = (int) ($m->requires_infusor ?? 0);
            return $m;
        });

        // Cargar diluyentes y vías por catálogo
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
                'diluyentes'        => $diluyentes,
                'vias'              => $vias,
                // 👇 Redundancia útil por si en JS lees esta clave
                'requires_infusor'  => (int) $med->requires_infusor,
            ];
        }

        // 👇 NUEVO: lista de infusores activos para el <select>
        $infusors = DB::table('infusors')
            ->select('id', 'nombre_generico', 'nombre_comercial')
            ->where('is_active', true)
            ->orderBy('nombre_generico')
            ->orderBy('nombre_comercial')
            ->get();

        return view('admin.oncologicos.solicitudes.create', [
            'medicamentos'  => $medicamentos,
            'infoAdicional' => $infoAdicional,
            'infusors'      => $infusors,   // 👈 necesario para el front
        ]);
    }



    public function store(Request $request)
    {

        // Validación general del formulario (igual a la tuya)
        $request->validate([
            'paciente_nombre' => 'required|string|max:255',
            'servicio'        => 'required|string|max:255',
            'registro'        => 'required|string|max:255',
            'sexo'            => 'required|in:M,F',
            'fecha_nacimiento' => 'required|date',
            'peso'            => 'required|numeric|min:1',
            'piso'            => 'required|string|max:50',
            'cama'            => 'required|string|max:50',
            'diagnostico'     => 'required|string|max:255',
            'medico_nombre'   => 'required|string|max:255',
            'medico_cedula'   => 'required|string|max:255',
            'fecha_entrega'   => 'required|date',
            'observaciones'   => 'nullable|string|max:500',
            'mezclas'         => 'required|string',
        ]);

        $mezclas = json_decode($request->mezclas, true);
        if (!is_array($mezclas)) {
            return back()->withErrors(['mezclas' => 'El formato del campo mezclas no es válido.'])->withInput();
        }

        DB::beginTransaction();
        try {
            $user = auth()->user();

            // Precios personalizados por lista del usuario
            $precios = DB::table('medicine_medicine_lists')
                ->where('medicine_list_id', $user->medicine_list_id)
                ->pluck('precio', 'medicine_id');

            // Crear solicitud principal
            $solicitud = SolicitudOnco::create([
                'user_id'         => $user->id,
                'servicio'        => $request->servicio,
                'nombre_paciente' => $request->paciente_nombre,
                'sexo'            => $request->sexo,
                'edad'            => null,
                'peso'            => $request->peso,
                'cama'            => $request->cama,
                'piso'            => $request->piso,
                'registro_paciente' => $request->registro,
                'fecha_nacimiento' => $request->fecha_nacimiento,
                'diagnostico'     => $request->diagnostico,
                'fecha_entrega'   => $request->fecha_entrega,
                'observaciones'   => $request->observaciones,
                'nombre_medico'   => $request->medico_nombre,
                'cedula_medico'   => $request->medico_cedula,
                'estado'          => 'pendiente',
                'remision'        => null,
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

                // Regla de exclusión (mutuamente excluyentes, pero ambos opcionales)
                if ($setInfusion && $infusorId) {
                    throw new \Exception("Mezcla #" . ($index + 1) . ": selecciona set de infusión o un infusor, no ambos.");
                }
                // Si deseas "exactamente uno", descomenta la siguiente línea:
                // if (!$setInfusion && !$infusorId) throw new \Exception("Mezcla #" . ($index + 1) . ": debes seleccionar set de infusión o un infusor.");

                // Validación de que todos los meds compartan diluyente y vía
                $meds = $mezclaData['medicamentos'] ?? [];
                if (!is_array($meds) || count($meds) === 0) {
                    throw new \Exception("La Mezcla #" . ($index + 1) . " no contiene medicamentos.");
                }

                $diluyenteRef = null;
                $viaRef = null;

                // Si viene infusor, verificar: al menos 1 medicamento con requires_infusor = 1
                $hayMedQueRequiereInfusor = false;

                foreach ($meds as $i => $medicamento) {
                    $diluyente = $medicamento['diluyente_id'] ?? null;
                    $via       = $medicamento['via_administracion_id'] ?? null;

                    if ($i === 0) {
                        $diluyenteRef = $diluyente;
                        $viaRef       = $via;
                    } else {
                        if ($diluyente !== $diluyenteRef || $via !== $viaRef) {
                            throw new \Exception("Mezcla #" . ($index + 1) . ": todos los medicamentos deben tener el mismo diluyente y la misma vía de administración.");
                        }
                    }

                    // Verificar requires_infusor
                    $mo = DB::table('medicine_oncos as mo')
                        ->join('medicines_catalog as mc', 'mo.catalog_id', '=', 'mc.id')
                        ->where('mo.id', $medicamento['medicamento_id'])
                        ->select('mc.requires_infusor', 'mc.denominacion', 'mc.conc_min', 'mc.conc_max', 'mc.volumen_diluyente', 'mc.cantidad_medicamento')
                        ->first();

                    if (!$mo) {
                        throw new \Exception("No se encontró información del catálogo para el medicamento ID {$medicamento['medicamento_id']}.");
                    }

                    // Si hay infusor seleccionado, al menos un medicamento debe admitirlo
                    if ((int)$mo->requires_infusor === 1) {
                        $hayMedQueRequiereInfusor = true;
                    }
                }

                if ($infusorId) {
                    // Debe existir y estar activo
                    $infusor = DB::table('infusors')->where('id', $infusorId)->where('is_active', true)->first();
                    if (!$infusor) {
                        throw new \Exception("Mezcla #" . ($index + 1) . ": el infusor seleccionado no existe o no está activo.");
                    }
                    if (!$hayMedQueRequiereInfusor) {
                        throw new \Exception("Mezcla #" . ($index + 1) . ": para seleccionar un infusor, la mezcla debe contener al menos un medicamento que lo admita.");
                    }
                }

                // Crear la mezcla (ahora con set_infusion e infusor_id)
                $mezcla = Mezcla::create([
                    'solicitud_id'    => $solicitud->id,
                    'volumen_dilucion' => $volumen,
                    'tiempo_infusion' => $tiempo,
                    'estado'          => 'pendiente',
                    'set_infusion'    => $setInfusion,
                    'infusor_id'      => $infusorId,
                ]);

                // Guardar medicamentos de la mezcla
                foreach ($meds as $medicamento) {
                    $medicine = MedicineOnco::with('catalog')->find($medicamento['medicamento_id']);
                    if (!$medicine || !$medicine->catalog) {
                        throw new \Exception("No se encontró información del catálogo para el medicamento ID {$medicamento['medicamento_id']}.");
                    }

                    $catalog = $medicine->catalog;

                    // Validar concentración permitida
                    $dosis = isset($medicamento['dosis']) ? floatval($medicamento['dosis']) : 0;
                    $concentracion = $volumen > 0 ? $dosis / $volumen : 0;

                    if ($concentracion < $catalog->conc_min || $concentracion > $catalog->conc_max) {
                        throw new \Exception(
                            "La concentración de '{$catalog->denominacion}' está fuera del rango permitido ({$catalog->conc_min} - {$catalog->conc_max} mL). Dosis: {$dosis}, Volumen: {$volumen}."
                        );
                    }

                    // Calcular dosis en mL: (dosis * volumen_diluyente) / cantidad_medicamento
                    $dosisML = null;
                    if ($catalog->cantidad_medicamento > 0) {
                        $dosisML = ($dosis * $catalog->volumen_diluyente) / $catalog->cantidad_medicamento;
                    }

                    $precioUnit = $precios[$medicamento['medicamento_id']] ?? 0;

                    MezclaMedicamento::create([
                        'mezcla_id'             => $mezcla->id,
                        'medicamento_id'        => $medicamento['medicamento_id'],
                        'nombre_medicamento'    => $medicamento['nombre'],
                        'dosis'                 => $dosis,
                        'dosis_ml'              => $dosisML,
                        'diluyente_id'          => $medicamento['diluyente_id'] ?? null,
                        'via_administracion_id' => $medicamento['via_administracion_id'] ?? null,
                        'precio_unitario'       => $precioUnit,
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
        $listaId = $user->medicine_list_id;

        // Medicamentos de la lista del usuario
        $medicamentos = DB::table('medicine_medicine_lists as mml')
            ->join('medicine_oncos as mo', 'mml.medicine_id', '=', 'mo.id')
            ->join('medicines_catalog as mc', 'mo.catalog_id', '=', 'mc.id')
            ->where('mml.medicine_list_id', $listaId)
            ->select(
                'mo.id as id',
                'mml.precio as precio', // precio desde pivot
                DB::raw('NULL as lote'),      // alias para mantener compatibilidad en la vista
                DB::raw('NULL as caducidad'), // alias para mantener compatibilidad en la vista
                'mc.denominacion',
                'mc.presentacion',
                'mc.id as catalog_id',
                // NUEVO: bandera para habilitar infusor en front
                'mc.requires_infusor as requires_infusor'
            )
            ->get()
            ->map(function ($m) {
                $m->requires_infusor = (int) ($m->requires_infusor ?? 0);
                return $m;
            });

        // Si quisieras permitir fallback al precio base cuando no hay en la lista, usa este SELECT en lugar del de arriba:
        /*
    $medicamentos = DB::table('medicine_oncos as mo')
        ->leftJoin('medicine_medicine_lists as mml', function ($join) use ($listaId) {
            $join->on('mml.medicine_id', '=', 'mo.id')
                 ->where('mml.medicine_list_id', '=', $listaId);
        })
        ->join('medicines_catalog as mc', 'mo.catalog_id', '=', 'mc.id')
        ->select(
            'mo.id as id',
            DB::raw('COALESCE(mml.precio, mo.precio) as precio'),
            DB::raw('NULL as lote'),
            DB::raw('NULL as caducidad'),
            'mc.denominacion',
            'mc.presentacion',
            'mc.id as catalog_id',
            'mc.requires_infusor as requires_infusor'
        )
        ->get()
        ->map(function ($m) {
            $m->requires_infusor = (int) ($m->requires_infusor ?? 0);
            return $m;
        });
    */

        // Info adicional por medicamento (diluyentes y vías)
        $infoAdicional = [];
        foreach ($medicamentos as $med) {
            $diluyentes = DB::table('diluent_medicine_catalog')
                ->join('diluents', 'diluent_medicine_catalog.diluent_id', '=', 'diluents.id')
                ->where('diluent_medicine_catalog.medicine_catalog_id', $med->catalog_id)
                ->select(
                    'diluents.id',
                    DB::raw('diluents.denominacion_generica as name') // igual que en create
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
                // útil en el front si decides leer desde aquí
                'requires_infusor' => (int) $med->requires_infusor,
            ];
        }

        // NUEVO: lista de infusores activos para el <select>
        $infusors = DB::table('infusors')
            ->select('id', 'nombre_generico', 'nombre_comercial')
            ->where('is_active', true)
            ->orderBy('nombre_generico')
            ->orderBy('nombre_comercial')
            ->get();

        return view('admin.oncologicos.solicitudes.edit', [
            'solicitud'     => $solicitud,
            'medicamentos'  => $medicamentos,
            'infoAdicional' => $infoAdicional,
            'infusors'      => $infusors, // <-- importante para el script
        ]);
    }


    public function update(Request $request, $id)
    {
        
        $request->validate([
            'paciente_nombre'  => 'required|string|max:255',
            'servicio'         => 'required|string|max:255',
            'registro'         => 'required|string|max:255',
            'sexo'             => 'required|in:M,F',
            'fecha_nacimiento' => 'required|date',
            'peso'             => 'required|numeric|min:1',
            'piso'             => 'required|string|max:50',
            'cama'             => 'required|string|max:50',
            'diagnostico'      => 'required|string|max:255',
            'medico_nombre'    => 'required|string|max:255',
            'medico_cedula'    => 'required|string|max:255',
            'fecha_entrega'    => 'required|date',
            'observaciones'    => 'nullable|string|max:500',
            'mezclas'          => 'required|string'
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

            $user = auth()->user();
            $precios = DB::table('medicine_medicine_lists')
                ->where('medicine_list_id', $user->medicine_list_id)
                ->pluck('precio', 'medicine_id');

            // 2) Actualizar set/infusor de mezclas existentes
            //    (no tocamos medicamentos ni volumen/tiempo en esta ruta)
            foreach ($mezclasExistentesPayload as $idx => $payload) {
                // Mapeamos por posición visual: Mezcla #N (orden en la vista)
                // Tomamos la N-ésima mezcla del modelo (mismo orden que el que mandas a la vista)
                $mezclaModelo = $solicitud->mezclas[$idx] ?? null;
                if (!$mezclaModelo) {
                    continue; // o lanzar error si prefieres
                }

                $setInfusion = !empty($payload['set_infusion']) ? (bool)$payload['set_infusion'] : false;
                $infusorId   = !empty($payload['infusor_id']) ? (int)$payload['infusor_id'] : null;

                // XOR: no ambos; ambos opcionales
                if ($setInfusion && $infusorId) {
                    throw new \Exception("Mezcla #" . ($idx + 1) . ": selecciona set de infusión o un infusor, no ambos.");
                }
                // Si quieres exactamente uno, descomenta:
                // if (!$setInfusion && !$infusorId) { throw new \Exception("Mezcla #".($idx+1).": debes seleccionar set de infusión o un infusor."); }

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

            // 3) Crear nuevas mezclas (con todas las validaciones, igual que en store)
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
                // Forzar exactamente uno (opcional):
                // if (!$setInfusion && !$infusorId) { throw new \Exception("Nueva mezcla #" . ($index + 1) . ": debes seleccionar set de infusión o un infusor."); }

                $meds = $mezclaData['medicamentos'] ?? [];
                if (!is_array($meds) || count($meds) === 0) {
                    throw new \Exception("La nueva mezcla #" . ($index + 1) . " no contiene medicamentos.");
                }

                // Validar que no haya una mezcla "igual" ya existente (tu lógica de coincidencia)
                $coincidencia = $mezclasExistentes->first(function ($mezclaExistente) use ($mezclaData) {
                    $coincidenMedicamentos = $mezclaExistente->medicamentos->pluck('medicamento_id')->sort()->values()->all() ===
                        collect($mezclaData['medicamentos'])->pluck('medicamento_id')->sort()->values()->all();

                    return $mezclaExistente->volumen_dilucion == $mezclaData['volumen_dilucion']
                        && $mezclaExistente->tiempo_infusion == $mezclaData['tiempo_infusion']
                        && $mezclaExistente->medicamentos->count() === count($mezclaData['medicamentos'])
                        && $coincidenMedicamentos;
                });
                if ($coincidencia) {
                    continue; // evitar duplicado
                }

                // Validación diluyente/vía y requires_infusor si aplica
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
                        ->select('mc.requires_infusor')
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

                // Guardar medicamentos con validación de concentración + cálculo dosis_ml
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

                    $precioUnit = $precios[$medicamento['medicamento_id']] ?? 0;

                    MezclaMedicamento::create([
                        'mezcla_id'             => $mezcla->id,
                        'medicamento_id'        => $medicamento['medicamento_id'],
                        'nombre_medicamento'    => $medicamento['nombre'],
                        'dosis'                 => $dosis,
                        'dosis_ml'              => $dosisML,
                        'diluyente_id'          => $medicamento['diluyente_id'] ?? null,
                        'via_administracion_id' => $medicamento['via_administracion_id'] ?? null,
                        'precio_unitario'       => $precioUnit,
                    ]);
                }
            }

            DB::commit();
            return redirect()
                ->route('admin.oncologicos.mezclas.index', ['mezcla' => $solicitud->id])
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
        // Trae todo lo necesario en una consulta
        $solicitud_onco = SolicitudOnco::with([
            'user.hospital',
            'user.medicineList.medicines', // 👈 ahora esta
            'mezclas.medicamentos.medicamentoOnco.catalog',
            'mezclas.medicamentos.diluyente',
        ])->findOrFail($solicitud->id);


        $pdf = Pdf::loadView('pdfs.oncologicos.remision', [
            'solicitud'  => $solicitud_onco,
            'mezclas'    => $solicitud_onco->mezclas,
            'fechaEmision' => now()->format('d/m/Y H:i'),
        ])->setPaper('letter', 'portrait');

        // return ([
        //     'solicitud' => $solicitud_onco,
        //     'mezclas' => $solicitud_onco->mezclas,
        // ]);

        return $pdf->stream();
    }
}
