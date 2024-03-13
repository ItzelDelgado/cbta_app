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
use Barryvdh\DomPDF\Facade\Pdf;
use Spatie\LaravelIgnition\Recorders\DumpRecorder\Dump;

class SolicitudController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $solicitudes = Solicitud::with('user', 'solicitud_detail', 'solicitud_patient', 'input', 'user.hospital')
            ->get();
        //return $solicitudes;
        return view('admin.solicitudes.index', compact('solicitudes'));
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


        //return $filtered_inputs;
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


        // $validator = Validator::make($request->all(), [
        //     'i_28_mL' => 'nullable|numeric|mvi_mayor_que_peso:' . $request->input('peso'),
        //     'i_4_g/Kg' => 'nullable|numeric|aminoacidos_validaciones:' . $request->input('peso') . ',' . $request->input('npt'),
        //     'i_5_g/Kg' => 'nullable|numeric|aminoacidos_validaciones:' . $request->input('peso') . ',' . $request->input('npt'),
        //     'i_6_g/Kg' => 'nullable|numeric|aminoacidos_validaciones:' . $request->input('peso') . ',' . $request->input('npt'),
        //     'i_7_g/Kg' => 'nullable|numeric|aminoacidos_validaciones:' . $request->input('peso') . ',' . $request->input('npt'),
        //     'i_8_g/Kg' => 'nullable|numeric|dextrosa_validaciones:' . $request->input('peso'),
        //     'i_9_g/Kg' => 'nullable|numeric|lipidos_validaciones:' . $request->input('peso'),
        //     'i_10_g/Kg' => 'nullable|numeric|lipidos_validaciones:' . $request->input('peso'),
        //     'i_11_mEq/Kg' => 'nullable|numeric|sodio_validaciones:' . $request->input('peso') . ',' . $request->input('npt')
        //         . ',' . $request->input('i_11') . ',' . $request->input('i_12') . ',' . $request->input('i_13'),
        //     'i_12_mEq/Kg' => 'nullable|numeric|sodio_validaciones:' . $request->input('peso') . ',' . $request->input('npt')
        //         . ',' . $request->input('i_11') . ',' . $request->input('i_12') . ',' . $request->input('i_13'),
        //     'i_13_mEq/Kg' => 'nullable|numeric|sodio_validaciones:' . $request->input('peso') . ',' . $request->input('npt')
        //         . ',' . $request->input('i_11') . ',' . $request->input('i_12') . ',' . $request->input('i_13'),
        //     'i_14_mEq/Kg' => 'nullable|numeric|magnesio_validaciones:' . $request->input('peso') . ',' . $request->input('npt'),
        //     'i_15_mEq/Kg' => 'nullable|numeric|potasio_validaciones:' . $request->input('peso') . ',' . $request->input('npt')
        //         . ',' . $request->input('i_15') . ',' . $request->input('i_16') . ',' . $request->input('i_17'),
        //     'i_16_mEq/Kg' => 'nullable|numeric|potasio_validaciones:' . $request->input('peso') . ',' . $request->input('npt')
        //         . ',' . $request->input('i_15') . ',' . $request->input('i_16') . ',' . $request->input('i_17'),
        //     'i_17_mEq/Kg' => 'nullable|numeric|potasio_validaciones:' . $request->input('peso') . ',' . $request->input('npt')
        //         . ',' . $request->input('i_15') . ',' . $request->input('i_16') . ',' . $request->input('i_17'),
        //     'i_18_mEq/Kg' => 'nullable|numeric|calcio_validaciones:' . $request->input('peso') . ',' . $request->input('npt'),
        //     'i_20_g' => 'nullable|numeric|albumina_validaciones:' . $request->input('peso') . ',' . (100 * $request->input('i_20') / 20),
        //     'i_21_g' => 'nullable|numeric|albumina_validaciones:' . $request->input('peso') . ',' . (100 * $request->input('i_21') / 25),
        //     'i_22_g' => 'nullable|numeric|glutamina_validaciones:' . $request->input('peso') . ',' . (100 * $request->input('i_22') / 20),
        //     'i_23_mcg' => 'nullable|numeric|cromo_validaciones:' . $request->input('peso') . ',' . $request->input('npt'),
        //     'i_24_UI' => 'nullable|numeric|heparina_validaciones:',
        //     'i_25_mg' => 'nullable|numeric|carnitina_validaciones:' . $request->input('peso') . ',' . ($request->input('i_25') / 200),
        //     'i_26_UI' => 'nullable|numeric|insulina_validaciones:',
        //     'i_27_mcg' => 'nullable|numeric|manganeso_validaciones:' . $request->input('peso') . ',' . $request->input('npt'),
        //     'i_28_mL' => 'nullable|numeric|multivitaminico_validaciones:' . $request->input('peso') . ',' . ($request->input('i_28')),
        //     'i_29_mL' => 'nullable|numeric|oligoelementos_validaciones:' . $request->input('peso') . ',' . $request->input('npt'),
        //     'i_31_mcg' => 'nullable|numeric|selenio_validaciones:' . $request->input('peso') . ',' . $request->input('npt'),
        //     'i_34_mg' => 'nullable|numeric|zinc_validaciones:' . $request->input('peso') . ',' . $request->input('npt'),

        // ]);

        // if ($validator->fails()) {
        //     return redirect('/admin/solicitudes/create')
        //         ->withErrors($validator)
        //         ->withInput();
        // }


        //El sobrellenado y tiempo 0 y 24
        $solicitud_paciente = $request->only(['nombre_paciente', 'apellidos_paciente', 'servicio', 'cama', 'piso', 'registro', 'diagnostico', 'peso', 'fecha_nacimiento', 'sexo']);
        $solicitud_detalles = $request->only(['via_administracion', 'tiempo_infusion_min', 'sobrellenado_ml', 'volumen_total', 'npt', 'observaciones', 'fecha_hora_entrega', 'nombre_medico', 'cedula']);
        $solicitud_paciente_resp = SolicitudPatient::create($solicitud_paciente);
        $solicitud_detalles_resp = SolicitudDetail::create($solicitud_detalles);

        $user = Auth::user();
        //$solicitud_paciente_resp->id;
        $solicitud['user_id'] = $user->id;
        $solicitud['solicitud_detail_id'] = $solicitud_detalles_resp->id;
        $solicitud['solicitud_patient_id'] = $solicitud_paciente_resp->id;
        //me falto agregarle el atributo de que está aceptada
        $solicitud_nueva = Solicitud::create($solicitud);

        $only_inputs = $request->except(['nombre_paciente', 'apellidos_paciente', 'servicio', 'cama', 'piso', 'registro', 'diagnostico', 'peso', 'fecha_nacimiento', 'sexo', 'via_administracion', 'tiempo_infusion_min', 'sobrellenado_ml', 'volumen_total', 'npt', 'observaciones', 'fecha_hora_entrega', 'nombre_medico', 'cedula']);

        $filtered_inputs = array_filter($only_inputs, function ($value) {
            return $value !== null;
        });

        // Recuperar el modelo existente
        $registro = SolicitudDetail::find($solicitud_detalles_resp->id);



        $suma_volumen_ml = 0;

        foreach ($filtered_inputs as $key => $value) {

            preg_match('/_(\d+)_/', $key, $matches);

            if (isset($matches[1])) {
                // El número extraído se encuentra en $matches[1]
                $numero = $matches[1];

                // Realizar acciones con el número extraído
                // Realizar la consulta para obtener el nombre, div y mult relacionados al ID
                $resultado = Input::select('description', 'mult', 'div')->where('id', $numero)->first();
                //var_dump($resultado);
                // echo "Nombre: " . $resultado->description . "<br>";
                // echo "Div: " . $resultado->div . "<br>";
                // echo "Mult: " . $resultado->mult;

                $valor_ml = ($value) * $resultado->mult / $resultado->div;
                $suma_volumen_ml = $suma_volumen_ml + $valor_ml;
                $solicitud_inputs['solicitud_id'] = $solicitud_nueva->id;
                $solicitud_inputs['valor'] = $value;
                $solicitud_inputs['valor_ml'] = $valor_ml;
                $solicitud_inputs['input_id'] = $numero;
                SolicitudInput::create($solicitud_inputs);
                // echo "El valor en mililitros de " . $resultado->description . " es: " . $valor_ml;
            }
        }

        //dump($suma_volumen_ml);
        //dump("Imprimi antes la suma de los valores");
        if ($registro->sobrellenado_ml != null) {
            // && $registro->volumen_total != null){
            if ($registro->volumen_total == null || $registro->volumen_total == 0) {
                $porcentaje_sobrellenado = ($registro->sobrellenado_ml * 100) / $suma_volumen_ml;
                //dump($porcentaje_sobrellenado);
                //dump("Algoooo");
                //var_dump($porcentaje_sobrellenado);
                // Realizar la consulta para obtener el nombre, div y mult relacionados al ID
                $inputs_valores = SolicitudInput::select('id', 'valor_ml', 'valor_sobrellenado')
                    ->where('solicitud_id', $solicitud_nueva->id)
                    ->get();

                $suma_volumen_sobrellenado_ml = 0;
                //dump($inputs_valores);
                foreach ($inputs_valores as $input_val) {
                    //dump($input_val);
                    $valor_en_ml = $input_val->valor_ml;
                    //dump($valor_en_ml);
                    $valor_sobrellenado_ml = (($valor_en_ml * $porcentaje_sobrellenado) / 100) + $valor_en_ml;
                    //dump($valor_sobrellenado_ml);
                    // Realizar acciones con el número extraído
                    // Realizar la consulta para obtener el nombre, div y mult relacionados al ID
                    $suma_volumen_sobrellenado_ml = $suma_volumen_sobrellenado_ml + $valor_sobrellenado_ml;
                    $registro_input = SolicitudInput::find($input_val->id);
                    //dump("Valor de suma hasta el momento");
                    //dump($suma_volumen_sobrellenado_ml);
                    //dump("Imprimimos el registro de la bd");
                    //dump($registro_input);

                    $registro_input->valor_sobrellenado = $valor_sobrellenado_ml;
                    $registro_input->save();
                    //dump("Imprimimos el registro guardado");
                    //dump($registro_input);
                }
                //dump("Imprimimos la suma total");
                //dump($suma_volumen_sobrellenado_ml);

                $suma_volumen_sobrellenado_red_ml = round($suma_volumen_sobrellenado_ml, 2);
                $registro->suma_volumen_sobrellenado = $suma_volumen_sobrellenado_red_ml;
                $registro->volumen_total_final = $suma_volumen_sobrellenado_red_ml;
            } else {
                //dump("ingresaron un valor en el volumen total");
                $porcentaje_sobrellenado = ($registro->sobrellenado_ml * 100) / $registro->volumen_total;
                //dump($porcentaje_sobrellenado);

                // Realizar la consulta para obtener el nombre, div y mult relacionados al ID
                $inputs_valores = SolicitudInput::select('id', 'valor_ml', 'valor_sobrellenado')
                    ->where('solicitud_id', $solicitud_nueva->id)
                    ->get();

                $suma_volumen_sobrellenado_ml = 0;

                foreach ($inputs_valores as $input_val) {
                    // dump("Input valorrr----");
                    // dump($input_val);
                    $valor_en_ml = $input_val->valor_ml;
                    //dump($valor_en_ml);
                    $valor_sobrellenado_ml = (($valor_en_ml * $porcentaje_sobrellenado) / 100) + $valor_en_ml;
                    // dump("Valor de sobrellenado del input");
                    // dump($valor_sobrellenado_ml);
                    // Realizar acciones con el número extraído
                    // Realizar la consulta para obtener el nombre, div y mult relacionados al ID
                    $suma_volumen_sobrellenado_ml = $suma_volumen_sobrellenado_ml + $valor_sobrellenado_ml;
                    $registro_input = SolicitudInput::find($input_val->id);
                    // dump("Valor de suma hasta el momento");
                    // dump($suma_volumen_sobrellenado_ml);
                    // dump("Imprimimos el registro de la bd");
                    // dump($registro_input);

                    $registro_input->valor_sobrellenado = $valor_sobrellenado_ml;
                    $registro_input->save();
                    //dump("Imprimimos el registro guardado");
                    //dump($registro_input);
                }
                //dump("Imprimimos la suma total");
                //dump($suma_volumen_sobrellenado_ml);

                $suma_volumen_sobrellenado_red_ml = round($suma_volumen_sobrellenado_ml, 2);
                $registro->suma_volumen_sobrellenado = $suma_volumen_sobrellenado_red_ml;

                $agua_inyectable_ml = ($registro->volumen_total + $registro->sobrellenado_ml) - $suma_volumen_sobrellenado_red_ml;
                // dump("Imprimimos el valor de agua");
                // dump($agua_inyectable_ml);
                $registro->volumen_total_final = $suma_volumen_sobrellenado_red_ml + $agua_inyectable_ml;
                $solicitud_inputs['solicitud_id'] = $solicitud_nueva->id;
                $solicitud_inputs['valor'] = $agua_inyectable_ml;
                $solicitud_inputs['valor_ml'] = $agua_inyectable_ml;
                $solicitud_inputs['input_id'] = 37;
                SolicitudInput::create($solicitud_inputs);
            }
            //HACEMOS ALGO

        } else {
            //Si me ponen volumen total pero no sobrellenado
            if ($registro->volumen_total != null || $registro->volumen_total != 0) {
                $agua_inyectable_ml = ($registro->volumen_total) - $suma_volumen_ml;
                //dump("Imprimimos el valor de agua");
                //dump($agua_inyectable_ml);
                $solicitud_inputs['solicitud_id'] = $solicitud_nueva->id;
                $solicitud_inputs['valor'] = $agua_inyectable_ml;
                $solicitud_inputs['valor_ml'] = $agua_inyectable_ml;
                $solicitud_inputs['input_id'] = 37;
                $registro->volumen_total_final = $agua_inyectable_ml + $suma_volumen_ml;
                SolicitudInput::create($solicitud_inputs);
            } else {
                $registro->volumen_total_final = $suma_volumen_ml;
            }
        }
        // Modificar los atributos del modelo
        $suma_valores_red_ml = round($suma_volumen_ml, 2);
        $registro->suma_volumen = $suma_valores_red_ml;

        // Guardar el modelo actualizado
        $registro->save();

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
    // public function show(Solicitud $solicitud)
    // {
    //     // $solicitud = SolicitudInput::select('id')
    //     //     ->where('solicitud_id', $id)
    //     //     ->get();
    //     // var_dump("Entramos al controller");
    //     return $solicitud;
    //     //return response()->json($solicitud);
    // }
    public function show($id)
    {
        $solicitud = Solicitud::findOrFail($id);
        return response()->json($solicitud);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Solicitud $solicitud)
    {

        return $solicitud;
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

    public function ordenPreparacion()
    {
        $ordenPreparacion = "HOlaa";
        $pdf = Pdf::loadView('pdfs.orden-de-preparacion', \compact('ordenPreparacion'));

        return $pdf->stream();
    }

    public function remision()
    {
        $ordenPreparacion = "HOlaa";
        $pdf = Pdf::loadView('pdfs.remision', \compact('ordenPreparacion'));

        return $pdf->stream();
    }

    public function envio()
    {
        $ordenPreparacion = "HOlaa";
        $pdf = Pdf::loadView('pdfs.envio', \compact('ordenPreparacion'));

        return $pdf->stream();
    }
}
