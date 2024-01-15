<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Medicine;
use Illuminate\Http\Request;

class MedicineController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $medicines = Medicine::paginate();
        return view('admin.medicines.index', compact('medicines'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all();
        return view('admin.medicines.create',compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate(([
            'denominacion_generica'=>'required|string|max:255',
            'nombre_comercial'=>'required|string|max:255',
            'descripcion'=>'nullable|string|max:255',
            'precio_ml'=>'required|numeric|regex:/^[0-9]+(\.[0-9][0-9][0-9]?)?$/',
            'presentacion_ml'=>'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
        ]));
        
        Medicine::create($request->all());

        session()->flash('swal',[
            'title'=>"¡Bien hecho!",
            'text'=>"El medicamento se ha creado con éxito.",
            'icon'=>"success"
            
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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Medicine $medicine)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Medicine $medicine)
    {
        //
    }
}
