<?php
// Define una función que utiliza el fragmento de código y toma $input como argumento
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
            $inputValue = number_format($inputItem->valor_ml, 2);
            break;
        }
    }

    // Devuelve el valor de $inputValue
    return $inputValue;
}

// Define una función que utiliza el fragmento de código y toma $input como argumento
function renderLoteSection($id, $inputs_solicitud)
{
    $inputLote = '';
    foreach ($inputs_solicitud as $inputItem) {
        if ($inputItem->input_id == $id) {
            $inputLote = $inputItem->lote;
            break;
        }
    }

    // Devuelve el valor de $inputValue
    return $inputLote;
}


// Define una función que utiliza el fragmento de código y toma $input como argumento
function renderCaducidadSection($id, $inputs_solicitud)
{
    $inputCaducidad = '';
    foreach ($inputs_solicitud as $inputItem) {
        if ($inputItem->input_id == $id) {
            $inputCaducidad = $inputItem->caducidad;
            break;
        }
    }

    // Devuelve el valor de $inputValue
    return $inputCaducidad;
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