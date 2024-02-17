<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Hospital;
use App\Models\Solicitud;
use App\Models\SolicitudDetail;
use App\Models\SolicitudInput;
use App\Models\SolicitudPatient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SolicitudController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
       //$hospitals = Hospital::paginate();
        return view('admin.solicitudes.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

        return view('admin.solicitudes.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        
        // $solicitud_paciente->validate(([

        // ]));
        $request->validate([
            'nombre_paciente' => 'required|string|max:255',
            'servicio' => 'required|string|max:100',
            'cama' => 'nullable|string|max:50',
            'piso' => 'nullable|string|max:50',
            'registro' => 'nullable|string|max:50',
            'diagnostico' => 'nullable|string|max:255',
            'peso' => 'nullable|numeric',
            'fecha_nacimiento' => 'required|date',
            'sexo'=>'nullable',
            'via_administracion' => 'required',
            'tiempo_infusion_min' => 'required|numeric',
            'sobrellenado_ml' => 'required|numeric',
            'volumen_total' => 'required|string|max:50',
            'npt' => 'required',
            'observaciones' => 'nullable|string|max:255',
            'fecha_entrega' => 'required|date',
            'hora_entrega' => 'required|date_format:H:i',
            'nombre_medico'=>'required|string|max:255',
            'cedula'=>'required|string|max:50',
        ]);

        $solicitud_paciente = $request->only(['nombre_paciente', 'servicio', 'cama', 'piso', 'registro', 'diagnostico', 'peso', 'fecha_nacimiento', 'sexo']);
        $solicitud_detalles = $request->only(['via_administracion', 'tiempo_infusion_min', 'sobrellenado_ml', 'volumen_total', 'npt', 'observaciones', 'fecha_entrega', 'hora_entrega', 'nombre_medico', 'cedula']);
        $solicitud_paciente_resp=SolicitudPatient::create($solicitud_paciente);
        $solicitud_detalles_resp=SolicitudDetail::create($solicitud_detalles);
        $user = Auth::user();
        $solicitud_paciente_resp->id;
        $solicitud['user_id'] = $user->id;
        $solicitud['solicitud_detail_id'] = $solicitud_detalles_resp->id;
        $solicitud['solicitud_patient_id'] = $solicitud_paciente_resp->id;
        //me falto agregarle el atributo de que está aceptada
        $solicitud_nueva = Solicitud::create($solicitud);
        session()->flash(
            'swal',
            [
                'title' => "¡Bien hecho!",
                'text' => "La solicitud se ha creado con éxito.",
                'icon' => "success"

            ]
        );
        return redirect()->route('admin.solicitudes.index');

    }

    /**
     * Display the specified resource.
     */
    public function show(Solicitud $solicitud)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Solicitud $solicitud)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Solicitud $solicitud)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Solicitud $solicitud)
    {
        //
    }
}
