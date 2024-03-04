<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Hospital;
use App\Models\Input;
use App\Models\Solicitud;
use App\Models\SolicitudDetail;
use App\Models\SolicitudInput;
use App\Models\SolicitudPatient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

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

        $inputs = Input::Join('categories', 'inputs.category_id', '=', 'categories.id')
            ->where('inputs.is_active', 1)
            ->orderBy('orden_enum', 'asc')
            ->select('inputs.*', 'inputs.id AS input_id') // Renombramos 'nombre' de 'categories' a 'nombre_categoria'
            ->get();

        return view('admin.solicitudes.create', compact('inputs'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //return $request->all();
        // $solicitud_paciente->validate(([

        function conversion_to_ml($mult, $valor, $div)
        {
            return $mult($valor)/$div;
        }

        $only_inputs = $request->except(['nombre_paciente', 'apellidos_paciente', 'servicio', 'cama', 'piso', 'registro', 'diagnostico', 'peso', 'fecha_nacimiento', 'sexo','via_administracion', 'tiempo_infusion_min', 'sobrellenado_ml', 'volumen_total', 'npt', 'observaciones', 'fecha_hora_entrega', 'nombre_medico', 'cedula']);
        return count($only_inputs);
        // ]));
        $request->validate([
            'nombre_paciente' => 'required|string|max:255',
            'apellidos_paciente' => 'required|string|max:255',
            'servicio' => 'required|string|max:100',
            'cama' => 'nullable|string|max:50',
            'piso' => 'nullable|string|max:50',
            'registro' => 'nullable|string|max:50',
            'diagnostico' => 'nullable|string|max:255',
            'peso' => 'required|numeric',
            'fecha_nacimiento' => 'required|date',
            'sexo' => 'nullable',
            'via_administracion' => 'required',
            'tiempo_infusion_min' => 'nullable|numeric',
            'sobrellenado_ml' => 'nullable|numeric',
            'volumen_total' => 'nullable|numeric',
            'npt' => 'required',
            'observaciones' => 'nullable|string|max:255',
            'fecha_hora_entrega' => 'required|date_format:Y-m-d\TH:i',
            'nombre_medico' => 'required|string|max:255',
            'cedula' => 'required|string|max:50',
        ]);


        $validator = Validator::make($request->all(), [
            'i_28_mL' => 'nullable|numeric|mvi_mayor_que_peso:' . $request->input('peso'),
            'i_4_g/Kg' => 'nullable|numeric|aminoacidos_validaciones:' . $request->input('peso') . ',' . $request->input('npt'),
            'i_8_g/Kg' => 'nullable|numeric|dextrosa_validaciones:' . $request->input('peso'),
            'i_9_g/Kg' => 'nullable|numeric|lipidos_validaciones:' . $request->input('peso'),
            'i_10_g/Kg' => 'nullable|numeric|lipidos_validaciones:' . $request->input('peso'),
            'i_11_mEq/Kg' => 'nullable|numeric|sodio_validaciones:' . $request->input('peso') . ',' . $request->input('npt')
                . ',' . $request->input('i_11') . ',' . $request->input('i_12') . ',' . $request->input('i_13'),
            'i_12_mEq/Kg' => 'nullable|numeric|sodio_validaciones:' . $request->input('peso') . ',' . $request->input('npt')
                . ',' . $request->input('i_11') . ',' . $request->input('i_12') . ',' . $request->input('i_13'),
            'i_13_mEq/Kg' => 'nullable|numeric|sodio_validaciones:' . $request->input('peso') . ',' . $request->input('npt')
                . ',' . $request->input('i_11') . ',' . $request->input('i_12') . ',' . $request->input('i_13'),
            'i_14_mEq/Kg' => 'nullable|numeric|magnesio_validaciones:' . $request->input('peso') . ',' . $request->input('npt'),
            'i_15_mEq/Kg' => 'nullable|numeric|potasio_validaciones:' . $request->input('peso') . ',' . $request->input('npt')
                . ',' . $request->input('i_15') . ',' . $request->input('i_16') . ',' . $request->input('i_17'),
            'i_16_mEq/Kg' => 'nullable|numeric|potasio_validaciones:' . $request->input('peso') . ',' . $request->input('npt')
                . ',' . $request->input('i_15') . ',' . $request->input('i_16') . ',' . $request->input('i_17'),
            'i_17_mEq/Kg' => 'nullable|numeric|potasio_validaciones:' . $request->input('peso') . ',' . $request->input('npt')
                . ',' . $request->input('i_15') . ',' . $request->input('i_16') . ',' . $request->input('i_17'),
            'i_18_mEq/Kg' => 'nullable|numeric|calcio_validaciones:' . $request->input('peso') . ',' . $request->input('npt'),
            'i_20_g' => 'nullable|numeric|albumina_validaciones:' . $request->input('peso') . ',' . (100 * $request->input('i_20') / 20),
            'i_21_g' => 'nullable|numeric|albumina_validaciones:' . $request->input('peso') . ',' . (100 * $request->input('i_21') / 25),
            'i_22_g' => 'nullable|numeric|glutamina_validaciones:' . $request->input('peso') . ',' . (100 * $request->input('i_22') / 20),
            'i_23_mcg' => 'nullable|numeric|cromo_validaciones:' . $request->input('peso') . ',' . $request->input('npt'),
            'i_24_UI' => 'nullable|numeric|heparina_validaciones:',
            'i_25_mg' => 'nullable|numeric|carnitina_validaciones:' . $request->input('peso') . ',' . ($request->input('i_25') / 200),
            'i_26_UI' => 'nullable|numeric|insulina_validaciones:',
            'i_27_mcg' => 'nullable|numeric|manganeso_validaciones:' . $request->input('peso') . ',' . $request->input('npt'),
            'i_28_mL' => 'nullable|numeric|multivitaminico_validaciones:' . $request->input('peso') . ',' . ($request->input('i_28')),
            'i_29_mL' => 'nullable|numeric|oligoelementos_validaciones:' . $request->input('peso') . ',' . $request->input('npt'),
            'i_31_mcg' => 'nullable|numeric|selenio_validaciones:' . $request->input('peso') . ',' . $request->input('npt'),
            'i_34_mg' => 'nullable|numeric|zinc_validaciones:' . $request->input('peso') . ',' . $request->input('npt'),

        ]);

        if ($validator->fails()) {
            return redirect('/admin/solicitudes/create')
                ->withErrors($validator)
                ->withInput();
        }
        //El sobrellenado y tiempo 0 y 24
        $solicitud_paciente = $request->only(['nombre_paciente', 'apellidos_paciente', 'servicio', 'cama', 'piso', 'registro', 'diagnostico', 'peso', 'fecha_nacimiento', 'sexo']);
        $solicitud_detalles = $request->only(['via_administracion', 'tiempo_infusion_min', 'sobrellenado_ml', 'volumen_total', 'npt', 'observaciones', 'fecha_hora_entrega', 'nombre_medico', 'cedula']);
        $solicitud_paciente_resp = SolicitudPatient::create($solicitud_paciente);
        $solicitud_detalles_resp = SolicitudDetail::create($solicitud_detalles);

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
