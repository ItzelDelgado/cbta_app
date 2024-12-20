<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Medicine;
use App\Models\Input;
use Illuminate\Http\Request;

class MedicineController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $medicines = Medicine::latest()->get();
        return view('admin.medicines.index', compact('medicines'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all();
        //$inputs = Input::all();

        $inputs = Input::leftJoin('medicines', 'inputs.id', '=', 'medicines.input_id')
            ->whereNull('medicines.id')
            ->select('inputs.*')
            ->get();

        //return $inputs;
        return view('admin.medicines.create', compact('categories', 'inputs'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate(([
            'denominacion_generica' => 'required|string|max:255',
            'denominacion_comercial' => 'required|string|max:255',
            'descripcion' => 'nullable|string|max:255',
            'precio_ml' => 'required|numeric',
            'presentacion' => 'required|string|max:255',
            'presentacion_ml' => 'required|numeric',
            'category_id' => 'required|exists:categories,id',
            'input_id' => 'required|exists:inputs,id',
            'lote' => 'nullable|string|max:255',
            'caducidad' => 'nullable|date',
        ]));
        //return $request->all();
        Medicine::create($request->all());

        session()->flash(
            'swal',
            [
                'title' => "¡Bien hecho!",
                'text' => "El medicamento se ha creado con éxito.",
                'icon' => "success"

            ]
        );
        return redirect()->route('admin.medicines.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Medicine $medicine)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Medicine $medicine)
    {
        $categories = Category::all();
        return view('admin.medicines.edit', compact('medicine', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Medicine $medicine)
    {
        $request->validate(([
            'denominacion_generica' => 'required|string|max:255',
            'denominacion_comercial' => 'required|string|max:255',
            'descripcion' => 'nullable|string|max:255',
            'precio_ml' => 'required|numeric',
            'presentacion_ml' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'lote' => 'nullable|string|max:255',
            'caducidad' => 'nullable|date',
            //'input_id' => 'required|exists:inputs,id',
        ]));
        //return $request->all();
        $medicine->update($request->all());
        session()->flash(
            'swal',
            [
                'title' => "¡Bien hecho!",
                'text' => "El medicamento se ha editado con éxito.",
                'icon' => "success"

            ]
        );
        return redirect()->route('admin.medicines.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Medicine $medicine)
    {
        //
    }
}
