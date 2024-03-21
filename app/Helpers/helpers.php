<?php
    // Define una función que utiliza el fragmento de código y toma $input como argumento
    function renderInputSection($id,$inputs_solicitud)
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
