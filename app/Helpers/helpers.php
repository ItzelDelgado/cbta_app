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


    // Define una función que utiliza el fragmento de código y toma $input como argumento
    function renderLoteSection($id,$inputs_solicitud)
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
        function renderCaducidadSection($id,$inputs_solicitud)
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