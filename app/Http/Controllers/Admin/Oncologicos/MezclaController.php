<?php

namespace App\Http\Controllers\Admin\Oncologicos;

use App\Http\Controllers\Controller;
use App\Models\Oncologicos\Mezcla;
use App\Models\Oncologicos\SolicitudOnco;
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
        $mezcla = \App\Models\Oncologicos\Mezcla::with(['solicitud', 'medicamentos'])
            ->findOrFail($id);

        return view('admin.oncologicos.mezclas.show', compact('mezcla'));
    }

    public function edit($id)
    {

        // Cargar la mezcla con solicitud y medicamentos
        $mezcla = Mezcla::with(['solicitud', 'medicamentos'])->findOrFail($id);
        $solicitud = $mezcla->solicitud;

        $user = Auth::user();
        $listaId = $user->medicine_list_id;

        // Medicamentos de la lista personalizada
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

        // Información adicional para cada medicamento
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

        // dd(
        //     $infoAdicional, $medicamentos, $mezcla, $solicitud, $user->medicine_list_id, $listaId
        // );
        // Devolver la mezcla, solicitud, medicamentos y las relaciones adicionales a la vista
        return view('admin.oncologicos.mezclas.edit', compact('mezcla', 'solicitud', 'medicamentos', 'infoAdicional'));
    }


    public function update(Request $request, $id)
    {
        // Logic to update the mix
        // Validate and update the mix data
        // Redirect or return a response
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
}
