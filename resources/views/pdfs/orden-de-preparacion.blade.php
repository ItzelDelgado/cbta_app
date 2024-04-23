<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Orden de preparación</title>

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

        /* Contenedor principal con borde negro */
        .contenedor {
            border: 2px solid black;
            padding: 0 2px;
            font-family: "Arial", sans-serif;
        }

        /* Estilos para el texto introductorio */
        .introduccion table {
            width: 100%;
            /* Ajusta esto según necesites */
            border-collapse: collapse;
            border: none;
        }

        .introduccion td {
            border: none;
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

        .liberacion-area td {
            margin: 0;
            padding: 0 8px;
        }

        .firmas td {
            border: none;
            text-align: center;
            border-top: 1px solid black;
        }

        .elementos td {
            margin: 0;
            padding: 0 8px;

        }
    </style>

</head>


<body>

    <div class="contenedor">
        <!-- Contenedor principal con borde negro -->
        <div class="introduccion">
            <table>
                <tr>
                    <td style="width: 20%">
                        <img style="width: 7rem" src="{{ asset('img/logo-cbta.png') }}" alt="">
                    </td>
                    <td style="width: 60%; margin: 0 auto; text-align: center; font-size: 13px">
                        <strong>CENTRAL DE MEZCLAS ESTÉRILES PRODIFEM <br> NUTRICIONES PARENTERALES</strong>
                    </td>
                    <td style="width: 20%">

                    </td>
                </tr>
            </table>
        </div>
        <table>
            <tr>
                <td style="border-top: 2px solid black; text-align: right; color: blue;">
                    FTO-NPT-023-005
                </td>
            </tr>
            <tr style="background-color: #1F4E78; color: white; font-weight: bold;">
                <td style="text-align: center;">ORDEN DE PREPARACIÓN DE NUTRICIÓN PARENTERAL TOTAL</td>
            </tr>
        </table>

        <table>
            <tr>
                <td style="border: none; text-align: right"><strong>Fecha de elaboración:</strong>
                    <span>{{ date('d-m-Y', strtotime($solicitud_detalles->solicitud_aprobada['created_at'])) }}</span>
                </td>
            </tr>
            <tr>
                <td style="border: none;"><strong>LIBERACIÓN DE ÁREA</strong></td>
            </tr>
        </table>
        <table class="liberacion-area" style="margin-bottom: 0.3rem;">
            <tr>
                <td style="border: none;"></td>
                <td>Si</td>
                <td>No</td>
            </tr>
            <tr>
                <td style="border: none">El personal encargado de la preparación porta el uniforme correspondiente a la
                    actividad a realizar?</td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td style="border: none;">El área de preparación y campana de seguridad biólogica se encuentran limpios?
                </td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td style="border: none">¿La zona de paso de material y de trabajo de la CSB se encuentra libre de
                    material?</td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td style="border: none">¿El área de preparación se encuentra libre de material y documentos ajenos a la
                    mezcla en turno a preparar?</td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td style="border: none"><strong>¿Se considera liberada el área para la preparación de la
                        mezcla?</strong></td>
                <td></td>
                <td></td>
            </tr>
        </table>
        <table style="width: 350px; margin-bottom: 0.5rem;">
            <tr>
                <td>Sanitizante:</td>
                <td style="width: 20px;"></td>
                <td>Alcohol Isopropilico</td>
                <td style="width: 20px;"></td>
                <td>Critical 0.2%</td>
            </tr>
        </table>
        <div style=" border: 1px solid black; margin-bottom: 0.5rem;">
            <table>
                <tr style="margin: 0; padding: 0">
                    <td style="border: none;"><strong>No. de lote de la mezcla:</strong> <span>{{$solicitud_detalles->solicitud_aprobada['lote']}}</span></td>
                    <td style="border: none;"><strong>No. de orden de preparación:</strong> NUT{{substr($solicitud_detalles->solicitud_aprobada['lote'],1)}}</td>
                </tr>
            </table>
            <p style="padding: 2px 8px; border-top: 1px solid black; border-bottom: 1px solid black;"><strong>DATOS DEL
                    PACIENTE</strong></p>
            <table>
                <tr>
                    <td style="border: none; padding: 0 auto;"><strong>Nombre:</strong>
                        <span>{{ $solicitud_detalles->solicitud_patient['nombre_paciente'] }}
                            {{ $solicitud_detalles->solicitud_patient['apellidos_paciente'] }}</span>
                    </td>
                    <td style="border: none; padding: 0 auto;"><strong>Médico tratante:
                        </strong>{{ $solicitud_detalles->solicitud_patient['nombre_medico'] }}<span>Dr. Alfonso Fajardo
                            R.</span></td>
                </tr>
                <tr>
                    <td style="border: none; padding: 0 auto;"><strong>Registro:</strong> <span
                            style="">10053619</span></td>
                    <td style="border: none; padding: 0 auto;"><strong>Diagnostico:</strong> <span style="">
                            {{ $solicitud_detalles->solicitud_patient['diagnostico'] }}</span></td>
                </tr>
            </table>
            <table style="width: 100%; border-collapse: collapse; padding-bottom: 14px">
                <tr>
                    <td style="border: none; padding: 0 auto;"><strong>Fecha de nacimiento:</strong> <span
                            style="">{{  date('d-m-Y', strtotime($solicitud_detalles->solicitud_patient['fecha_nacimiento'])) }}</span>
                    </td>
                    <td style="border: none; padding: 0 auto;"><strong>Genero:</strong>
                        {{ $solicitud_detalles->solicitud_patient['sexo'] }}<span style=""></span></td>
                    <td style="border: none; padding: 0 auto;"><strong>Talla:</strong> <span style="">S/D</span>
                    </td>
                    <td style="border: none; padding: 0 auto;"><strong>Peso:</strong> <span style="">
                            {{ $solicitud_detalles->solicitud_patient['peso'] }}</span></td>
                    <td style="border: none; padding: 0 auto;"><strong>SC:</strong> <span style="">S/D</span></td>
                </tr>
                <tr>
                    <td style="border: none; padding: 0 auto;" colspan="5"><strong>Sitio de procedencia:</strong>
                        <span style="">S/D</span>
                    </td>

                </tr>
            </table>
        </div>
        <p style="padding: 8px; border: 1px solid black; margin: 0;"><strong>NUTRICIÓN PARENTERAL TOTAL</strong></p>
        <table class="elementos">
            <thead>
                <tr>
                    <th style="background: #D9E2F3;">No</th>
                    <th style="background: #D9E2F3;">Volumen(mL)</th>
                    <th style="background: #D9E2F3;">Dosis</th>
                    <th style="background: #D9E2F3;">Denominaci&oacute;n g&eacute;nerica</th>
                    <th style="background: #D9E2F3;">Denominaci&oacute;n comercial</th>
                    <th style="background: #D9E2F3;">Presentaci&oacute;n</th>
                    <th style="background: #D9E2F3;">Lote</th>
                    <th style="background: #D9E2F3;">Caducidad</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($inputs_solicitud as $input_completo)
                    @if ($input_completo->input_id != 40)
                        <tr>
                            <td style="text-align: center">{{ $loop->iteration }}</td>
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
                            <td style="text-align: center">{{ $input_completo['valor'] }}
                                {{ explode('/', $input_completo->input->unidad)[0] }}</td>
                            <td>
                                @isset($input_completo->input->medicine)
                                    {{ $input_completo->input->medicine->denominacion_generica }}
                                @else
                                    Medicamento no disponible
                                @endisset
                            </td>
                            <td>
                                @isset($input_completo->input->medicine)
                                    {{ $input_completo->input->medicine->denominacion_comercial }}
                                @else
                                    Medicamento no disponible
                                @endisset
                            </td>
                            <td style="text-align: center">
                                @isset($input_completo->input->medicine)
                                    {{ $input_completo->input->medicine->presentacion_ml }} ML
                                @else
                                    Medicamento no disponible
                                @endisset
                            </td>
                            <td>{{ $input_completo['lote'] }}</td>
                            <td>{{  date('d-m-Y', strtotime($input_completo['caducidad'])) }}</td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
        <table>
            <tr>
                <td style="border-top: none">Volumen Total <span
                        style="font-weight: bold;">{{ number_format($solicitud_detalles->solicitud_detail['volumen_total_final'], 2) }}
                        ml</span></td>
                <td style="border-top: none">Contenedor <span
                        style="font-weight: bold;">{{ $bolsa_eva->input->medicine->presentacion_ml }} mL</span></td>
            </tr>
        </table>
        <p style="padding: 8px; text-align: center; margin: 0;"><strong>Bolsa EVA</strong></p>
        <table>
            <tr>
                <td style="background: #D9E2F3;"></td>
                <td style="background: #D9E2F3;"><strong>No. de lote</strong></td>
                <td style="background: #D9E2F3;"><strong>Caducidad</strong></td>
                <td style="background: #D9E2F3;"><strong>Nombre comercial</strong></td>
                <td style="background: #D9E2F3;"><strong>Nombre genérico</strong></td>
            </tr>
            <tr>
                <td>1</td>
                <td>{{ $bolsa_eva->lote }}</td>
                <td>{{  date('d-m-Y', strtotime($bolsa_eva->caducidad)) }}</td>
                <td>{{ $bolsa_eva->input->medicine->denominacion_comercial }}</td>
                <td>{{ $bolsa_eva->input->medicine->denominacion_generica }}</td>
            </tr>
        </table>
        <p style="padding: 8px; text-align: center; margin: 0;"><strong>Set de infusión</strong></p>
        <table>
            <tr>
                <td style="background: #D9E2F3;"></td>
                <td style="background: #D9E2F3;"><strong>No. de lote</strong></td>
                <td style="background: #D9E2F3;"><strong>Caducidad</strong></td>
                <td style="background: #D9E2F3;"><strong>Nombre comercial</strong></td>
                <td style="background: #D9E2F3;"><strong>Nombre genérico</strong></td>
            </tr>
            <tr>
                <td>1</td>
                <td>
                    @isset($set_infusion)
                        {{ $set_infusion->lote }}
                    @else
                        N/A
                    @endisset
                </td>
                <td>
                    @isset($set_infusion)
                        {{  date('d-m-Y', strtotime($set_infusion->caducidad)) }}
                    @else
                        N/A
                    @endisset
                </td>
                <td>
                    @isset($set_infusion)
                        {{ $set_infusion->input->medicine->denominacion_comercial }}
                    @else
                        N/A
                    @endisset
                </td>
                <td>
                    @isset($set_infusion)
                        {{ $set_infusion->input->medicine->denominacion_generica }}
                    @else
                        N/A
                    @endisset
                </td>
            </tr>
        </table>
        <br>
        <table class="firmas">
            <tr>
                <td>Elaboró <br> Nombre y firma</td>
                <td>Validó <br> Nombre y firma</td>
                <td>Preparó <br> Nombre y firma</td>
            </tr>
        </table>
    </div>


</body>

</html>
