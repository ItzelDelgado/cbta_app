<?php

namespace App\Http\Controllers\Admin\Oncologicos;

use App\Http\Controllers\Controller;
use App\Models\Oncologicos\AdministrationRoute;
use App\Models\Oncologicos\Diluent;
use App\Models\Oncologicos\MedicinesCatalog;
use Carbon\Carbon;
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
        // DILUYENTES: con presentaciones ACTIVAS ordenadas por volumen
        $diluents = Diluent::with([
            'presentations' => fn($q) => $q->where('is_active', true)->orderBy('volume_ml')
        ])->orderBy('denominacion_generica')->get(['id', 'denominacion_generica']);

        // VÍAS DE ADMINISTRACIÓN
        $routes = AdministrationRoute::orderBy('name')->get(['id', 'name']);

        return view('admin.oncologicos.catalog.create', compact('diluents', 'routes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'denominacion'             => 'required|string|max:255',
            'denominacion_comercial'   => 'required|string|max:255',
            'presentacion'             => 'required|string|max:255',
            'cantidad_medicamento'     => 'nullable|numeric|min:0',
            'volumen_diluyente'        => 'nullable|numeric|min:0',
            'conc_min'                 => 'nullable|numeric|min:0',
            'conc_max'                 => 'nullable|numeric|min:0',
            'legend'                   => 'nullable|string|max:1000',
            'lote'                     => 'nullable|string|max:100',
            'caducidad'                => 'nullable|date',
            'requires_infusor'         => 'nullable|boolean',   // 👈 nuevo
            'diluents'                 => 'nullable|array',
            'routes'                   => 'nullable|array',
        ]);

        $med = MedicinesCatalog::create([
            'denominacion'             => $request->denominacion,
            'denominacion_comercial'   => $request->denominacion_comercial,
            'presentacion'             => $request->presentacion,
            'cantidad_medicamento'     => $request->cantidad_medicamento,
            'volumen_diluyente'        => $request->volumen_diluyente,
            'conc_min'                 => $request->conc_min,
            'conc_max'                 => $request->conc_max,
            'legend'                   => $request->legend,
            'lote'                     => $request->lote,
            'caducidad'                => $request->caducidad ? Carbon::parse($request->caducidad)->format('Y-m-d') : null,
            'requires_infusor'         => $request->boolean('requires_infusor'), // 👈 nuevo
        ]);

        if ($request->filled('diluents')) {
            $med->diluents()->sync($request->diluents);
        }

        if ($request->filled('routes')) {
            $med->administrationRoutes()->sync($request->routes);
        }

        return redirect()
            ->route('admin.oncologicos.medicines.catalog.index')
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

        // DILUYENTES + presentaciones activas
        $diluents = Diluent::with([
            'presentations' => fn($q) => $q->where('is_active', true)->orderBy('volume_ml')
        ])->orderBy('denominacion_generica')->get(['id', 'denominacion_generica']);

        // VÍAS
        $routes = AdministrationRoute::orderBy('name')->get(['id', 'name']);

        // IDs relacionados (para marcar seleccionados en el form)
        $selectedDiluents = $medicamento->diluents->pluck('id')->toArray();
        $selectedRoutes   = $medicamento->administrationRoutes->pluck('id')->toArray();

        return view('admin.oncologicos.catalog.edit', compact(
            'medicamento',
            'diluents',
            'routes',
            'selectedDiluents',
            'selectedRoutes'
        ));
    }

    /**
     * Update the specified resource in storage.
     */

    public function update(Request $request, $id)
    {
        $request->validate([
            'denominacion'             => 'required|string|max:255',
            'denominacion_comercial'   => 'required|string|max:255',
            'presentacion'             => 'required|string|max:255',
            'cantidad_medicamento'     => 'nullable|numeric|min:0',
            'volumen_diluyente'        => 'nullable|numeric|min:0',
            'conc_min'                 => 'nullable|numeric|min:0',
            'conc_max'                 => 'nullable|numeric|min:0',
            'legend'                   => 'nullable|string|max:1000',
            'lote'                     => 'nullable|string|max:100',
            'caducidad'                => 'nullable|date',
            'requires_infusor'         => 'nullable|boolean',   // 👈 nuevo
            'diluents'                 => 'nullable|array',
            'routes'                   => 'nullable|array',
        ]);

        $medicamento = MedicinesCatalog::findOrFail($id);

        $medicamento->update([
            'denominacion'             => $request->denominacion,
            'denominacion_comercial'   => $request->denominacion_comercial,
            'presentacion'             => $request->presentacion,
            'cantidad_medicamento'     => $request->cantidad_medicamento,
            'volumen_diluyente'        => $request->volumen_diluyente,
            'conc_min'                 => $request->conc_min,
            'conc_max'                 => $request->conc_max,
            'legend'                   => $request->legend,
            'lote'                     => $request->lote,
            'caducidad'                => $request->caducidad ? Carbon::parse($request->caducidad)->format('Y-m-d') : null,
            'requires_infusor'         => $request->boolean('requires_infusor'), // 👈 nuevo
        ]);

        // Relaciones Many-to-Many
        $medicamento->diluents()->sync($request->input('diluents', []));
        $medicamento->administrationRoutes()->sync($request->input('routes', []));

        return redirect()
            ->route('admin.oncologicos.medicines.catalog.index')
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
