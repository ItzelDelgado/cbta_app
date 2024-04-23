<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Etiqueta</title>


    <style>
        @page {
            margin: 0px;
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
            padding: 2px 3px;
            font-size: 7px
        }

        th {
            background-color: #DEEAF6;
        }

        p {
            font-size: 10px
        }

        .tabla-format table {
            table-layout: fixed;
            border: 1px solid black;
        }

        .tabla-format td {
            border: 1px solid black;
        }

        .tabla-format td {
            word-wrap: break-word;
            /* Asegura que el texto se divida */
            /* max-width: 120px; */
            /* O el ancho específico que prefieras */
            overflow: auto;
            /* Hace desplazable el contenido que exceda el tamaño del td */
        }
    </style>

</head>


<body>

    <div class="contenedor">
        <!-- Contenedor principal con borde negro -->
        <div>
            <table class="introduccion">
                <tr>
                    <td style="text-align: center; padding-right: 2rem">
                        <strong>CENTRO DE MEZCLAS ESTÉRILES PRODIFEM</strong>
                    </td>
                </tr>
                <tr>
                    <td>{{ $solicitud_detalles->user->hospital->name }}</td>
                    <td>{{ $solicitud_detalles->solicitud_aprobada['lote'] }}</td>
                </tr>
                <tr>
                    <td>Paciente: {{ $solicitud_detalles->solicitud_patient['nombre_paciente'] }}
                        {{ $solicitud_detalles->solicitud_patient['apellidos_paciente'] }}</td>
                    <td>FN: {{  date('d-m-Y', strtotime($solicitud_detalles->solicitud_patient['fecha_nacimiento'])) }}</td>
                </tr>
                <tr>
                    <td>Médico: {{ $solicitud_detalles->solicitud_detail['nombre_medico'] }}</td>
                </tr>
                <tr>
                    <td>NUTRICIÓN PARENTERAL</td>
                </tr>
            </table>
            <table>
                <tr>
                    <td>COMPONENTES</td>
                    <td>CONTENIDO</td>
                </tr>
                @php
                    $osmolaridad_total = 0; // Inicializamos la variable total
                @endphp
                @foreach ($inputs_solicitud as $input_completo)
                    <tr>
                        <td style="border:none">
                            @isset($input_completo->input->medicine)
                                {{ $input_completo->input->medicine->denominacion_generica }}
                               
                                @php
                                    $osmolaridad_total += $input_completo->input->medicine->osmolaridad; // Sumamos el precio_ml al total
                                @endphp
                            @else
                                Medicamento no disponible
                                @php
                                    $osmolaridad_total += 0; // Sumamos el precio_ml al total
                                @endphp
                            @endisset
                        </td>
                        <td style="border:none">{{ $input_completo['valor'] }}
                            {{ explode('/', $input_completo->input->unidad)[0] }}</td>
                    </tr>
                @endforeach

            </table>
            <table>
                <tr>
                    <td>Osmolaridad: {{ number_format($osmolaridad_total, 2) }} mOSM/mL</td>
                    <td>Vol. tot: @if (
                        $solicitud_detalles->solicitud_detail['volumen_total'] == null ||
                            $solicitud_detalles->solicitud_detail['volumen_total'] == 0)
                            {{ number_format($solicitud_detalles->solicitud_detail['suma_volumen'], 2) }}
                        @else
                            {{ number_format($solicitud_detalles->solicitud_detail['volumen_total'], 2) }}
                        @endif mL
                    </td>
                </tr>
                <tr>
                    <td>Sobrellenado: @isset($solicitud_detalles->solicitud_detail['sobrellenado_ml'])
                            {{ $solicitud_detalles->solicitud_detail['sobrellenado_ml'] }}
                        @else
                            0
                        @endisset mL</td>
                </tr>
                <tr>
                    @php
                     // Inicializamos la variable total
                    $vol_total = 0;
                
                        if (
                            $solicitud_detalles->solicitud_detail['volumen_total'] == null ||
                            $solicitud_detalles->solicitud_detail['volumen_total'] == 0
                        ) {
                            $vol_total = floatval($solicitud_detalles->solicitud_detail['suma_volumen']);
                        } else {
                            $vol_total = floatval($solicitud_detalles->solicitud_detail['volumen_total']);
                        }
                    @endphp
                    <td>Administrar en: {{ $solicitud_detalles->solicitud_detail['tiempo_infusion_min'] }} h
                    </td>
                    <td>Vel. Infusión:
                        {{ number_format($vol_total / ($solicitud_detalles->solicitud_detail['tiempo_infusion_min'] * 60), 2) }}
                        ml/hr</td>
                </tr>
            </table>
            <table>
                <tr>
                    <td>MANTENER EN REFIGERACIÓN
                        HASTA UNA HORA ANTES DE SU ADMINISTRACIÓN</td>
                </tr>
                <tr>
                    <td>Fecha y hora de preparación: {{ date('d-m-Y H:i', strtotime($solicitud_detalles->solicitud_aprobada['fecha_hora_preparacion'])) }}h
                    </td>
                </tr>
                <tr>
                    <td>
                        Fecha y hora límite de uso: {{ date('d-m-Y H:i', strtotime($solicitud_detalles->solicitud_aprobada['fecha_hora_limite_uso'])) }}h
                    </td>
                </tr>
            </table>
        </div>
</body>

</html>
