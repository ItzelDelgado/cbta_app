<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Hospital;
use Illuminate\Http\Request;

class HospitalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $hospitals = Hospital::paginate();
        return view('admin.hospitals.index', compact('hospitals'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.hospitals.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate(([
            'name'=>'required|string|max:255',
            'adress'=>'required|string|max:400',
        ]));
        
        Hospital::create($request->all());

        session()->flash('swal',[
            'title'=>"¡Bien hecho!",
            'text'=>"El hospital se ha creado con éxito.",
            'icon'=>"success"
            
            ]
        );
        return redirect()->route('admin.hospitals.index');
        //return $request->all();
    }

    /**
     * Display the specified resource.
     */
    // public function show(Hospital $hospital)
    // {
    //     return view('admin.hospitals.show');
    // }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Hospital $hospital)
    {
        //return $hospital;
        return view('admin.hospitals.edit',compact('hospital'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Hospital $hospital)
    {
        $request->validate(([
            'name'=>'required|string|max:255',
            'adress'=>'required|string|max:400',
            'is_active'=>'required|boolean',
        ]));

        $hospital->update($request->all());
        session()->flash('swal',[
            'title'=>"¡Bien hecho!",
            'text'=>"El hospital se ha actualizado con éxito.",
            'icon'=>"success"
            ]
        );
        return redirect()->route('admin.hospitals.index');
        //return $request->all();
        // return view('admin.hospitals.edit',compact('hospital'));
    }

    /**
     * Remove the specified resource from storage.
     */
    // public function destroy(Hospital $hospital)
    // {
    //     //
    // }
}
