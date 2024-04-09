<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Hospital;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::paginate();
        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $hospitals = Hospital::all();
        return view('admin.users.create', compact('hospitals'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate(([
            'name' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'username' => 'required|string|max:255',
            'password' => 'required|string|max:12',
            'hospital_id' => 'required|exists:hospitals,id',
        ]));


        $password = Hash::make($request->password);
        $request->merge(['password' => $password]);

        User::create($request->all());

        session()->flash(
            'swal',
            [
                'title' => "¡Bien hecho!",
                'text' => "El usuario se ha creado con éxito.",
                'icon' => "success"

            ]
        );
        return redirect()->route('admin.users.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {

        $hospitals = Hospital::all();
        return view('admin.users.edit', compact('user', 'hospitals'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $request->validate(([
            'name' => 'string|max:255',
            'lastname' => 'string|max:255',
            'username' => 'string|max:255',
            'password' => 'string',
            'hospital_id' => 'exists:hospitals,id',
        ]));

        if ($request->filled('password')) {
            $password = Hash::make($request->password);
            $request->merge(['password' => $password]);
        }

        //User::create($request->all());
        $user->update($request->all());
        session()->flash(
            'swal',
            [
                'title' => "¡Bien hecho!",
                'text' => "El usuario se ha editado con éxito.",
                'icon' => "success"

            ]
        );
        return redirect()->route('admin.users.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        //
    }
}
