<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Remisión</title>


    <style>
        @page {
            margin: 1rem;
        }

        /* Estilos básicos */
        body {
            margin: 0;
            padding: 20px;
            /* Espacio alrededor del contenedor para que el borde no toque los bordes de la ventana del navegador */
            background-color: white;
            /* Fondo blanco para el body */
        }

        .introduccion table {
            width: 100%;
            /* Ajusta esto según necesites */
            border-collapse: collapse;
            border: none;
        }

        .introduccion td {
            border: none;
        }

        /* Contenedor principal con borde negro */
        .contenedor {
            border: 2px solid black;
            padding: 0 2px;
            font-family: "Arial", sans-serif;
        }



        /* Estilos para la tabla */
        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid black;
            text-align: left;
            padding: 8px;
            font-size: 10px
        }

        th {
            background-color: #f2f2f2;
        }

        p {
            font-size: 10px
        }
    </style>

</head>


<body>

    <div class="contenedor">
        <!-- Contenedor principal con borde negro -->
        <div>
            <table class="introduccion">
                <tr>
                    <td>
                        <img style="width: 7rem" src="{{ asset('img/Centro Biotecnologico de Terapias Avanzadas.png') }}"
                            alt="">
                    </td>
                    <td style="text-align: center; padding-right: 171px; font-weight: bold; font-size: 15px">
                        <strong>{{ $solicitud_detalles->user->hospital->name }}</strong>
                    </td>
                    <td>

                    </td>
                </tr>
            </table>
            <table>
                <tr>
                    <td style="border: none; border-top: 1px solid black; font-weight: bold">Fecha de envío:</td>
                    <td style="text-align: right; border: none; border-top: 1px solid black; ">DOMICILIO CLIENTE
                        RECEPTOR:</td>
                </tr>
                <tr>
                    <td style="border: none; font-weight: bold">No. 00018</td>
                    <td style="text-align: right; border: none">{{ $solicitud_detalles->user->hospital->adress }}
                    </td>
                </tr>
            </table>
            <table>
                <tr>
                    <td
                        style="text-align: center; border-top: 1px solid black; border-bottom: none; background-color: #1F4E78; color: white; font-weight: bold;">
                        <strong>DATOS DEL
                            PACIENTE</strong>
                    </td>
                </tr>
            </table>
            <table>
                <tr>
                    <th style="background: #D9E2F3; width: 40%; text-align: center">NOMBRE COMPLETO</th>
                    <th style="background: #D9E2F3; width: 20%; text-align: center">FECHA DE NACIMIENTO</th>
                    <th style="background: #D9E2F3; width: 10%; text-align: center">EDAD(a)</th>
                    <th style="background: #D9E2F3; width: 10%; text-align: center">GENERO</th>
                    <th style="background: #D9E2F3; width: 20%; text-align: center">SUPERFICIE CORPORAL (m2)</th>
                </tr>
                <tr>
                    <td>{{ $solicitud_detalles->solicitud_patient['nombre_paciente'] }}
                        {{ $solicitud_detalles->solicitud_patient['apellidos_paciente'] }}</td>
                    <td>{{ $solicitud_detalles->solicitud_patient['fecha_nacimiento'] }}</td>
                    <td>{{ $solicitud_detalles->solicitud_patient['edad'] }}</td>
                    <td>{{ $solicitud_detalles->solicitud_patient['sexo'] }}</td>
                    <td>S/D</td>
                </tr>
            </table>
            <table>
                <tr>
                    <th style="border-top: none; background: #D9E2F3; width: 40%; text-align: center">DIAGNOSTICO</th>
                    <th style="border-top: none; background: #D9E2F3; width: 20%; text-align: center">SERVICIOS</th>
                    <th style="border-top: none; background: #D9E2F3; width: 20%; text-align: center">No. De EXPREDIENTE
                    </th>
                    <th style="border-top: none; background: #D9E2F3; width: 20%; text-align: center">MÉDICO TRATANTE
                    </th>
                </tr>
                <tr>
                    <td>{{ $solicitud_detalles->solicitud_patient['diagnostico'] }}</td>
                    <td>{{ $solicitud_detalles->solicitud_patient['servicio'] }}</td>
                    <td>{{ $solicitud_detalles->solicitud_patient['registro'] }}</td>
                    <td>{{ $solicitud_detalles->solicitud_detail['nombre_medico'] }}</td>
                </tr>
            </table>
            <table>
                <tr>
                    <td
                        style="border-top: none; width: 50%; text-align: center; border-top: 1px solid black; border-bottom: none; background-color: #1F4E78; color: white; font-weight: bold; padding: 8px 0">
                        <strong>DATOS DE LAS MEZCLAS</strong>
                    </td>
                    <td
                        style="border-top: none; width: 50%; text-align: center; text-align: center; border-top: 1px solid black; border-bottom: none; background-color: #1F4E78; color: white; font-weight: bold; padding: 8px 0">
                        <strong>COSTO MEDICAMENTO</strong>
                    </td>
                </tr>
            </table>
            <table>
                <tr>
                    <th style="border-top: none; background: #D9E2F3; width: 10%; text-align: center">
                        <strong>No</strong>
                    </th>
                    <th style="border-top: none; background: #D9E2F3; width: 20%; text-align: center">
                        <strong>MEDICAMENTO</strong>
                    </th>
                    <th style="border-top: none; background: #D9E2F3; width: 10%; text-align: center">
                        <strong>DOSIS</strong>
                    </th>
                    <th style="border-top: none; background: #D9E2F3; width: 10%; text-align: center"><strong>LOTE DE LA
                            MEZCLA</strong></th>
                    <th style="border-top: none; background: #D9E2F3; width: 12.5%; text-align: center">
                        <strong>PRESENTACIÓN</strong>
                    </th>
                    <th style="border-top: none; background: #D9E2F3; width: 12.5%; text-align: center">
                        <strong>CANTIDAD</strong>
                    </th>
                    <th style="border-top: none; background: #D9E2F3; width: 12.5%; text-align: center"><strong>PRECIO
                            (ml)</strong></th>
                    <th style="border-top: none; background: #D9E2F3; width: 12.5%; text-align: center">
                        <strong>SUBTOTAL</strong>
                    </th>
                </tr>
                @php
                    $total = 0; // Inicializamos la variable total
                @endphp
                @foreach ($inputs_solicitud as $input_completo)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td><strong>
                                @isset($input_completo->input->medicine)
                                    {{ $input_completo->input->medicine->denominacion_generica }}
                                @else
                                    Medicamento no disponible
                                @endisset
                            </strong></td>
                        <td>{{ $input_completo['valor'] }} {{ explode('/', $input_completo->input->unidad)[0] }}</td>
                        <td>N10122304</td>
                        <td>
                            @isset($input_completo->input->medicine)
                                {{ $input_completo->input->medicine->presentacion }} ML
                            @else
                                Medicamento no disponible
                            @endisset
                        </td>

                        @php
                            // Inicializamos la variable total
                            $valor_final = 0;

                            if (
                                $solicitud_detalles->solicitud_detail['sobrellenado_ml'] == null ||
                                $solicitud_detalles->solicitud_detail['sobrellenado_ml'] == 0
                            ) {
                                $valor_final = number_format($input_completo['valor_ml'], 2);
                                
                            } else {
                                $valor_final = number_format($input_completo['valor_sobrellenado'], 2);
                            }
                        @endphp

                        <td>{{ $valor_final }} mL</td>
                        <td> @isset($input_completo->input->medicine)
                                {{ $input_completo->input->medicine->precio_ml }}
                            @else
                                Medicamento no disponible
                            @endisset
                        </td>
                        <td> @isset($input_completo->precio_ml)
                                @php
                                    $total += $input_completo->precio_ml; // Sumamos el precio_ml al total
                                @endphp
                                {{ number_format($input_completo->precio_ml, 2) }}
                            @else
                                Medicamento no disponible
                            @endisset
                        </td>
                    </tr>
                @endforeach
            </table>
            <table>
                <tr>
                    <td style="text-align: right; border-top: none"><strong>Total
                            ${{ number_format($total, 2) }}</strong></td>
                </tr>
            </table>
            <br>
            <table>
                <tr>
                    <td style="border: none;">
                        <hr style="width: 170px;  background-color: black; margin: 0; padding: 0; margin: 0 auto">
                    </td>
                </tr>
                <tr style="margin: 0; padding: 0">
                    <td style="border: none; text-align: center; border-top: none; margin: 0; padding: 0">Nombre
                        completo/firma <br> Fecha de recibido</td>
                </tr>
            </table>
        </div>


</body>

</html>
