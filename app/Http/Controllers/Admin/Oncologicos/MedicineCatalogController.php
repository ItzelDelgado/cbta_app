<?php

namespace App\Http\Controllers\Admin\Oncologicos;

use App\Http\Controllers\Controller;
use App\Models\Oncologicos\AdministrationRoute;
use App\Models\Oncologicos\Diluent;
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
        $diluents = Diluent::all();
        $routes = AdministrationRoute::all();

        return view('admin.oncologicos.catalog.create', compact('diluents', 'routes'));

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'denominacion' => 'required|string|max:255',
            'presentacion' => 'required|string|max:255',
            'diluents' => 'nullable|array',
            'routes' => 'nullable|array',
        ]);

        // Crear medicamento
        $med = \App\Models\Oncologicos\MedicinesCatalog::create([
            'denominacion' => $request->denominacion,
            'presentacion' => $request->presentacion,
        ]);

        // Relacionar diluyentes
        if ($request->filled('diluents')) {
            $med->diluents()->sync($request->diluents);
        }

        // Relacionar vías de administración
        if ($request->filled('routes')) {
            $med->administrationRoutes()->sync($request->routes);
        }

        return redirect()->route('admin.oncologicos.medicines.catalog.index')
            ->with('success', 'Medicamento agregado correctamente al catálogo.');
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
        $medicamento = MedicinesCatalog::with(['diluents', 'administrationRoutes'])->findOrFail($id);
        $diluents = Diluent::all();
        $routes = AdministrationRoute::all();

        // IDs relacionados
        $selectedDiluents = $medicamento->diluents->pluck('id')->toArray();
        $selectedRoutes = $medicamento->administrationRoutes->pluck('id')->toArray();

        return view('admin.oncologicos.catalog.edit', compact(
            'medicamento', 'diluents', 'routes', 'selectedDiluents', 'selectedRoutes'
        ));
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

        if ($request->filled('diluents')) {
            $medicamento->diluents()->sync($request->diluents);
        } else {
            $medicamento->diluents()->detach(); // Limpia si no seleccionaron
        }

        if ($request->filled('routes')) {
            $medicamento->administrationRoutes()->sync($request->routes);
        } else {
            $medicamento->administrationRoutes()->detach();
        }

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
