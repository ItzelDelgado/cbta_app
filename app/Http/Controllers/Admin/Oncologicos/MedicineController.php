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
        $listas = MedicineList::with(['medicines' => function ($q) {
            // Cargar el catálogo asociado a cada medicine_onco (sin 'presentacion')
            $q->with('catalog:id,denominacion,denominacion_comercial');
        }])->get();

        return view('admin.oncologicos.medicines.index', compact('listas'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $catalogo = MedicinesCatalog::where('state', true)
            ->get(['id', 'denominacion', 'denominacion_comercial']);

        return view('admin.oncologicos.medicines.create', compact('catalogo'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'                      => 'required|string|max:255|unique:medicine_lists,name',
            'description'               => 'nullable|string',

            'active_brands'             => 'nullable|boolean',
            'charge_by'                 => 'required|in:mg,frasco',

            'medicamentos'              => 'required|array|min:1',
            'medicamentos.*.id'         => 'required|exists:medicines_catalog,id',
            'medicamentos.*.precio'     => 'required|numeric|min:0',
        ], [
            'medicamentos.required'         => 'Debes agregar al menos un medicamento.',
            'medicamentos.*.id.required'    => 'Selecciona un medicamento válido.',
            'medicamentos.*.precio.required' => 'Indica el precio para cada medicamento.',
        ]);

        // Normalizar filas válidas
        $medicamentos = collect($request->medicamentos)
            ->filter(fn($m) => !empty($m['id']) && $m['precio'] !== null && $m['precio'] !== '')
            ->unique('id')
            ->values();

        if ($medicamentos->isEmpty()) {
            return back()->withInput()->withErrors([
                'medicamentos' => 'Debes ingresar al menos un medicamento válido con precio.',
            ]);
        }

        try {
            DB::beginTransaction();

            $chargeBy      = $request->input('charge_by', 'mg');
            $activeBrands  = $request->boolean('active_brands', false);

            // 1) Crear lista
            $lista = MedicineList::create([
                'name'          => $request->name,
                'description'   => $request->description,
                'active_brands' => $activeBrands,
                'charge_by'     => $chargeBy,
            ]);

            // 2) Pivot final
            $pivotData = [];

            foreach ($medicamentos as $m) {
                $catalog = MedicinesCatalog::find($m['id']);
                if (!$catalog) {
                    continue;
                }

                // asegurar existencia en medicine_oncos
                $medicineOnco = MedicineOnco::firstOrCreate(
                    ['catalog_id' => $catalog->id],
                    ['precio'     => 0]
                );

                // precio base por frasco
                $precioBaseFrasco = $medicineOnco->precio ?? 0;

                $precio           = null;
                $precioMgOverride = null;

                if ($chargeBy === 'mg') {
                    // Lista cobra por mg:
                    // - mantenemos precio por frasco en 'precio'
                    // - override por mg en 'precio_mg_override'
                    $precio           = $precioBaseFrasco;
                    $precioMgOverride = $m['precio'];
                } else { // 'frasco'
                    // Lista cobra por frasco:
                    $precio           = $m['precio'];
                    $precioMgOverride = null;
                }

                $pivotData[$medicineOnco->id] = [
                    'charge_by'          => $chargeBy,
                    'precio'             => $precio,
                    'precio_mg_override' => $precioMgOverride,
                ];
            }

            $lista->medicines()->sync($pivotData);

            DB::commit();

            return redirect()
                ->route('admin.oncologicos.medicines.index')
                ->with('success', 'Lista de medicamentos creada correctamente.');
        } catch (\Throwable $e) {

            DB::rollBack();

            return back()->withInput()->withErrors([
                'error' => 'Error al crear la lista: ' . $e->getMessage(),
            ]);
        }
    }



    public function edit(string $id)
    {
        $lista = MedicineList::with([
            'medicines' => function ($q) {
                $q->with('catalog:id,denominacion,denominacion_comercial');
            }
        ])->findOrFail($id);

        $catalogo = MedicinesCatalog::where('state', true)
            ->get(['id', 'denominacion', 'denominacion_comercial']);

        return view('admin.oncologicos.medicines.edit', compact('lista', 'catalogo'));
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'name'                      => 'required|string|max:255|unique:medicine_lists,name,' . $id,
            'description'               => 'nullable|string',
            'active_brands'             => 'nullable|boolean',

            'charge_by'                 => 'required|in:mg,frasco',

            'medicamentos'              => 'required|array|min:1',
            'medicamentos.*.id'         => 'required|exists:medicines_catalog,id',
            'medicamentos.*.precio'     => 'required|numeric|min:0',
        ], [
            'medicamentos.required'         => 'Debes agregar al menos un medicamento.',
            'medicamentos.*.id.required'    => 'Selecciona un medicamento válido.',
            'medicamentos.*.precio.required' => 'Indica el precio para cada medicamento.',
        ]);

        // Normalizar filas válidas
        $medicamentos = collect($request->input('medicamentos', []))
            ->filter(fn($m) => !empty($m['id']) && $m['precio'] !== null && $m['precio'] !== '')
            ->unique('id')
            ->values();

        if ($medicamentos->isEmpty()) {
            return back()->withInput()->withErrors([
                'medicamentos' => 'Debes ingresar al menos un medicamento válido con precio.',
            ]);
        }

        try {
            DB::beginTransaction();

            // Cargamos la lista con sus pivots actuales
            $lista = MedicineList::with('medicines')->findOrFail($id);

            // Mapa rápido: [medicine_onco_id => pivot]
            $pivotsActuales = $lista->medicines
                ->mapWithKeys(fn($m) => [$m->id => $m->pivot]);

            $chargeBy = $request->input('charge_by', 'mg'); // global de la lista

            // 1) Actualizar datos de la lista
            $lista->update([
                'name'          => $request->name,
                'description'   => $request->description,
                'active_brands' => $request->boolean('active_brands', false),
                'charge_by'     => $chargeBy,
            ]);

            // 2) Construir datos pivot
            $pivotData = [];

            foreach ($medicamentos as $m) {
                $catalogItem = MedicinesCatalog::find($m['id']);
                if (!$catalogItem) {
                    continue;
                }

                // asegurar que exista en medicine_oncos
                $medicineOnco = MedicineOnco::firstOrCreate(
                    ['catalog_id' => $catalogItem->id],
                    ['precio'     => 0]
                );

                // pivot actual (si la lista ya tenía ese medicamento)
                $pivotActual = $pivotsActuales[$medicineOnco->id] ?? null;

                // base frasco:
                //  1) si ya había pivot, usamos ese precio
                //  2) si no, usamos el precio del catálogo
                //  3) si tampoco, 0
                $precioBaseFrasco = $pivotActual->precio
                    ?? $medicineOnco->precio
                    ?? 0;

                $precio           = null;
                $precioMgOverride = null;

                if ($chargeBy === 'mg') {
                    // Lista cobra por mg:
                    // - Mantenemos el precio por frasco que ya tenía la lista (si existía)
                    // - Usamos el input como precio por mg
                    $precio           = $precioBaseFrasco;
                    $precioMgOverride = $m['precio'];
                } else { // 'frasco'
                    // Lista cobra por frasco:
                    // - El input es el nuevo precio por frasco
                    // - Borramos override por mg
                    $precio           = $m['precio'];
                    $precioMgOverride = null;
                }

                $pivotData[$medicineOnco->id] = [
                    'precio'             => $precio,
                    'precio_mg_override' => $precioMgOverride,
                    'charge_by'          => $chargeBy,
                ];
            }

            if (empty($pivotData)) {
                DB::rollBack();
                return back()->withInput()->withErrors([
                    'medicamentos' => 'No se pudo construir ninguna relación de medicamentos con la lista.',
                ]);
            }

            // 3) Sincronizar pivot
            $lista->medicines()->sync($pivotData);

            DB::commit();

            return redirect()
                ->route('admin.oncologicos.medicines.index')
                ->with('success', 'Lista de medicamentos actualizada correctamente.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->withInput()->withErrors([
                'error' => 'Error al actualizar la lista: ' . $e->getMessage(),
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
