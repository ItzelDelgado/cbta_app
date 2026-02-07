<?php

namespace App\Http\Controllers\Admin\Oncologicos;

use App\Exports\Oncologicos\MedicineListExport;
use App\Exports\Oncologicos\MedicineListPricesByHospitalExport;
use App\Http\Controllers\Controller;
use App\Models\Oncologicos\Distributor;
use App\Models\Oncologicos\MedicineList;
use App\Models\Oncologicos\MedicineOnco;
use App\Models\Oncologicos\MedicinePresentation;
use App\Models\Oncologicos\MedicinesCatalog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

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

        // 👇 para que la vista pueda usar $distributor sin truene
        $distributor = null;

        return view('admin.oncologicos.medicines.create', compact('catalogos', 'distributor'));
    }




    public function store(Request $request)
    {
        $request->validate([
            'name'          => 'required|string|max:255|unique:medicine_lists,name',
            'description'   => 'nullable|string',
            'active_brands' => 'nullable|boolean',
            'charge_by'     => 'required|in:mg,frasco',

            'medicamentos'                   => 'required|array|min:1',
            'medicamentos.*.presentation_id' => 'required|exists:medicine_presentations,id',
            'medicamentos.*.precio'          => 'required|numeric|min:0',

            // Distributor (opcional)
            'distributor_name'    => 'nullable|string|max:255|required_with:distributor_address,distributor_logo',
            'distributor_address' => 'nullable|string|max:500|required_with:distributor_name,distributor_logo',
            'distributor_logo'    => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'distributor_name.required_with'    => 'Indica el nombre del distribuidor.',
            'distributor_address.required_with' => 'Indica la dirección del distribuidor.',
        ]);

        $items = collect($request->input('medicamentos', []))
            ->filter(
                fn($m) =>
                !empty($m['presentation_id']) &&
                    $m['precio'] !== null &&
                    $m['precio'] !== ''
            )
            ->values();

        if ($items->isEmpty()) {
            return back()->withInput()->withErrors([
                'medicamentos' => 'Debes ingresar al menos una presentación válida con precio.',
            ]);
        }

        // ✅ no permitir duplicados (en vez de “colapsarlos”)
        if ($items->pluck('presentation_id')->duplicates()->isNotEmpty()) {
            return back()->withInput()->withErrors([
                'medicamentos' => 'No puedes repetir la misma presentación más de una vez en la lista.',
            ]);
        }

        try {
            DB::beginTransaction();

            $chargeBy = $request->input('charge_by', 'mg'); // ✅ SOLO MANDA EL SWITCH GLOBAL

            $lista = MedicineList::create([
                'name'          => $request->name,
                'description'   => $request->description,
                'active_brands' => $request->boolean('active_brands', false),
                'charge_by'     => $chargeBy,
            ]);

            $hasDistributor =
                $request->filled('distributor_name') ||
                $request->filled('distributor_address') ||
                $request->hasFile('distributor_logo');

            if ($hasDistributor) {
                $logoPath = null;
                if ($request->hasFile('distributor_logo')) {
                    $logoPath = $request->file('distributor_logo')->store('distributors', 'public');
                }

                Distributor::create([
                    'medicine_list_id' => $lista->id,
                    'nombre'           => $request->input('distributor_name'),
                    'direccion'        => $request->input('distributor_address'),
                    'logo_path'        => $logoPath,
                ]);
            }

            // ✅ Pivot: SOLO manda el switch global
            $pivotData = [];

            foreach ($items as $item) {
                $presentationId  = (int) $item['presentation_id'];
                $precioCapturado = (float) $item['precio'];

                $pivotData[$presentationId] = [
                    'charge_by'          => $chargeBy,
                    'precio'             => $chargeBy === 'frasco' ? $precioCapturado : null,
                    'precio_mg_override' => $chargeBy === 'mg' ? $precioCapturado : null,
                ];
            }

            if (empty($pivotData)) {
                DB::rollBack();
                return back()->withInput()->withErrors([
                    'medicamentos' => 'No se pudo construir ninguna relación de presentaciones con la lista.',
                ]);
            }

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
            'presentations.catalog',
            'distributor', // ✅ NUEVO
        ])->findOrFail($id);

        $catalogos = MedicinesCatalog::with('presentations')
            ->orderBy('denominacion')
            ->get();

        $listaItems = $lista->presentations->map(function ($pres) {
            return [
                'catalog_id'      => $pres->catalog_id,
                'presentation_id' => $pres->id,
                'charge_by'       => $pres->pivot->charge_by ?? 'mg',
                'precio'          => $pres->pivot->precio,
            ];
        })->values();

        return view('admin.oncologicos.medicines.edit', [
            'lista'        => $lista,
            'catalogos'    => $catalogos,
            'listaItems'   => $listaItems,
            'distributor'  => $lista->distributor, // ✅ NUEVO (puede ser null)
        ]);
    }


    public function update(Request $request, string $id)
    {
        $request->validate([
            'name'           => 'required|string|max:255|unique:medicine_lists,name,' . $id,
            'description'    => 'nullable|string',
            'active_brands'  => 'nullable|boolean',
            'charge_by'      => 'required|in:mg,frasco',

            // Distributor
            'distributor_nombre'    => 'nullable|string|max:255',
            'distributor_direccion' => 'nullable|string|max:500',
            'distributor_logo'      => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'distributor_delete'    => 'nullable|boolean',

            'medicamentos'                   => 'required|array|min:1',
            'medicamentos.*.catalog_id'      => 'required|exists:medicines_catalog,id',
            'medicamentos.*.presentation_id' => 'required|exists:medicine_presentations,id',
            'medicamentos.*.precio'          => 'required|numeric|min:0',

            // ❌ ya NO validamos charge_by por fila
            // 'medicamentos.*.charge_by'    => 'nullable|in:mg,frasco',
        ], [
            'medicamentos.required'                => 'Debes agregar al menos una presentación.',
            'medicamentos.*.catalog_id.*'          => 'Selecciona un medicamento válido.',
            'medicamentos.*.presentation_id.*'     => 'Selecciona una presentación válida.',
            'medicamentos.*.precio.required'       => 'Indica el precio para cada presentación.',
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

        if ($rows->pluck('presentation_id')->duplicates()->isNotEmpty()) {
            return back()->withInput()->withErrors([
                'medicamentos' => 'No puedes repetir la misma presentación más de una vez en la lista.',
            ]);
        }

        try {
            DB::beginTransaction();

            /** @var \App\Models\Oncologicos\MedicineList $lista */
            $lista = MedicineList::with('distributor')->findOrFail($id);

            $chargeByGlobal = $request->input('charge_by', 'mg');

            // 1) Actualizar lista
            $lista->update([
                'name'          => $request->name,
                'description'   => $request->description,
                'active_brands' => $request->boolean('active_brands', false),
                'charge_by'     => $chargeByGlobal,
            ]);

            // 1.1) Distributor delete
            if ($request->boolean('distributor_delete')) {
                if ($lista->distributor) {
                    if (!empty($lista->distributor->logo_path)) {
                        Storage::disk('public')->delete($lista->distributor->logo_path);
                    }
                    $lista->distributor->delete();
                }
            } else {
                // 1.2) Distributor upsert si mandan algo
                $distNombre    = trim((string) $request->input('distributor_nombre', ''));
                $distDireccion = trim((string) $request->input('distributor_direccion', ''));

                $hayDatosDistributor =
                    ($distNombre !== '') ||
                    ($distDireccion !== '') ||
                    $request->hasFile('distributor_logo');

                if ($hayDatosDistributor) {
                    $distributor = $lista->distributor ?: new Distributor();
                    $distributor->medicine_list_id = $lista->id;
                    $distributor->nombre    = $distNombre;
                    $distributor->direccion = $distDireccion;

                    if ($request->hasFile('distributor_logo')) {
                        if (!empty($distributor->logo_path)) {
                            Storage::disk('public')->delete($distributor->logo_path);
                        }
                        $path = $request->file('distributor_logo')->store('distributors/logos', 'public');
                        $distributor->logo_path = $path;
                    }

                    $distributor->save();
                }
            }

            // 2) Pivot: ✅ SIEMPRE manda el switch global (ignora cualquier charge_by por fila)
            $pivotData = [];

            foreach ($rows as $row) {
                $presentationId = (int) $row['presentation_id'];
                $precio         = (float) $row['precio'];

                $pivotData[$presentationId] = [
                    'charge_by'          => $chargeByGlobal,
                    'precio'             => $chargeByGlobal === 'frasco' ? $precio : null,
                    'precio_mg_override' => $chargeByGlobal === 'mg' ? $precio : null,
                ];
            }

            if (empty($pivotData)) {
                DB::rollBack();
                return back()->withInput()->withErrors([
                    'medicamentos' => 'No se pudo construir ninguna relación de presentaciones con la lista.',
                ]);
            }

            // 3) Sync
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


    public function exportarExcel(MedicineList $medicineList)
    {
        $filename = 'lista_precios_' . $medicineList->id . '.xlsx';
        return Excel::download(new MedicineListExport($medicineList->id), $filename);
    }
}
