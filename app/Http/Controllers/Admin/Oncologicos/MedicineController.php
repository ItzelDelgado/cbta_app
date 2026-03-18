<?php

namespace App\Http\Controllers\Admin\Oncologicos;

use App\Exports\Oncologicos\MedicineListExport;
use App\Http\Controllers\Controller;
use App\Models\Hospital;
use App\Models\Oncologicos\Distributor;
use App\Models\Oncologicos\MedicineList;
use App\Models\Oncologicos\MedicinesCatalog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

class MedicineController extends Controller
{
    private function currentHospitalId(): int
    {
        $hospitalId = Auth::user()?->hospital_id;
        abort_unless($hospitalId, 422, 'Tu usuario no tiene hospital asignado.');
        return (int) $hospitalId;
    }

    private function listForCurrentHospital(): ?MedicineList
    {
        return MedicineList::where('hospital_id', $this->currentHospitalId())
            ->with(['presentations.catalog', 'distributor'])
            ->first();
    }

    public function index()
    {
        return view('admin.oncologicos.medicines.index');
    }


    public function create()
    {
        $catalogos = MedicinesCatalog::with([
            'presentations' => function ($q) {
                $q->where('is_available', 1)
                    ->orderBy('presentacion');
            }
        ])
            ->orderBy('denominacion')
            ->get();

        // 👉 Hospitales para el select del formulario
        $hospitals = Hospital::orderBy('name')->get(['id', 'name']);

        $distributor = null;

        return view(
            'admin.oncologicos.medicines.create',
            compact('catalogos', 'hospitals', 'distributor')
        );
    }


    public function store(Request $request)
    {
        // ✅ Ahora el hospital viene del formulario
        $request->validate([
            'hospital_id'   => 'required|exists:hospitals,id',

            // ⚠️ ya no unique global; si quieres unique por hospital, lo validamos manual
            'name'          => 'required|string|max:255',
            'description'   => 'nullable|string',
            'active_brands' => 'nullable|boolean',
            'charge_by'     => 'required|in:mg,frasco',

            'medicamentos'                   => 'required|array|min:1',
            'medicamentos.*.presentation_id' => 'required|exists:medicine_presentations,id',
            'medicamentos.*.precio'          => 'required|numeric|min:0',

            'distributor_name'    => 'nullable|string|max:255|required_with:distributor_address,distributor_logo',
            'distributor_address' => 'nullable|string|max:500|required_with:distributor_name,distributor_logo',
            'distributor_logo'    => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'distributor_name.required_with'    => 'Indica el nombre del distribuidor.',
            'distributor_address.required_with' => 'Indica la dirección del distribuidor.',
            'hospital_id.required'              => 'Selecciona un hospital.',
            'hospital_id.exists'                => 'El hospital seleccionado no existe.',
        ]);

        $hospitalId = (int) $request->input('hospital_id');

        // ✅ Regla 1:1 por hospital (con el hospital elegido)
        if (MedicineList::where('hospital_id', $hospitalId)->exists()) {
            return back()->withInput()->withErrors([
                'hospital_id' => 'Este hospital ya tiene una lista de medicamentos configurada.',
            ]);
        }

        $items = collect($request->input('medicamentos', []))
            ->filter(fn($m) => !empty($m['presentation_id']) && $m['precio'] !== null && $m['precio'] !== '')
            ->values();

        if ($items->isEmpty()) {
            return back()->withInput()->withErrors([
                'medicamentos' => 'Debes ingresar al menos una presentación válida con precio.',
            ]);
        }

        if ($items->pluck('presentation_id')->duplicates()->isNotEmpty()) {
            return back()->withInput()->withErrors([
                'medicamentos' => 'No puedes repetir la misma presentación más de una vez en la lista.',
            ]);
        }

        try {
            DB::beginTransaction();

            $chargeBy = $request->input('charge_by', 'mg');

            $lista = MedicineList::create([
                'hospital_id'   => $hospitalId, // ✅ VIENE DEL FORM
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
        // ✅ Cargar la lista por ID (ya NO por hospital del usuario)
        $lista = MedicineList::with([
            'hospital',
            'presentations.catalog',
            'distributor'
        ])
            ->findOrFail($id);

        // ✅ Para el select de hospital (si quieres permitir cambiarlo)
        $hospitals = DB::table('hospitals')
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        $catalogos = MedicinesCatalog::with('presentations')
            ->orderBy('denominacion')
            ->get();

        $listaItems = $lista->presentations->map(function ($pres) {
            return [
                'catalog_id'      => $pres->catalog_id,
                'presentation_id' => $pres->id,
                'charge_by'       => $pres->pivot->charge_by ?? 'mg',
                'precio'          => $pres->pivot->charge_by === 'frasco'
                    ? ($pres->pivot->precio ?? null)
                    : ($pres->pivot->precio_mg_override ?? null),
            ];
        })->values();

        return view('admin.oncologicos.medicines.edit', [
            'lista'        => $lista,
            'catalogos'    => $catalogos,
            'listaItems'   => $listaItems,
            'distributor'  => $lista->distributor,
            'hospitals'    => $hospitals, // ✅ para el select
        ]);
    }


    public function update(Request $request, string $id)
    {
        $request->validate([
            // ✅ ahora el hospital viene del formulario
            'hospital_id' => [
                'required',
                'exists:hospitals,id',
                Rule::unique('medicine_lists', 'hospital_id')->ignore($id), // 1 hospital = 1 lista
            ],

            'name'           => 'required|string|max:255',
            'description'    => 'nullable|string',
            'active_brands'  => 'nullable|boolean',
            'charge_by'      => 'required|in:mg,frasco',

            'distributor_nombre'    => 'nullable|string|max:255',
            'distributor_direccion' => 'nullable|string|max:500',
            'distributor_logo'      => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'distributor_delete'    => 'nullable|boolean',

            'medicamentos'                   => 'required|array|min:1',
            'medicamentos.*.catalog_id'      => 'required|exists:medicines_catalog,id',
            'medicamentos.*.presentation_id' => 'required|exists:medicine_presentations,id',
            'medicamentos.*.precio'          => 'required|numeric|min:0',
        ], [
            'hospital_id.unique' => 'Ese hospital ya tiene una lista de medicamentos configurada.',
        ]);

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

            // ✅ ya NO filtramos por hospital del usuario
            $lista = MedicineList::with('distributor')->findOrFail($id);

            $chargeByGlobal = $request->input('charge_by', 'mg');

            $lista->update([
                'hospital_id'   => (int) $request->hospital_id,  // ✅ CLAVE
                'name'          => $request->name,
                'description'   => $request->description,
                'active_brands' => $request->boolean('active_brands', false),
                'charge_by'     => $chargeByGlobal,
            ]);

            // ===== Distributor =====
            if ($request->boolean('distributor_delete')) {
                if ($lista->distributor) {
                    if (!empty($lista->distributor->logo_path)) {
                        Storage::disk('public')->delete($lista->distributor->logo_path);
                    }
                    $lista->distributor->delete();
                }
            } else {
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

            // ===== Pivot sync =====
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

    public function destroy(string $id)
    {
        try {
            DB::beginTransaction();

            $hospitalId = $this->currentHospitalId();

            $lista = MedicineList::where('hospital_id', $hospitalId)->findOrFail($id);

            // OJO: tú usas presentations() en el resto del controller, aquí estabas usando medicines()
            $lista->presentations()->detach();
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
        $hospitalId = $this->currentHospitalId();

        $filename = 'lista_precios_' . $medicineList->id . '.xlsx';
        return Excel::download(new MedicineListExport($medicineList->id), $filename);
    }
}
