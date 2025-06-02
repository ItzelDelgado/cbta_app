<?php

namespace App\Http\Controllers\Admin\Oncologicos;

use App\Http\Controllers\Controller;
use App\Models\Oncologicos\MedicineList;
use App\Models\Oncologicos\MedicineOnco;
use App\Models\Oncologicos\MedicinesCatalog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MedicineController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $listas = MedicineList::with('medicines')->get(); // Asume relación definida
        return view('admin.oncologicos.medicines.index', compact('listas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $catalogo = MedicinesCatalog::where('state', true)->get(['id', 'denominacion', 'presentacion']);
        return view('admin.oncologicos.medicines.create', compact('catalogo'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request->all());
        // Validación básica SIN eliminar antes los medicamentos
        $request->validate([
            'name' => 'required|string|max:255|unique:medicine_lists,name',
            'description' => 'nullable|string',
            'medicamentos' => 'required|array|min:1',
            'medicamentos.*.id' => 'required|exists:medicines_catalog,id',
            'medicamentos.*.precio' => 'required|numeric|min:0',
        ]);


        // Después de la validación, limpiar filas incompletas por seguridad
        $medicamentosFiltrados = collect($request->input('medicamentos', []))
            ->filter(function ($item) {
                return isset($item['id'], $item['precio']) && $item['id'] !== null && $item['precio'] !== null;
            })->values()->toArray();

        if (count($medicamentosFiltrados) === 0) {
            return back()->withInput()->withErrors(['medicamentos' => 'Debes ingresar al menos un medicamento válido con precio.']);
        }

        // Crear la lista
        $lista = MedicineList::create([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        // Asociar medicamentos con precio
        $data = [];
        foreach ($medicamentosFiltrados as $med) {
            $catalogItem = MedicinesCatalog::find($med['id']);
            if (!$catalogItem) continue;

        $medicineOnco = MedicineOnco::firstOrCreate(
            ['catalog_id' => $catalogItem->id], // <-- condición
            [
                'catalog_id' => $catalogItem->id, // <-- incluirlo también al crear
                'precio' => $med['precio']
            ]
        );


            $data[$medicineOnco->id] = ['precio' => $med['precio']];
        }

        $lista->medicines()->attach($data);

        return redirect()->route('admin.oncologicos.medicines.index')
            ->with('success', 'Lista de medicamentos creada correctamente.');
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
        $lista = MedicineList::with(['medicines' => function ($q) {
            $q->with('catalog'); // ← si necesitas mostrar denominación/presentación
        }])->findOrFail($id);

        $catalogo = MedicinesCatalog::where('state', true)->get(['id', 'denominacion', 'presentacion']);

        return view('admin.oncologicos.medicines.edit', compact('lista', 'catalogo'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:medicine_lists,name,' . $id,
            'description' => 'nullable|string',
            'medicamentos' => 'required|array|min:1',
            'medicamentos.*.precio' => 'required|numeric|min:0',
        ]);

        $lista = MedicineList::findOrFail($id);
        $lista->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        // Limpiar y volver a asociar medicamentos
        $lista->medicines()->detach();

        $data = [];
        foreach ($request->medicamentos as $med) {
            $catalogItem = MedicinesCatalog::find($med['id']);
            if (!$catalogItem) continue;

            $medicineOnco = MedicineOnco::firstOrCreate(
                ['catalog_id' => $catalogItem->id],
                [
                    'catalog_id' => $catalogItem->id,
                    'precio' => $med['precio']
                ]
            );

            $data[$medicineOnco->id] = ['precio' => $med['precio']];
        }

        $lista->medicines()->attach($data);

        return redirect()->route('admin.oncologicos.medicines.index')
            ->with('success', 'Lista de medicamentos actualizada correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $lista = MedicineList::findOrFail($id);
        $lista->medicines()->detach(); // Elimina relaciones
        $lista->delete(); // Elimina la lista

        return redirect()->route('admin.oncologicos.medicines.index')
            ->with('success', 'Lista de medicamentos eliminada correctamente.');
    }
}
