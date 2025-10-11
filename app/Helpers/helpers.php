<?php

use App\Models\Nutricionales\Input;
use Carbon\Carbon;

/**
 * Convierte cualquier valor a float de forma robusta:
 * - Acepta strings con comas, espacios, "ml", etc.
 * - Soporta notación científica.
 * - null, vacío o no numérico => 0.0
 */
function toFloat($value): float {
    if ($value === null) return 0.0;
    $v = preg_replace('/[^\d\.\-eE]/', '', (string)$value); // deja solo dígitos, punto y signo
    if ($v === '' || $v === '.' || $v === '-') return 0.0;
    return (float)$v;
}

/**
 * Formatea cualquier valor a 3 decimales (para mostrar en la vista).
 */
function fmt3($value): string {
    return number_format(toFloat($value), 3, '.', '');
}

/**
 * Devuelve el valor crudo del input (texto). Úsalo con toFloat() si lo necesitas numérico.
 */
function renderInputSection($id, $inputs_solicitud) {
    $inputValue = '';
    foreach ($inputs_solicitud as $inputItem) {
        if ($inputItem->input_id == $id) {
            $inputValue = $inputItem->valor;
            break;
        }
    }
    return $inputValue;
}

/**
 * Devuelve el valor en ML como float (limpio). No formatea; eso se hace en la vista.
 */
function renderInputMLSection($id, $inputs_solicitud): float {
    $value = null;
    foreach ($inputs_solicitud as $inputItem) {
        if ($inputItem->input_id == $id) {
            $value = $inputItem->valor_ml; // puede venir string o null
            break;
        }
    }
    return toFloat($value);
}

/**
 * Devuelve el valor de sobrellenado en ML como float (limpio).
 */
function renderInputMLSobrellenadoSection($id, $inputs_solicitud): float {
    $value = null;
    foreach ($inputs_solicitud as $inputItem) {
        if ($inputItem->input_id == $id) {
            $value = $inputItem->valor_sobrellenado; // puede venir string o null
            break;
        }
    }
    return toFloat($value);
}

/**
 * Lote: primero del registro de la solicitud, si no, del medicamento relacionado.
 * Tolera distintos namespaces del modelo Input.
 */
function renderLoteSection($id, $inputs_solicitud) {
    foreach ($inputs_solicitud as $inputItem) {
        if ($inputItem->input_id == $id && !empty($inputItem->lote)) {
            return $inputItem->lote;
        }
    }

    // Fallback: intenta cargar el medicamento si la clase Input existe
    $input = null;
    if (class_exists(Input::class)) {
        $input = Input::with('medicine')->find($id);
    } elseif (class_exists(Input::class)) {
        $input = Input::with('medicine')->find($id);
    }

    return $input->medicine->lote ?? '';
}

/**
 * Caducidad: primero del registro de la solicitud, si no, del medicamento relacionado.
 * Devuelve 'Y-m-d' o cadena vacía. Tolera distintos namespaces del modelo Input.
 */
function renderCaducidadSection($id, $inputs_solicitud) {
    foreach ($inputs_solicitud as $inputItem) {
        if ($inputItem->input_id == $id && !empty($inputItem->caducidad)) {
            return Carbon::parse($inputItem->caducidad)->format('Y-m-d');
        }
    }

    // Fallback: intenta cargar el medicamento si la clase Input existe
    $input = null;
    if (class_exists(Input::class)) {
        $input = Input::with('medicine')->find($id);
    } elseif (class_exists(Input::class)) {
        $input = Input::with('medicine')->find($id);
    }

    if (!empty($input?->medicine?->caducidad)) {
        return Carbon::parse($input->medicine->caducidad)->format('Y-m-d');
    }
    return '';
}

/**
 * Guarda/retorna la bolsa EVA seleccionada dentro del ciclo (usa variable global como en tu código).
 */
function renderBolsaEvaInputSection($id, $inputs_solicitud) {
    global $inputBolsaEva;

    foreach ($inputs_solicitud as $inputItem) {
        if ($inputItem->input_id == $id) {
            $inputBolsaEva = $inputItem->input_id;
            return $inputItem->input_id;
        }
    }
    return null;
}

/**
 * Lote de la bolsa EVA seleccionada (global).
 */
function renderLoteBolsaEvaSection($inputs_solicitud) {
    global $inputBolsaEva;
    $inputLote = '';
    foreach ($inputs_solicitud as $inputItem) {
        if ($inputItem->input_id == $inputBolsaEva) {
            $inputLote = $inputItem->lote;
            break;
        }
    }
    return $inputLote;
}

/**
 * Caducidad de la bolsa EVA seleccionada (global).
 */
function renderCaducidadBolsaEvaSection($inputs_solicitud) {
    global $inputBolsaEva;
    $inputCaducidad = '';
    foreach ($inputs_solicitud as $inputItem) {
        if ($inputItem->input_id == $inputBolsaEva) {
            $inputCaducidad = $inputItem->caducidad;
            break;
        }
    }
    return $inputCaducidad;
}
