<?php

namespace App\Http\Controllers\Admin\Oncologicos;

use App\Http\Controllers\Controller;
use App\Models\Oncologicos\MedicineOnco;
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
        $listaId = $user->medicine_list_id; // puede ser null

        // Base: medicine_oncos (mo) + catalog (mc); LEFT JOIN a la lista (mml)
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
                DB::raw('COALESCE(mml.precio, mo.precio) as precio'), // precio lista > precio base
                'mc.lote',        // <- desde catalog
                'mc.caducidad',   // <- desde catalog
                'mc.denominacion',
                'mc.presentacion',
                'mc.id as catalog_id'
            );

        // Si HAY lista asignada, filtra a los que están en esa lista.
        if ($listaId) {
            $query->where('mml.medicine_list_id', $listaId);
        }

        $medicamentos = $query->get();

        // Cargar diluyentes y vías por cada catalog_id
        $infoAdicional = [];
        foreach ($medicamentos as $med) {
            $diluyentes = DB::table('diluent_medicine_catalog')
                ->join('diluents', 'diluent_medicine_catalog.diluent_id', '=', 'diluents.id')
                ->where('diluent_medicine_catalog.medicine_catalog_id', $med->catalog_id)
                ->select('diluents.id', 'diluents.denominacion_generica')
                ->get();

            $vias = DB::table('administration_route_medicine_catalog')
                ->join('administration_routes', 'administration_route_medicine_catalog.administration_route_id', '=', 'administration_routes.id')
                ->where('administration_route_medicine_catalog.medicine_catalog_id', $med->catalog_id)
                ->select('administration_routes.id', 'administration_routes.name')
                ->get();

            $infoAdicional[$med->id] = [
                'denominacion' => $med->denominacion,
                'presentacion' => $med->presentacion,
                'lote'         => $med->lote,
                'caducidad'    => $med->caducidad,
                'precio'       => $med->precio,
                'diluyentes'   => $diluyentes,
                'vias'         => $vias,
            ];
        }

        return view('admin.oncologicos.mezclas.show', compact('mezcla', 'infoAdicional'));
    }


    public function edit($id)
    {
        $mezcla = Mezcla::with(['medicamentos', 'solicitud'])->findOrFail($id);

        $solicitud = $mezcla->solicitud;
        $user      = Auth::user();
        $listaId   = $user->medicine_list_id; // puede ser null

        // Base: medicine_oncos (mo) + catalog (mc) y LEFT JOIN a la lista (mml)
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
                DB::raw('COALESCE(mml.precio, mo.precio) as precio'), // precio de lista > precio base
                'mc.lote',        // <- AHORA desde catalog
                'mc.caducidad',   // <- AHORA desde catalog
                'mc.denominacion',
                'mc.presentacion',
                'mc.id as catalog_id'
            );

        // Si hay lista asignada, limita a los meds de la lista
        if ($listaId) {
            $query->where('mml.medicine_list_id', $listaId);
        }

        $medicamentos = $query->get();

        // Cargar diluyentes y vías por catálogo
        $infoAdicional = [];
        foreach ($medicamentos as $med) {
            $diluyentes = DB::table('diluent_medicine_catalog')
                ->join('diluents', 'diluent_medicine_catalog.diluent_id', '=', 'diluents.id')
                ->where('diluent_medicine_catalog.medicine_catalog_id', $med->catalog_id)
                ->select('diluents.id', 'diluents.denominacion_generica')
                ->get();

            $vias = DB::table('administration_route_medicine_catalog')
                ->join('administration_routes', 'administration_route_medicine_catalog.administration_route_id', '=', 'administration_routes.id')
                ->where('administration_route_medicine_catalog.medicine_catalog_id', $med->catalog_id)
                ->select('administration_routes.id', 'administration_routes.name')
                ->get();

            $infoAdicional[$med->id] = [
                'denominacion' => $med->denominacion,
                'presentacion' => $med->presentacion,
                'lote'         => $med->lote,
                'caducidad'    => $med->caducidad,
                'precio'       => $med->precio,
                'diluyentes'   => $diluyentes,
                'vias'         => $vias,
            ];
        }

        return view('admin.oncologicos.mezclas.edit', compact('mezcla', 'solicitud', 'medicamentos', 'infoAdicional'));
    }


    public function update(Request $request, $id)
    {
        // Helpers internos
        $generarLotePorMezcla = function (Mezcla $mezcla) {
            if ($mezcla->lote) return; // no sobreescribir si ya existe

            $hoy = Carbon::today();

            // Consecutivo diario para LOTES: cuenta mezclas del día con lote ya asignado
            $conteoHoy = Mezcla::whereDate('created_at', $hoy)
                ->whereNotNull('lote')
                ->lockForUpdate() // usar dentro de transacción
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

        // Acciones rápidas
        if ($request->accion === 'preparada') {
            $mezcla = Mezcla::with('solicitud')->findOrFail($id);

            DB::transaction(function () use ($mezcla, $generarLotePorMezcla, $asegurarRemisionPorSolicitud) {
                $mezcla->estado = 'preparada';

                // 1) LOTE por mezcla
                $generarLotePorMezcla($mezcla);

                // 2) REMISIÓN por solicitud (todas las mezclas comparten)
                if ($mezcla->solicitud) {
                    $remision = $asegurarRemisionPorSolicitud($mezcla->solicitud);
                    $mezcla->remision = $remision;

                    // Poner solicitud en "enproceso" si corresponde
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
                ->with('success', 'Mezcla marcada como preparada con lote (por mezcla) y remisión (por solicitud).');
        }

        if ($request->accion === 'entregada') {
            $mezcla = Mezcla::with('solicitud')->findOrFail($id);

            DB::transaction(function () use ($mezcla) {
                $mezcla->estado = 'entregada';
                $mezcla->save();

                // Si todas las mezclas están entregadas, finaliza
                if ($mezcla->solicitud) {
                    $todasEntregadas = $mezcla->solicitud->mezclas()->where('estado', '!=', 'entregada')->doesntExist();
                    if ($todasEntregadas) {
                        $mezcla->solicitud->estado = 'finalizada';
                        $mezcla->solicitud->save();
                    }
                }
            });

            return redirect()
                ->route('admin.oncologicos.mezclas.index', $mezcla->solicitud->id)
                ->with('success', 'Mezcla marcada como entregada.');
        }

        // ----- Edición normal (sin tocar lote/remisión) -----
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

        $user = auth()->user();
        $precios = DB::table('medicine_medicine_lists')
            ->where('medicine_list_id', $user->medicine_list_id)
            ->pluck('precio', 'medicine_id')
            ->mapWithKeys(fn($v, $k) => [(int)$k => $v]);

        DB::beginTransaction();
        try {
            // 1) Mezcla
            $volumenDilucion = (float) ($mezclaData['volumen_dilucion'] ?? 0);
            $tiempoInfusion  = $mezclaData['tiempo_infusion'] ?? null;

            $mezcla->volumen_dilucion = $volumenDilucion;
            $mezcla->tiempo_infusion  = $tiempoInfusion;
            $mezcla->save();

            // 2) Solicitud
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

            // 3) Medicamentos
            $mezcla->medicamentos()->delete();

            foreach ($mezclaData['medicamentos'] as $med) {
                $medicamentoId = (int) $med['medicamento_id'];
                $nombre        = $med['nombre'] ?? '';
                $diluyenteId   = $med['diluyente_id'] ?? null;
                $viaAdminId    = $med['via_administracion_id'] ?? null;
                $dosis         = (float) ($med['dosis'] ?? 0);
                $precio        = $precios[$medicamentoId] ?? 0;

                $medicine = MedicineOnco::with('catalog')->find($medicamentoId);
                if (!$medicine || !$medicine->catalog) {
                    throw new Exception("No se encontró información del catálogo para el medicamento ID {$medicamentoId}.");
                }
                $catalog = $medicine->catalog;

                $concentracion = $volumenDilucion > 0 ? $dosis / $volumenDilucion : 0;
                if ($concentracion < (float)$catalog->conc_min || $concentracion > (float)$catalog->conc_max) {
                    throw new Exception("La concentración de '{$catalog->denominacion}' está fuera del rango permitido ({$catalog->conc_min} - {$catalog->conc_max}). Dosis: {$dosis}, Volumen: {$volumenDilucion}.");
                }

                $dosisML = null;
                if ((float)$catalog->cantidad_medicamento > 0) {
                    $dosisML = ($dosis * (float)$catalog->volumen_diluyente) / (float)$catalog->cantidad_medicamento;
                    $dosisML = round($dosisML, 2);
                }

                $mezcla->medicamentos()->create([
                    'medicamento_id'        => $medicamentoId,
                    'nombre_medicamento'    => $nombre,
                    'dosis'                 => $dosis,
                    'dosis_ml'              => $dosisML,
                    'diluyente_id'          => $diluyenteId,
                    'via_administracion_id' => $viaAdminId,
                    'precio_unitario'       => $precio,
                ]);
            }

            // Si la acción es "aprobar" no tocamos lote/remisión
            if ($request->accion === 'aprobar') {
                $mezcla->estado = 'aprobada';
                $mezcla->save();
            }

            DB::commit();

            return redirect()
                ->route('admin.oncologicos.mezclas.index', $mezcla->solicitud->id)
                ->with('success', 'Mezcla y solicitud actualizadas correctamente.');
        } catch (Exception $e) {
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
        // Mezcla + solicitud + hospital
        $mezcla = Mezcla::with(['solicitud.user.hospital'])->findOrFail($mezcla->id);

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
            ->orderBy('mc.denominacion')        // ⬅️ ordenar alfabéticamente
            ->get()
            // Adjuntar el volumen total de la mezcla a cada item para el blade
            ->map(function ($m) use ($mezcla) {
                $m->volumen_total = $mezcla->volumen_dilucion; // mL totales de la mezcla
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

        $pdf = Pdf::loadView('pdfs.oncologicos.orden-de-preparacion', [
            'mezcla'            => $mezcla,
            'medicamentos'      => $medicamentos,
            'fecha_preparacion' => $fechaPreparacion,
            'fecha_limite_uso'  => $fechaLimiteUso,
            'hospital'          => $hospital,
        ])->setPaper('letter', 'portrait');

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
