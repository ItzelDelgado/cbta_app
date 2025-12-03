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

    public function create()
    {
        // Diluyentes con sus presentaciones activas (solo para mostrar info en el form)
        $diluents = Diluent::with([
            'presentations' => fn($q) => $q->where('is_active', true)->orderBy('volume_ml')
        ])->orderBy('denominacion_generica')
            ->get(['id', 'denominacion_generica']);

        // Vías de administración
        $routes = AdministrationRoute::orderBy('name')->get(['id', 'name']);

        return view('admin.oncologicos.catalog.create', compact('diluents', 'routes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'denominacion'           => 'required|string|max:255',
            'denominacion_comercial' => 'required|string|max:255',
            'conc_min'               => 'nullable|numeric|min:0',
            'conc_max'               => 'nullable|numeric|min:0',
            'legend'                 => 'nullable|string|max:1000',
            'requires_infusor'       => 'nullable|boolean',
            'diluents'               => 'nullable|array',
            'diluents.*'             => 'integer|exists:diluents,id',
            'routes'                 => 'nullable|array',
            'routes.*'               => 'integer|exists:administration_routes,id',
        ]);

        // Importante: la lógica de dosis/volumen ya NO vive en catálogo,
        // ahora se calculará por presentación (medicine_presentations).
        $catalog = MedicinesCatalog::create([
            'denominacion'           => $request->denominacion,
            'denominacion_comercial' => $request->denominacion_comercial,
            'conc_min'               => $request->conc_min,
            'conc_max'               => $request->conc_max,
            'legend'                 => $request->legend,
            'requires_infusor'       => $request->boolean('requires_infusor', false),
            // si dejaste cantidad_medicamento/volumen_diluyente en la tabla, puedes
            // inicializarlos como null y usarlos solo de referencia, no para cálculos
            // 'cantidad_medicamento'   => null,
            // 'volumen_diluyente'      => null,
        ]);

        // Relaciones many-to-many
        $catalog->diluents()->sync($request->input('diluents', []));
        $catalog->administrationRoutes()->sync($request->input('routes', []));

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

    public function edit(string $id)
    {
        $medicamento = MedicinesCatalog::with(['diluents', 'administrationRoutes'])->findOrFail($id);

        // DILUYENTES con presentaciones activas
        $diluents = Diluent::with([
            'presentations' => fn($q) => $q->where('is_active', true)->orderBy('volume_ml')
        ])
            ->orderBy('denominacion_generica')
            ->get(['id', 'denominacion_generica']);

        // VÍAS
        $routes = AdministrationRoute::orderBy('name')->get(['id', 'name']);

        // IDs seleccionados
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
    
    public function update(Request $request, $id)
    {
        $request->validate([
            'denominacion'           => 'required|string|max:255',
            'denominacion_comercial' => 'required|string|max:255',
            'conc_min'               => 'nullable|numeric|min:0',
            'conc_max'               => 'nullable|numeric|min:0',
            'legend'                 => 'nullable|string|max:1000',
            'requires_infusor'       => 'nullable|boolean',
            'diluents'               => 'nullable|array',
            'diluents.*'             => 'integer|exists:diluents,id',
            'routes'                 => 'nullable|array',
            'routes.*'               => 'integer|exists:administration_routes,id',
        ]);

        $medicamento = MedicinesCatalog::findOrFail($id);

        $medicamento->update([
            'denominacion'           => $request->denominacion,
            'denominacion_comercial' => $request->denominacion_comercial,
            'conc_min'               => $request->conc_min,
            'conc_max'               => $request->conc_max,
            'legend'                 => $request->legend,
            'requires_infusor'       => $request->boolean('requires_infusor', false),
        ]);

        // Relaciones many-to-many
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
