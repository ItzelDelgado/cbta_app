<?php

namespace App\Http\Controllers\Admin;

use App\Exports\Hospital\MezclasOncoPorHospitalExport;
use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\Hospital;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class HospitalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $hospitals = Hospital::latest()->get();
        return view('admin.hospitals.index', compact('hospitals'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $clientes = Cliente::orderBy('apellido')
            ->orderBy('nombre')
            ->get();

        return view('admin.hospitals.create', compact('clientes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name_hp' => 'required|string|max:255',
            'adress'  => 'required|string|max:400',
            'clientes'   => 'nullable|array',
            'clientes.*' => 'integer|exists:clientes,id',
        ]);

        // Para la solicitud
        $datos = $request->all();
        $datos['name'] = $datos['name_hp'];
        unset($datos['name_hp']);
        unset($datos['clientes']); // evitamos que intente guardarlo como columna

        // Crear hospital
        $hospital = Hospital::create($datos);

        // Guardar relación en pivote (si vienen clientes)
        $clientesIds = $request->input('clientes', []);
        $hospital->clientes()->sync($clientesIds);

        session()->flash('swal', [
            'title' => "¡Bien hecho!",
            'text'  => "El hospital se ha creado con éxito.",
            'icon'  => "success"
        ]);

        return redirect()->route('admin.hospitals.index');
    }

    /**
     * Display the specified resource.
     */
    // public function show(Hospital $hospital)
    // {
    //     return view('admin.hospitals.show');
    // }

    public function edit(Hospital $hospital)
    {
        $clientes = Cliente::orderBy('apellido')
            ->orderBy('nombre')
            ->get();

        // ids ya asignados al hospital (para precargar seleccionados)
        $selectedClientesIds = $hospital->clientes()
            ->pluck('clientes.id')
            ->toArray();

        return view('admin.hospitals.edit', compact('hospital', 'clientes', 'selectedClientesIds'));
    }

    public function update(Request $request, Hospital $hospital)
    {
        $request->validate([
            'name'      => 'required|string|max:255',
            'adress'    => 'required|string|max:400',
            'is_active' => 'required|boolean',

            // clientes (pivot)
            'clientes'   => 'nullable|array',
            'clientes.*' => 'integer|exists:clientes,id',
        ]);

        // Actualiza campos del hospital (sin clientes)
        $data = $request->only(['name', 'adress', 'is_active']);
        $hospital->update($data);

        // Actualiza relación pivot (si no mandas nada, queda vacío)
        $clientesIds = $request->input('clientes', []);
        $hospital->clientes()->sync($clientesIds);

        session()->flash('swal', [
            'title' => "¡Bien hecho!",
            'text'  => "El hospital se ha actualizado con éxito.",
            'icon'  => "success"
        ]);

        return redirect()->route('admin.hospitals.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    // public function destroy(Hospital $hospital)
    // {
    //     //
    // }

    public function exportarMezclasOnco(Hospital $hospital)
    {
        return Excel::download(
            new MezclasOncoPorHospitalExport($hospital->id),
            'reporte_mezclas_onco_hospital_' . $hospital->id . '.xlsx'
        );
    }
}
