<?php

namespace App\Http\Controllers\Admin\Oncologicos;

use App\Http\Controllers\Controller;
use App\Models\Oncologicos\MedicineList;
use App\Models\Oncologicos\MedicineOnco;
use App\Models\Oncologicos\MedicinePresentation;
use App\Models\Oncologicos\MedicinesCatalog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MedicineController extends Controller
{

    public function index()
    {
        $listas = MedicineList::with([
            'presentations.catalog' // presentaciones ligadas a la lista + su genérico
        ])->get();

        return view('admin.oncologicos.medicines.index', compact('listas'));
    }


    public function create()
    {
        $catalogos = MedicinesCatalog::with('presentations')
            ->orderBy('denominacion')
            ->get();

        return view('admin.oncologicos.medicines.create', compact('catalogos'));
    }



    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255|unique:medicine_lists,name',
            'description' => 'nullable|string',

            'active_brands' => 'nullable|boolean',

            'charge_by'   => 'required|in:mg,frasco',

            'medicamentos'                      => 'required|array|min:1',
            'medicamentos.*.presentation_id'    => 'required|exists:medicine_presentations,id',
            'medicamentos.*.precio'             => 'required|numeric|min:0',
        ], [
            'medicamentos.required'                    => 'Debes agregar al menos un medicamento.',
            'medicamentos.*.presentation_id.required'  => 'Selecciona una presentación válida.',
            'medicamentos.*.precio.required'           => 'Indica el precio para cada presentación.',
        ]);

        // Normalizar filas válidas (evitar vacías y duplicadas por presentación)
        $items = collect($request->input('medicamentos', []))
            ->filter(
                fn($m) =>
                !empty($m['presentation_id']) &&
                    $m['precio'] !== null &&
                    $m['precio'] !== ''
            )
            ->unique('presentation_id')
            ->values();

        if ($items->isEmpty()) {
            return back()->withInput()->withErrors([
                'medicamentos' => 'Debes ingresar al menos una presentación válida con precio.',
            ]);
        }

        try {
            DB::beginTransaction();

            $chargeBy = $request->input('charge_by', 'mg');

        // 1) Crear la lista
            /** @var \App\Models\Oncologicos\MedicineList $lista */
            $lista = MedicineList::create([
                'name'          => $request->name,
                'description'   => $request->description,
                'active_brands' => $request->boolean('active_brands', false),
                'charge_by'     => $chargeBy,
            ]);

            // 2) Construir datos de la pivot: [presentation_id => [..campos..]]
            $pivotData = [];

            foreach ($items as $item) {
                $presentationId = (int) $item['presentation_id'];
                $precioCapturado = (float) $item['precio'];

                $pivotData[$presentationId] = [
                    'charge_by'          => $chargeBy,
                    'precio'             => $chargeBy === 'frasco' ? $precioCapturado : null,
                    'precio_mg_override' => $chargeBy === 'mg'     ? $precioCapturado : null,
                ];
            }

            if (empty($pivotData)) {
                DB::rollBack();
                return back()->withInput()->withErrors([
                    'medicamentos' => 'No se pudo construir ninguna relación de presentaciones con la lista.',
                ]);
            }

            // 3) Sincronizar presentaciones en la nueva tabla pivot
            $lista->presentations()->sync($pivotData);

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
            'presentations.catalog', // 👈 ahora traemos las presentaciones con su catálogo
        ])->findOrFail($id);

        // Catálogos con sus presentaciones para los selects
        $catalogos = MedicinesCatalog::with('presentations')
            ->orderBy('denominacion')
            ->get();

        // Flatten de lo que ya tiene la lista para pasarlo a JS
        $listaItems = $lista->presentations->map(function ($pres) {
            return [
                'catalog_id'      => $pres->catalog_id,
                'presentation_id' => $pres->id,
                'charge_by'       => $pres->pivot->charge_by ?? 'mg',
                'precio'          => $pres->pivot->precio,
            ];
        })->values();

        return view('admin.oncologicos.medicines.edit', [
            'lista'      => $lista,
            'catalogos'  => $catalogos,
            'listaItems' => $listaItems,
        ]);
    }


    public function update(Request $request, string $id)
    {
        $request->validate([
            'name'        => 'required|string|max:255|unique:medicine_lists,name,' . $id,
            'description' => 'nullable|string',
            'active_brands' => 'nullable|boolean',

            'charge_by'   => 'required|in:mg,frasco',

            'medicamentos'                        => 'required|array|min:1',
            'medicamentos.*.catalog_id'           => 'required|exists:medicines_catalog,id',
            'medicamentos.*.presentation_id'      => 'required|exists:medicine_presentations,id',
            'medicamentos.*.precio'               => 'required|numeric|min:0',
            'medicamentos.*.charge_by'            => 'nullable|in:mg,frasco',
        ], [
            'medicamentos.required'          => 'Debes agregar al menos una presentación.',
            'medicamentos.*.catalog_id.*'    => 'Selecciona un medicamento válido.',
            'medicamentos.*.presentation_id.*' => 'Selecciona una presentación válida.',
            'medicamentos.*.precio.required'   => 'Indica el precio para cada presentación.',
        ]);

        // Normalizar filas válidas
        $rows = collect($request->input('medicamentos', []))
            ->filter(
                fn($m) =>
                !empty($m['catalog_id']) &&
                    !empty($m['presentation_id']) &&
                    $m['precio'] !== null &&
                    $m['precio'] !== ''
            )
            ->values();

        if ($rows->isEmpty()) {
            return back()->withInput()->withErrors([
                'medicamentos' => 'Debes ingresar al menos una presentación con precio.',
            ]);
        }

        // Aseguramos que no vengan presentaciones duplicadas
        if ($rows->pluck('presentation_id')->duplicates()->isNotEmpty()) {
            return back()->withInput()->withErrors([
                'medicamentos' => 'No puedes repetir la misma presentación más de una vez en la lista.',
            ]);
        }

        try {
            DB::beginTransaction();

            $lista = MedicineList::findOrFail($id);

            $chargeByGlobal = $request->input('charge_by', 'mg');

            // 1) Actualizar datos de la lista
            $lista->update([
                'name'          => $request->name,
                'description'   => $request->description,
                'active_brands' => $request->boolean('active_brands', false),
                'charge_by'     => $chargeByGlobal,
            ]);

            // 2) Construir datos para el pivot medicine_list_presentation
            $pivotData = [];

            foreach ($rows as $row) {
                $presentation = MedicinePresentation::find($row['presentation_id']);
                if (!$presentation) {
                    continue;
                }

                // Cobro final: el de la fila o el global
                $chargeBy = $row['charge_by'] ?? $chargeByGlobal;
                $precio   = (float) $row['precio'];

                $pivotData[$presentation->id] = [
                    'charge_by'          => $chargeBy,
                    'precio'             => $precio,
                    'precio_mg_override' => $chargeBy === 'mg' ? $precio : null,
                ];
            }

            if (empty($pivotData)) {
                DB::rollBack();
                return back()->withInput()->withErrors([
                    'medicamentos' => 'No se pudo construir ninguna relación de presentaciones con la lista.',
                ]);
            }

            // 3) Sincronizar presentaciones de la lista
            $lista->presentations()->sync($pivotData);

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
