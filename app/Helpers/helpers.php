<?php
// Define una función que utiliza el fragmento de código y toma $input como argumento

use App\Models\Nutricionales\Input;

function renderInputSection($id, $inputs_solicitud)
{
    $inputValue = '';
    foreach ($inputs_solicitud as $inputItem) {
        if ($inputItem->input_id == $id) {
            $inputValue = $inputItem->valor;
            break;
        }
    }

    // Devuelve el valor de $inputValue
    return $inputValue;
}

// Define una función que utiliza el fragmento de código y toma $input como argumento
function renderInputMLSection($id, $inputs_solicitud)
{
    $inputValue = '';
    foreach ($inputs_solicitud as $inputItem) {
        if ($inputItem->input_id == $id) {
            $inputValue = sprintf('%.3f', $inputItem->valor_ml); // Cambiado a tres decimales
            break;
        }
    }

    // Devuelve el valor de $inputValue
    return $inputValue;
}

// Define una función que utiliza el fragmento de código y toma $input como argumento
function renderInputMLSobrellenadoSection($id, $inputs_solicitud)
{
    $inputValue = '';
    foreach ($inputs_solicitud as $inputItem) {
        if ($inputItem->input_id == $id) {
            $inputValue = sprintf('%.3f', $inputItem->valor_sobrellenado);
            break;
        }
    }

    // Devuelve el valor de $inputValue
    return $inputValue;
}

// Define una función que utiliza el fragmento de código y toma $input como argumento
function renderLoteSection($id, $inputs_solicitud)
{
    foreach ($inputs_solicitud as $inputItem) {
        if ($inputItem->input_id == $id && !empty($inputItem->lote)) {
            return $inputItem->lote;
        }
    }

    // Fallback: obtener lote del medicamento relacionado si se pasó como relación
    $input = Input::with('medicine')->find($id);
    return $input->medicine->lote ?? '';
}

// Define una función que utiliza el fragmento de código y toma $input como argumento
function renderCaducidadSection($id, $inputs_solicitud)
{
    foreach ($inputs_solicitud as $inputItem) {
        if ($inputItem->input_id == $id && !empty($inputItem->caducidad)) {
            return \Carbon\Carbon::parse($inputItem->caducidad)->format('Y-m-d');
        }
    }

    // Fallback: obtener caducidad del medicamento
    $input = Input::with('medicine')->find($id);
    return optional($input->medicine->caducidad)->format('Y-m-d') ?? '';
}




// Define una función que utiliza el fragmento de código y toma $input como argumento
function renderBolsaEvaInputSection($id, $inputs_solicitud)
{
    global $inputBolsaEva;

    foreach ($inputs_solicitud as $inputItem) {
        if ($inputItem->input_id == $id) {
            $inputBolsaEva = $inputItem->input_id;
            return $inputItem->input_id;
            break;
        }
    }
}


// Define una función que utiliza el fragmento de código y toma $input como argumento
function renderLoteBolsaEvaSection($inputs_solicitud)
{
    global $inputBolsaEva;
    $inputLote = '';
    foreach ($inputs_solicitud as $inputItem) {
        if ($inputItem->input_id == $inputBolsaEva) {
            $inputLote = $inputItem->lote;
            break;
        }
    }

    // Devuelve el valor de $inputValue
    return $inputLote;
}


// Define una función que utiliza el fragmento de código y toma $input como argumento
function renderCaducidadBolsaEvaSection($inputs_solicitud)
{
    global $inputBolsaEva;
    $inputCaducidad = '';
    foreach ($inputs_solicitud as $inputItem) {
        if ($inputItem->input_id == $inputBolsaEva) {
            $inputCaducidad = $inputItem->caducidad;
            break;
        }
    }

    // Devuelve el valor de $inputValue
    return $inputCaducidad;
}
