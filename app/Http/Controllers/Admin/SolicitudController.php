<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Hospital;
use App\Models\Input;
use App\Models\Medicine;
use App\Models\Solicitud;
use App\Models\SolicitudDetail;
use App\Models\SolicitudInput;
use App\Models\SolicitudPatient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use DateInterval;
use DateTime;
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


    function calcularEdad($fechaNacimiento)
    {
        $fechaNacimiento = new DateTime($fechaNacimiento);
        $fechaActual = new DateTime();

        $edadAnios = $fechaActual->diff($fechaNacimiento)->y;
        $edadMeses = $fechaActual->diff($fechaNacimiento)->m;
        $edadDias = $fechaActual->diff($fechaNacimiento)->d;

        if ($edadDias < 0) {
            $edadMeses--;
            $ultimoDiaMesAnterior = $fechaActual->sub(new DateInterval('P1M'))->format('t');
            $edadDias = $ultimoDiaMesAnterior + $edadDias;
        }

        if ($edadMeses < 0) {
            $edadAnios--;
            $edadMeses = 12 + $edadMeses;
        }

        $edad = '';

        if ($edadAnios > 0) {
            $edad .= $edadAnios . ' año(s) ';
        }

        if ($edadMeses > 0) {
            $edad .= $edadMeses . ' mes(es) ';
        }

        if ($edadDias > 0) {
            $edad .= $edadDias . ' día(s)';
        }

        return $edad;
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
        $fecha_nacimiento = $request->input('fecha_nacimiento');
        $edad = $this->calcularEdad($fecha_nacimiento);
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
            'edad' => 'nullable',
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
        $solicitud_paciente['edad'] = $edad;
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
                $resultado = Input::select('id', 'description', 'mult', 'div')->where('id', $numero)->first();
                //var_dump($resultado);
                // echo "Nombre: " . $resultado->description . "<br>";
                // echo "Div: " . $resultado->div . "<br>";
                // echo "Mult: " . $resultado->mult;

                // if($resultado->id == 40){
                //     $valor_ml = ($value) * $resultado->mult / $resultado->div;
                // }
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

                $suma_volumen_sobrellenado_red_ml = $suma_volumen_sobrellenado_ml;
                $registro->suma_volumen_sobrellenado = $suma_volumen_sobrellenado_red_ml;
                $registro->volumen_total_final = $suma_volumen_sobrellenado_red_ml;
                //$registro->volumen_total = $suma_volumen_sobrellenado_red_ml;
                //$registro->volumen_total = $suma_volumen_ml;
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

                $suma_volumen_sobrellenado_red_ml = $suma_volumen_sobrellenado_ml;
                $registro->suma_volumen_sobrellenado = $suma_volumen_sobrellenado_red_ml;

                $agua_inyectable_ml = round(($registro->volumen_total + $registro->sobrellenado_ml) - $suma_volumen_sobrellenado_red_ml, 2);
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
                $agua_inyectable_ml = round(($registro->volumen_total) - $suma_volumen_ml, 2);
                //dump("Imprimimos el valor de agua");
                //dump($agua_inyectable_ml);
                $solicitud_inputs['solicitud_id'] = $solicitud_nueva->id;
                $solicitud_inputs['valor'] = $agua_inyectable_ml;
                $solicitud_inputs['valor_ml'] = $agua_inyectable_ml;
                $solicitud_inputs['input_id'] = 37;
                $registro->volumen_total_final = $agua_inyectable_ml + $suma_volumen_ml;
                SolicitudInput::create($solicitud_inputs);
            } else {
                //$registro->volumen_total = $suma_volumen_ml;
                $registro->volumen_total_final = $suma_volumen_ml;
            }
        }
        // Modificar los atributos del modelo
        $suma_valores_red_ml = $suma_volumen_ml;
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
     * Show the form for editing the specified resource.
     */
    public function edit(Solicitud $solicitud)
    {
        //$solicitudes = Solicitud::with('user', 'solicitud_detail', 'solicitud_patient', 'input', 'user.hospital')
        // ->get();
        $solicitud = Solicitud::with('user', 'solicitud_detail', 'solicitud_patient', 'input', 'user.hospital')
            ->find($solicitud->id);
        // $inputs_solicitud = Solicitud::with('input')->get()->pluck('input')->flatten();
        $inputs_solicitud = SolicitudInput::where('solicitud_id', $solicitud['id'])->get();
        //return $inputs_solicitud;
        $inputs = Input::Join('categories', 'inputs.category_id', '=', 'categories.id')
            ->where('inputs.is_active', 1)
            ->orderBy('orden_enum', 'asc')
            ->select('inputs.*', 'inputs.id AS input_id') // Renombramos 'nombre' de 'categories' a 'nombre_categoria'
            ->get();

        return view('admin.solicitudes.edit', compact('solicitud', 'inputs', 'inputs_solicitud'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Solicitud $solicitud)
    {
        //return $request->all();
        $fecha_nacimiento = $request->input('fecha_nacimiento');
        $edad = $this->calcularEdad($fecha_nacimiento);
        //return $request->all();
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


        $solicitud_paciente = $request->only(['nombre_paciente', 'apellidos_paciente', 'servicio', 'cama', 'piso', 'registro', 'diagnostico', 'peso', 'fecha_nacimiento', 'sexo']);
        $solicitud_detalles = $request->only(['via_administracion', 'tiempo_infusion_min', 'sobrellenado_ml', 'volumen_total', 'npt', 'observaciones', 'fecha_hora_entrega', 'nombre_medico', 'cedula']);
        $is_aprobada_value = $request->only(['is_aprobada']);
        $set_infusion = $request->only(['i_40']);
        $solicitud_paciente['edad'] = $edad;
        $solicitud_patient_u = SolicitudPatient::find($solicitud['solicitud_patient_id']);
        $solicitud_detail_u = SolicitudDetail::find($solicitud['solicitud_detail_id']);
        $solicitud_patient_u->update($solicitud_paciente);
        $solicitud_detail_u->update($solicitud_detalles);

        // Eliminar todos los elementos de SolicitudInput que corresponden a la solicitud_id proporcionada
        // $solicitudesInput = SolicitudInput::where('solicitud_id', $solicitud['id'])->get();
        // print_r($solicitudesInput);
        SolicitudInput::where('solicitud_id', $solicitud['id'])->delete();
        $request2 = $request->all();

        $tripletas = [];

        foreach ($request2 as $key => $value) {
            // Verificar si la clave sigue el patrón 'i_numero' y si el valor no es null
            if (preg_match('/^i_[0-9]+$/', $key) && $value !== null) {
                $numero = explode('_', $key)[1]; // Obtener el número después del primer guion bajo
                $tripletas[$numero]["i_$numero"] = $value;

                // Verificar si existen los valores de 'l_numero' y 'c_numero' correspondientes y guardarlos si no son null
                $l_key = "l_$numero";
                $c_key = "c_$numero";

                // Establecer 'l_numero' como null si no existe o es null
                $tripletas[$numero][$l_key] = isset($request2[$l_key]) ? $request2[$l_key] : null;

                // Establecer 'c_numero' como null si no existe o es null
                $tripletas[$numero][$c_key] = isset($request2[$c_key]) ? $request2[$c_key] : null;
            }
        }


        $registro = SolicitudDetail::find($solicitud['solicitud_detail_id']);


        $suma_volumen_ml = 0;

        foreach ($tripletas as $numero => $tripleta) {
            // Realizar acciones con el número extraído
            // Realizar la consulta para obtener el nombre, div y mult relacionados al ID
            $resultado = Input::select('description', 'mult', 'div')->where('id', $numero)->first();
            $valor_unidad = $tripleta["i_{$numero}"];

            
            $valor_ml = ($valor_unidad) * $resultado->mult / $resultado->div;
            // if($numero == 40){
            //     if($valor_unidad == 1){
            //         $suma_volumen_ml = $suma_volumen_ml + $valor_ml + 1; 
            //     }
            // }
            $suma_volumen_ml = $suma_volumen_ml + $valor_ml;

            $medicina = Medicine::select('id', 'precio_ml')->where('input_id', $numero)->first();
            $solicitud_inputs['solicitud_id'] = $solicitud['id'];
            $solicitud_inputs['valor'] = $valor_unidad;
            $solicitud_inputs['valor_ml'] = $valor_ml;
            $solicitud_inputs['input_id'] = $numero;
            $solicitud_inputs['precio_ml'] = $valor_ml * $medicina['precio_ml'];
            $solicitud_inputs['lote'] = $tripleta["l_{$numero}"];
            $solicitud_inputs['caducidad'] = $tripleta["c_{$numero}"];

            SolicitudInput::create($solicitud_inputs);
        }
        //var_dump("Mandamosssss la lista de inputsssssss");
        //var_dump(SolicitudInput::where('solicitud_id', $solicitud['id']));

        //print_r($suma_volumen_ml);

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
                // $inputs_valores = SolicitudInput::select('id', 'valor_ml', 'valor_sobrellenado', 'input_id')
                //     ->where('solicitud_id', $solicitud->id)
                //     ->get();

                $inputs_valores = SolicitudInput::select('id', 'valor_ml', 'valor_sobrellenado', 'input_id')
                    ->where('solicitud_id', $solicitud->id)
                    ->whereNotIn('input_id', [40]) // Excluir input_id 37 y 40
                    ->get();
                // $inputs_valores = SolicitudInput::select('id', 'valor_ml', 'valor_sobrellenado', 'input_id')
                // ->where('solicitud_id', $solicitud->id)
                // ->whereNotIn('input_id', [37, 40]) // Excluir input_id 37 y 40
                // ->get();
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
                    $medicina = Medicine::select('id', 'precio_ml')->where('input_id', $input_val->input_id)->first();
                    $registro_input->precio_ml = $valor_sobrellenado_ml * $medicina['precio_ml'];
                    $registro_input->save();
                    //dump("Imprimimos el registro guardado");
                    //dump($registro_input);
                }
                //dump("Imprimimos la suma total");
                //dump($suma_volumen_sobrellenado_ml);
                // $input_agua_ml = SolicitudInput::select('id', 'valor_ml', 'valor_sobrellenado', 'input_id')
                // ->where('solicitud_id', $solicitud->id)
                // ->where('input_id', 37) // Obtener solo los registros con input_id igual a 37
                // ->get();
                $registro->suma_volumen_sobrellenado = $suma_volumen_sobrellenado_ml;
                $registro->volumen_total_final = $suma_volumen_sobrellenado_ml;
                // $registro->volumen_total_final = $suma_volumen_sobrellenado_ml + $input_agua_ml->valor_ml;
                //$registro->volumen_total = $suma_volumen_ml;
            } else { //Si tiene sobrellenado y volumen total
                //dump("ingresaron un valor en el volumen total");
                $porcentaje_sobrellenado = ($registro->sobrellenado_ml * 100) / $registro->volumen_total;
                //dump($porcentaje_sobrellenado);

                // Realizar la consulta para obtener el nombre, div y mult relacionados al ID
                $inputs_valores = SolicitudInput::select('id', 'valor_ml', 'valor_sobrellenado', 'input_id')
                    ->where('solicitud_id', $solicitud->id)
                    ->whereNotIn('input_id', [40]) // Excluir input_id 37 y 40
                    ->get();

                $suma_volumen_sobrellenado_ml = 0;
                $suma_volumen_mls = 0;

                foreach ($inputs_valores as $input_val) {
                    // dump("Input valorrr----");
                    // dump($input_val);
                    $valor_en_ml = $input_val->valor_ml;
                    $suma_volumen_mls = $suma_volumen_mls + $input_val->valor_ml;
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
                    $medicina = Medicine::select('id', 'precio_ml')->where('input_id', $input_val->input_id)->first();
                    $registro_input->precio_ml = $valor_sobrellenado_ml * $medicina['precio_ml'];

                    $registro_input->save();
                    //dump("Imprimimos el registro guardado");
                    //dump($registro_input);
                }
                //dump("Imprimimos la suma total");
                //dump($suma_volumen_sobrellenado_ml);

                $registro->suma_volumen_sobrellenado = $suma_volumen_sobrellenado_ml;

                $agua_inyectable_ml = round($registro->volumen_total  - $suma_volumen_mls, 2);
                // dump("Imprimimos el valor de agua");
                // dump($agua_inyectable_ml);
                $registro->volumen_total_final = $suma_volumen_mls + $agua_inyectable_ml + $registro->sobrellenado_ml;
                $medicina_agua = Medicine::select('id', 'precio_ml')
                    ->where('input_id', 37) // Condición para input_id igual a 37
                    ->first();
                $agua_valor_sobrellenado = (($agua_inyectable_ml * $porcentaje_sobrellenado) / 100) + $agua_inyectable_ml;
                $solicitud_inputs['solicitud_id'] = $solicitud->id;
                $solicitud_inputs['valor'] = $agua_inyectable_ml;
                $solicitud_inputs['valor_ml'] = $agua_inyectable_ml;
                $solicitud_inputs['input_id'] = 37;
                $solicitud_inputs['valor_sobrellenado'] = $agua_valor_sobrellenado;
                $solicitud_inputs['precio_ml'] = $agua_valor_sobrellenado * $medicina_agua['precio_ml'];
                SolicitudInput::create($solicitud_inputs);
            }
            //HACEMOS ALGO

        } else {
            //Si me ponen volumen total pero no sobrellenado
            if ($registro->volumen_total != null || $registro->volumen_total != 0) {
                //dump($set_infusion);
                if($set_infusion['i_40'] == "1"){
                    $agua_inyectable_ml = (($registro->volumen_total) - $suma_volumen_ml) + 1;
                    
                    //dump("Entramos al if");
                }else{
                    $agua_inyectable_ml = ($registro->volumen_total) - $suma_volumen_ml;
                }
                
                
                //dump("Imprimimos el valor de agua");
                //dump($agua_inyectable_ml);
                $medicina_agua = Medicine::select('id', 'precio_ml')
                    ->where('input_id', 37) // Condición para input_id igual a 37
                    ->first();
                
                $solicitud_inputs['solicitud_id'] = $solicitud->id;
                $solicitud_inputs['valor'] = $agua_inyectable_ml;
                $solicitud_inputs['valor_ml'] = $agua_inyectable_ml;
                $solicitud_inputs['input_id'] = 37;
                $solicitud_inputs['precio_ml'] = $agua_inyectable_ml * $medicina_agua['precio_ml'];
                $registro->volumen_total_final = $agua_inyectable_ml + $suma_volumen_ml;
                SolicitudInput::create($solicitud_inputs);
            } else {
                //$registro->volumen_total = $suma_volumen_ml;
                $registro->volumen_total_final = $suma_volumen_ml;
            }
        }
        // Modificar los atributos del modelo
        $suma_valores_red_ml = $suma_volumen_ml;
        $registro->suma_volumen = $suma_valores_red_ml;
        $solicitud['is_aprobada'] = $is_aprobada_value;

        // Guardar el modelo actualizado
        $registro->save();
        $solicitud->update($solicitud['is_aprobada']);
        if ($solicitud['is_aprobada'] == 'Pendiente') {
            session()->flash(
                'swal',
                [
                    'title' => "Solicitud Actualizada",
                    'text' => "La solicitud se ha editado con éxito.",
                    'icon' => "success"

                ]
            );
        } elseif ($solicitud['is_aprobada'] == 'Aprobada') {

            session()->flash(
                'swal',
                [
                    'title' => "Solicitud Aprobada",
                    'text' => "La solicitud se ha aprobado con éxito.",
                    'icon' => "success"

                ]
            );
        } elseif ($solicitud['is_aprobada'] == 'No Aprobada') {
            session()->flash(
                'swal',
                [
                    'title' => "Solicitud Rechazada",
                    'text' => "La solicitud se ha rechazado.",
                    'icon' => "warning"

                ]
            );
        }

        //METER TODO DENTRO DE ESTOS IF PARA MANEJAR QUE PASA

        return redirect()->route('admin.solicitudes.index');
        // print_r($solicitud['id']);
        // print_r($solicitud['solicitud_detail_id']);
        // print_r($solicitud['solicitud_patient_id']);
        //print_r($solicitud_patient_u);
        //print_r($solicitud_detail_u);

    }

    /**
     * Display the specified resource.
     */
    public function show(Solicitud $solicitud)
    {
        //$solicitudes = Solicitud::with('user', 'solicitud_detail', 'solicitud_patient', 'input', 'user.hospital')
        // ->get();
        $solicitud_detalles = Solicitud::with('user', 'solicitud_detail', 'solicitud_patient', 'input', 'user.hospital')
            ->find($solicitud->id);
        // $inputs_solicitud = Solicitud::with('input')->get()->pluck('input')->flatten();
        //$inputs_solicitud = SolicitudInput::where('solicitud_id', $solicitud['id'])->get();
        $inputs = Input::Join('categories', 'inputs.category_id', '=', 'categories.id')
            ->where('inputs.is_active', 1)
            ->orderBy('orden_enum', 'asc')
            ->select('inputs.*', 'inputs.id AS input_id') // Renombramos 'nombre' de 'categories' a 'nombre_categoria'
            ->get();

        // $inputs_solicitud = Solicitud::with('input')->get()->pluck('input')->flatten();
        $inputs_solicitud = SolicitudInput::where('solicitud_id', $solicitud['id'])->get();
        return view('admin.solicitudes.show', compact('solicitud', 'inputs_solicitud', 'solicitud_detalles', 'inputs'));
        // return $solicitud_detalles;

    }

    public function ordenPreparacion(Solicitud $solicitud)
    {
        $inputs_solicitud = SolicitudInput::where('solicitud_id', $solicitud['id'])
            ->with('input.medicine') // Cargar la relación 'medicine' a través de 'input'
            ->get();

        //return $inputs_solicitud;
        $solicitud_detalles = Solicitud::with('user', 'solicitud_detail', 'solicitud_patient', 'input', 'user.hospital')
            ->find($solicitud->id);

        //return $solicitud_detalles;
        $pdf = Pdf::loadView('pdfs.orden-de-preparacion', \compact('solicitud_detalles', 'inputs_solicitud'));

        return $pdf->stream();
    }

    public function remision(Solicitud $solicitud)
    {
        $inputs_solicitud = SolicitudInput::where('solicitud_id', $solicitud['id'])
            ->with('input.medicine') // Cargar la relación 'medicine' a través de 'input'
            ->get();

        //return $inputs_solicitud;
        $solicitud_detalles = Solicitud::with('user', 'solicitud_detail', 'solicitud_patient', 'input', 'user.hospital')
            ->find($solicitud->id);
        //return $solicitud_detalles;
        $ordenPreparacion = "HOlaa";
        $pdf = Pdf::loadView('pdfs.remision', \compact('solicitud_detalles', 'inputs_solicitud'));

        return $pdf->stream();
    }

    public function envio(Solicitud $solicitud)
    {
        $inputs_solicitud = SolicitudInput::where('solicitud_id', $solicitud['id'])
            ->whereNotIn('input_id', [40]) // Excluir input_id 40
            ->with('input.medicine') // Cargar la relación 'medicine' a través de 'input'
            ->get();

        //return $inputs_solicitud;
        $solicitud_detalles = Solicitud::with('user', 'solicitud_detail', 'solicitud_patient', 'input', 'user.hospital')
            ->find($solicitud->id);
        //return $solicitud_detalles;

        $pdf = Pdf::loadView('pdfs.envio', \compact('solicitud_detalles', 'inputs_solicitud'));

        return $pdf->stream();
    }

    public function etiqueta(Solicitud $solicitud)
    {

        $inputs_solicitud = SolicitudInput::where('solicitud_id', $solicitud['id'])
            ->whereNotIn('input_id', [40]) // Excluir input_id 40
            ->with('input.medicine') // Cargar la relación 'medicine' a través de 'input'
            ->get();

        //return $inputs_solicitud;
        $solicitud_detalles = Solicitud::with('user', 'solicitud_detail', 'solicitud_patient', 'input', 'user.hospital')
            ->find($solicitud->id);
        //return $solicitud_detalles;
        $customPaper = [0, 0, 368.50, 255.12]; // 9cm x 13cm en puntos
        $pdf = Pdf::loadView('pdfs.etiqueta', \compact('solicitud_detalles', 'inputs_solicitud'))
            ->setPaper($customPaper, 'landscape');

        return $pdf->stream();
    }
}
