<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Regristro de Envio</title>


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
            padding: 2px 3px;
            font-size: 9px
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
                    <td style="width: 25%">
                        <img style="width: 7rem; margin: 1rem 0"
                            src="{{ asset('img/Centro Biotecnologico de Terapias Avanzadas.png') }}" alt="">
                    </td>
                    <td style="width: 50%; margin: 0 auto; text-align: center; font-weight: bold; font-size: 15px">
                        <strong>{{ $solicitud_detalles->user->hospital->name }}</strong>
                    </td>
                    <td style="width: 25%">
                    </td>
                </tr>
            </table>
            <table>
                <tr>
                    <td style="border: none; border-top: 1px solid black; font-weight: bold">Fecha de envío:
                        <span>{{ date('d-m-Y', strtotime($solicitud_detalles->solicitud_detail['fecha_hora_entrega'])) }}</span>
                    </td>
                    <td style="text-align: right; border: none; border-top: 1px solid black; ">DOMICILIO CLIENTE
                        RECEPTOR:</td>
                </tr>
                <tr>
                    <td style="border: none; font-weight: bold">No. {{ str_pad($solicitud_detalles->solicitud_aprobada['id'], 6, '0', STR_PAD_LEFT) }}</td>
                    <td style="text-align: right; border: none">{{ $solicitud_detalles->user->hospital->adress }}
                    </td>
                </tr>
            </table>
            <table style="tabla-format">
                <tr>
                    <td
                        style="text-align: center; border-top: 1px solid black; border-bottom: none; background-color: #1F4E78; color: white; font-weight: bold; padding: 8px 0">
                        <strong>DATOS DEL
                            PACIENTE</strong>
                    </td>
                </tr>
            </table>
            <table style="tabla-format">
                <tr>
                    <th style="background: #D9E2F3; width: 40%; padding: 8px 0; text-align: center">NOMBRE COMPLETO</th>
                    <th style="background: #D9E2F3; width: 20%; text-align: center">FECHA DE NACIMIENTO</th>
                    <th style="background: #D9E2F3; width: 10%; text-align: center">EDAD(a)</th>
                    <th style="background: #D9E2F3; width: 10%; text-align: center">GENERO</th>
                    <th style="background: #D9E2F3; width: 20%; text-align: center">SUPERFICIE CORPORAL (m2)</th>
                </tr>

                <tr>
                    <td>{{ $solicitud_detalles->solicitud_patient['nombre_paciente'] }}
                        {{ $solicitud_detalles->solicitud_patient['apellidos_paciente'] }}</td>
                    <td style="text-align: center">{{  date('d-m-Y', strtotime($solicitud_detalles->solicitud_patient['fecha_nacimiento'])) }}</td>
                    <td style="text-align: center">{{ $solicitud_detalles->solicitud_patient['edad'] }}</td>
                    <td style="text-align: center">{{ $solicitud_detalles->solicitud_patient['sexo'] }}</td>
                    <td style="text-align: center">S/D</td>
                </tr>
            </table>
            <table style="tabla-format">
                <tr>
                    <th style="background: #D9E2F3; border-top: none; width: 40%; padding: 8px 0; text-align: center">
                        DIAGNOSTICO</th>
                    <th style="background: #D9E2F3; border-top: none; width: 20%; text-align: center">SERVICIOS</th>
                    <th style="background: #D9E2F3; border-top: none; width: 20%; text-align: center">No. De EXPREDIENTE
                    </th>
                    <th style="background: #D9E2F3; border-top: none; width: 20%; text-align: center">MÉDICO TRATANTE
                    </th>
                </tr>
                <tr>
                    <td>{{ $solicitud_detalles->solicitud_patient['diagnostico'] }}</td>
                    <td style="text-align: center">{{ $solicitud_detalles->solicitud_patient['servicio'] }}</td>
                    <td style="text-align: center"></td>
                    <td style="text-align: center">{{ $solicitud_detalles->solicitud_detail['nombre_medico'] }}</td>
                </tr>
            </table>
            <table style="tabla-format">
                <tr>
                    <td
                        style="text-align: center; border-top: 1px solid black; border-bottom: none; background-color: #1F4E78; color: white; font-weight: bold; padding: 8px 0">
                        <strong>DATOS DE LAS MEZCLAS</strong>
                    </td>
                </tr>
            </table>
            <table style="tabla-format">
                <tr>
                    <th style="background: #D9E2F3; border-top: none; width: 5%; text-align: center">
                        <strong>No</strong>
                    </th>
                    <th style="background: #D9E2F3; border-top: none; width: 25%; text-align: center">
                        <strong>MEDICAMENTO</strong>
                    </th>
                    <th style="background: #D9E2F3; border-top: none; width: 10%; text-align: center">
                        <strong>DOSIS</strong>
                    </th>
                    <th style="background: #D9E2F3; border-top: none; width: 10%; text-align: center">
                        <strong>VOLUMEN</strong>
                    </th>
                    <th style="background: #D9E2F3; border-top: none; width: 10%; text-align: center"><strong>LOTE DE LA
                            MEZCLA</strong></th>
                    <th style="background: #D9E2F3; border-top: none; width: 20%; text-align: center"><strong>FECHA\HORA
                            DE PREPARACIÓN</strong></th>
                    <th style="background: #D9E2F3; border-top: none; width: 20%; text-align: center"><strong>FECHA\HORA
                            DE LIMITE DE USO</strong></th>
                </tr>
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
                @foreach ($inputs_solicitud as $input_completo)
                    <tr>
                        <td style="text-align: center">{{ $loop->iteration }}</td>
                        <td style="text-align: center">
                            <strong>
                                @isset($input_completo->input->medicine)
                                    {{ $input_completo->input->medicine->denominacion_generica }}
                                @else
                                    Medicamento no disponible
                                @endisset
                            </strong>
                        </td>
                        <td style="text-align: center"><strong>{{ $input_completo['valor'] }} {{ explode('/', $input_completo->input->unidad)[0] }} </strong></td>
                        <td style="text-align: center">{{ number_format($vol_total, 2) }} mL</td>
                        <td style="text-align: center">{{ $solicitud_detalles->solicitud_aprobada['lote'] }}</td>
                        <td style="text-align: center">{{ date('d-m-Y H:i', strtotime($solicitud_detalles->solicitud_aprobada['fecha_hora_preparacion'])) }}h</td>
                        <td style="text-align: center">{{ date('d-m-Y H:i', strtotime($solicitud_detalles->solicitud_aprobada['fecha_hora_limite_uso'])) }}h</td>
                    </tr>
                @endforeach
            </table>
            <table>
                <tr>
                    <td style="text-align: right; border-top: none"><strong>Cantidad total de mezclas 1</strong></td>
                </tr>
            </table>
            <br>
            <table style="margin: 0 5rem">
                <tr>
                    <td style="border: none;">Recepción Cliente</td>
                </tr>
                <tr>
                    <td style="border: none;">
                        Fecha: <span style="border-bottom: 1px solid black; text-align: center"></span>
                    </td>
                </tr>
                <tr>
                    <td style="border: none;">Hora de recibido:</td>
                </tr>
                <tr>
                    <td style="border: none;">Temperatura:</td>
                </tr>
                <tr>
                    <td style="border: none;">Nombre completo/firma y sello:</td>
                </tr>
            </table>
        </div>
</body>

</html>
