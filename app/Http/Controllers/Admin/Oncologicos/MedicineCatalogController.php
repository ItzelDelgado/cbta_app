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
            'cantidad_medicamento' => 'nullable|numeric|min:0',
            'volumen_diluyente' => 'nullable|numeric|min:0',
            'conc_min' => 'nullable|numeric|min:0',
            'conc_max' => 'nullable|numeric|min:0',
            'legend' => 'nullable|string|max:1000',
            'diluents' => 'nullable|array',
            'routes' => 'nullable|array',
        ]);

        $med = \App\Models\Oncologicos\MedicinesCatalog::create([
            'denominacion' => $request->denominacion,
            'presentacion' => $request->presentacion,
            'cantidad_medicamento' => $request->cantidad_medicamento,
            'volumen_diluyente' => $request->volumen_diluyente,
            'conc_min' => $request->conc_min,
            'conc_max' => $request->conc_max,
            'legend' => $request->legend,
        ]);

        if ($request->filled('diluents')) {
            $med->diluents()->sync($request->diluents);
        }

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
            'cantidad_medicamento' => 'nullable|numeric|min:0',
            'volumen_diluyente' => 'nullable|numeric|min:0',
            'conc_min' => 'nullable|numeric|min:0',
            'conc_max' => 'nullable|numeric|min:0',
            'legend' => 'nullable|string|max:1000',
        ]);

        $medicamento = MedicinesCatalog::findOrFail($id);
        $medicamento->update([
            'denominacion' => $request->denominacion,
            'presentacion' => $request->presentacion,
            'cantidad_medicamento' => $request->cantidad_medicamento,
            'volumen_diluyente' => $request->volumen_diluyente,
            'conc_min' => $request->conc_min,
            'conc_max' => $request->conc_max,
            'legend' => $request->legend,
        ]);

        if ($request->filled('diluents')) {
            $medicamento->diluents()->sync($request->diluents);
        } else {
            $medicamento->diluents()->detach();
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
