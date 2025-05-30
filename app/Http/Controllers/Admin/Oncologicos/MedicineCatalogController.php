<?php

namespace App\Http\Controllers\Admin\Oncologicos;

use App\Http\Controllers\Controller;
use App\Models\Oncologicos\MedicinesCatalog;
use Illuminate\Http\Request;

class MedicineCatalogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $medicamentos = MedicinesCatalog::where('state', true)->get();
        return view('admin.oncologicos.catalog.index', compact('medicamentos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.oncologicos.catalog.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validación de datos
        $request->validate([
            'denominacion' => 'required|string|max:255',
            'presentacion' => 'required|string|max:255',
        ]);

        // Crear el medicamento en el catálogo
        MedicinesCatalog::create([
            'denominacion' => $request->denominacion,
            'presentacion' => $request->presentacion,
        ]);

        // Redirigir con mensaje de éxito
        return redirect()->route('admin.oncologicos.medicines.catalog.index')->with('success', 'Medicamento agregado correctamente al catálogo.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $medicamento = MedicinesCatalog::findOrFail($id);
        return view('admin.oncologicos.catalog.edit', compact('medicamento'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'denominacion' => 'required|string|max:255',
            'presentacion' => 'required|string|max:255',
        ]);

        $medicamento = MedicinesCatalog::findOrFail($id);
        $medicamento->update([
            'denominacion' => $request->denominacion,
            'presentacion' => $request->presentacion,
        ]);

        return redirect()->route('admin.oncologicos.medicines.catalog.index')
            ->with('success', 'Medicamento actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $medicamento = MedicinesCatalog::findOrFail($id);
        $medicamento->update(['state' => false]);

        return redirect()->route('admin.oncologicos.medicines.catalog.index')
            ->with('success', 'Medicamento deshabilitado correctamente.');
    }
}
