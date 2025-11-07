<?php

namespace App\Http\Controllers\Admin\Oncologicos;

use App\Http\Controllers\Controller;
use App\Models\Oncologicos\MedicineList;
use App\Models\Oncologicos\MedicineOnco;
use App\Models\Oncologicos\MedicinesCatalog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MedicineController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Si tu relación está definida con withPivot('precio'), esto trae los precios personalizados
        $listas = MedicineList::with(['medicines' => function ($q) {
            $q->with('catalog:id,denominacion,presentacion');
        }])->get();

        return view('admin.oncologicos.medicines.index', compact('listas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Catálogo base (activos)
        $catalogo = MedicinesCatalog::where('state', true)
            ->get(['id', 'denominacion', 'presentacion']);

        return view('admin.oncologicos.medicines.create', compact('catalogo'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'                      => 'required|string|max:255|unique:medicine_lists,name',
            'description'               => 'nullable|string',
            'active_brands'             => 'nullable|boolean',
            'charge_by'                 => 'required|in:mg,frasco',   // ⬅️ NUEVO
            'medicamentos'              => 'required|array|min:1',
            'medicamentos.*.id'         => 'required|exists:medicines_catalog,id',
            'medicamentos.*.precio'     => 'required|numeric|min:0',
        ]);

        // Normaliza/filtra filas válidas
        $medicamentos = collect($request->input('medicamentos', []))
            ->filter(fn($it) => isset($it['id'], $it['precio']) && $it['id'] !== null && $it['precio'] !== null)
            ->values();

        if ($medicamentos->isEmpty()) {
            return back()->withInput()->withErrors([
                'medicamentos' => 'Debes ingresar al menos un medicamento válido con precio.'
            ]);
        }

        try {
            DB::beginTransaction();

            // Crear lista con active_brands + charge_by
            $lista = MedicineList::create([
                'name'          => $request->name,
                'description'   => $request->description,
                'active_brands' => $request->boolean('active_brands', false),
                'charge_by'     => $request->input('charge_by', 'mg'), // ⬅️ NUEVO
            ]);

            // Construir datos para el pivot: [medicine_onco_id => ['precio' => X]]
            $pivotData = [];

            foreach ($medicamentos as $med) {
                $catalogItem = MedicinesCatalog::find($med['id']);
                if (!$catalogItem) {
                    continue;
                }

                // Asegura que exista/actualiza el registro en medicine_oncos para ese catalog_id
                $medicineOnco = MedicineOnco::updateOrCreate(
                    ['catalog_id' => $catalogItem->id],
                    ['precio'     => $med['precio']]
                );

                $pivotData[$medicineOnco->id] = ['precio' => $med['precio']];
            }

            // Asociar (con precios) en la tabla pivot
            $lista->medicines()->sync($pivotData);

            DB::commit();

            return redirect()
                ->route('admin.oncologicos.medicines.index')
                ->with('success', 'Lista de medicamentos creada correctamente.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()->withErrors([
                'error' => 'Error al crear la lista: ' . $e->getMessage()
            ]);
        }
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $lista = MedicineList::with([
            'medicines' => function ($q) {
                $q->with('catalog:id,denominacion,presentacion');
            }
        ])->findOrFail($id);

        $catalogo = MedicinesCatalog::where('state', true)
            ->get(['id', 'denominacion', 'presentacion']);

        return view('admin.oncologicos.medicines.edit', compact('lista', 'catalogo'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name'                      => 'required|string|max:255|unique:medicine_lists,name,' . $id,
            'description'               => 'nullable|string',
            'active_brands'             => 'nullable|boolean',
            'charge_by'                 => 'required|in:mg,frasco',  // ⬅️ NUEVO
            'medicamentos'              => 'required|array|min:1',
            'medicamentos.*.id'         => 'required|exists:medicines_catalog,id',
            'medicamentos.*.precio'     => 'required|numeric|min:0',
        ]);

        // Normaliza/filtra filas válidas
        $medicamentos = collect($request->input('medicamentos', []))
            ->filter(fn($it) => isset($it['id'], $it['precio']) && $it['id'] !== null && $it['precio'] !== null)
            ->values();

        if ($medicamentos->isEmpty()) {
            return back()->withInput()->withErrors([
                'medicamentos' => 'Debes ingresar al menos un medicamento válido con precio.'
            ]);
        }

        try {
            DB::beginTransaction();

            $lista = MedicineList::findOrFail($id);
            $lista->update([
                'name'          => $request->name,
                'description'   => $request->description,
                'active_brands' => $request->boolean('active_brands', false),
                'charge_by'     => $request->input('charge_by', 'mg'), // ⬅️ NUEVO
            ]);

            $pivotData = [];

            foreach ($medicamentos as $med) {
                $catalogItem = MedicinesCatalog::find($med['id']);
                if (!$catalogItem) {
                    continue;
                }

                // Actualiza o crea el registro de medicine_oncos para ese catalog_id
                $medicineOnco = MedicineOnco::updateOrCreate(
                    ['catalog_id' => $catalogItem->id],
                    ['precio'     => $med['precio']]
                );

                $pivotData[$medicineOnco->id] = ['precio' => $med['precio']];
            }

            // Reemplaza relaciones (y precios) en pivot
            $lista->medicines()->sync($pivotData);

            DB::commit();

            return redirect()->route('admin.oncologicos.medicines.index')
                ->with('success', 'Lista de medicamentos actualizada correctamente.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()->withErrors([
                'error' => 'Error al actualizar la lista: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            DB::beginTransaction();

            $lista = MedicineList::findOrFail($id);
            $lista->medicines()->detach();
            $lista->delete();

            DB::commit();

            return redirect()->route('admin.oncologicos.medicines.index')
                ->with('success', 'Lista de medicamentos eliminada correctamente.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors([
                'error' => 'Error al eliminar la lista: ' . $e->getMessage()
            ]);
        }
    }
}
