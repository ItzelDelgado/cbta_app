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

        .title {
            text-align: center;
            font-weight: bold;
            font-size: 18px;
        }

        .salto-pagina {
            page-break-before: always;
        }

        /* Contenedor principal con borde negro */
        .contenedor {
            padding: 0;
            font-family: "Arial", sans-serif;
        }

        /* Estilos para el texto introductorio */
        .introduccion table {
            width: 100%;
            /* Ajusta esto según necesites */
            border-collapse: collapse;
            border: none;
        }

        /* Estilos para la tabla */
        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 0px solid black;
            padding: 0px;
            font-size: 12px
        }

        .border-1 {
            border: 1px solid black;
        }

        .border-x-1 {
            border-left: 1px solid black;
            border-right: 1px solid black;
        }

        .border-y-1 {
            border-top: 1px solid black;
            border-bottom: 1px solid black;
        }

        .border-l-0 {
            border-left: none;
        }

        .border-r-0 {
            border-right: none;
        }

        .mx-1 {
            margin-left: 0.25rem;
            margin-right: 0.5rem;
        }

        .mt-8 {
            margin-top: 2rem;
        }

        .mt-2 {
            margin-top: 0.5rem;
        }

        .px-1 {
            padding-left: 0.25rem;
            padding-right: 0.25rem;
        }

        .py-1 {
            padding-top: 0.25rem;
            padding-bottom: 0.25rem;
        }

        th {
            background-color: #f2f2f2;
        }

        p {
            font-size: 11px
        }

        .liberacion-area td {
            margin: 0;
            padding: 0 8px;
        }

        .firmas td {

            text-align: center;
        }

        .elementos td {
            margin: 0;
            padding: 0 8px;

        }

        .border-r-0 {
            border-right: none;
        }

        .border-l-0 {
            border-left: none;
        }

        .border-t-0 {
            border-top: none;
        }

        .border-b-0 {
            border-bottom: none;
        }

        .border-l-1 {
            border-left: 1px solid black;
        }

        .border-r-1 {
            border-right: 1px solid black;
        }

        .border-0 {
            border: none;
        }

        .border-1 {
            border: 1px solid black;
        }

        .border-t-1 {
            border-top: 1px solid black;
        }

        .border-b-1 {
            border-bottom: 1px solid black;
        }

        .text-left {
            text-align: left;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .font-bold {
            font-weight: bold;
        }

        .text-center {
            text-align: center
        }
    </style>

</head>

<body>

    <div class="contenedor border-1">
        <!-- Contenedor principal con borde negro -->
        <div class="introduccion">
            <table style="padding-top: 0.5rem">
                <tr>
                    <td style="width: 20%">
                        <img style="width: 10rem;" src="{{ asset('img/logo-cbta.jpg') }}" alt="">
                    </td>
                    <td style="width: 60%; margin: 0 auto; text-align: center; font-size: 13px">
                        <strong>CENTRAL DE MEZCLAS ESTÉRILES PRODIFEM <br> NUTRICIONES PARENTERALES</strong>
                    </td>
                    <td style="width: 20%"></td>
                </tr>
            </table>
        </div>
        <table>
            <tr>
                <td style="text-align: right; color: blue; padding: 2px 8px;">
                    FTO-NPT-023-005
                </td>
            </tr>
            <tr style="background-color: #1F4E78; color: white; font-weight: bold;">
                <td style="text-align: center;">ORDEN DE PREPARACIÓN DE NUTRICIÓN PARENTERAL TOTAL</td>
            </tr>
        </table>
        <table>
            <tr>
                <td class="border-r-1 px-1">Paciente Nombre(s): {{ $solicitud->nombre_paciente }}</td>
                <td class="border-x-1 px-1">Servicio: {{ $solicitud->servicio }}</td>
                <td class="border-l-1 px-1">Registro: {{ $solicitud->registro_paciente }}</td>
            </tr>
        </table>
        <table>
            <tr>
                <td class="border-1 border-l-0 px-1">Sexo: {{ $solicitud->sexo }}</td>
                <td class="border-1 px-1">Fecha de Nacimiento:
                    {{ \Carbon\Carbon::parse($solicitud->fecha_nacimiento)->format('d/m/Y') }}</td>
                <td class="border-1 px-1">Peso: {{ $solicitud->peso }}</td>
                <td class="border-1 px-1">Piso: {{ $solicitud->piso }}</td>
                <td class="border-1 border-r-0 px-1">Cama: {{ $solicitud->cama }}</td>
            </tr>
        </table>
        <table>
            <tr>
                <td class="border-x-1 border-l-0 px-1">Diagnóstico: {{ $solicitud->diagnostico }}</td>
                <td class="border-x-1 border-r-0 px-1">Nombre del Médico: {{ $solicitud->nombre_medico }}</td>
            </tr>
            <tr>
                <td class="border-1 border-l-0 px-1">Cédula del Médico: {{ $solicitud->cedula_medico }}</td>
                <td class="border-1 border-r-0 px-1">Fecha de entrega*:
                    {{ \Carbon\Carbon::parse($solicitud->fecha_entrega)->format('d/m/Y') }}</td>
            </tr>
        </table>
        <table>
            <tr>
                <td class="border-b-1 px-1">Observaciones: {{ $solicitud->observaciones }}</td>
            </tr>
        </table>

        {{-- Mezclas --}}
        @foreach ($solicitud->mezclas as $index => $mezcla)
            <div class="mt-2">
                <table>
                    <tr>
                        <td class="border-t-1 border-b-1 px-1 text-center"
                            style="background: black; color: white; font-weight: bold; font-size: 14px">
                            Mezcla #{{ $index + 1 }}
                        </td>
                    </tr>
                </table>
                <table>
                    <tr>
                        <td class="border-l-0 px-1 font-bold">Medicamento</td>
                        <td class="border-x-1 px-1 font-bold">Dosis</td>
                        <td class="border-r-1 px-1 font-bold">Diluyente</td>
                        <td class="px-1 font-bold">Vía de administración</td>
                    </tr>
                    @foreach ($mezcla->medicamentos as $med)
                        <tr>
                            <td class="border-t-1 border-r-1 px-1">
                                {{ $med->medicamentoOnco->catalog->denominacion ?? '—' }}
                            </td>
                            <td class="border-t-1 border-r-1 px-1">
                                {{ number_format($med->dosis, 2) }}
                            </td>
                            <td class="border-t-1 border-r-1 px-1">
                                {{ $med->diluyente->name ?? '—' }}
                            </td>
                            <td class="border-t-1 px-1">
                                {{ $med->viaAdministracion->name ?? '—' }}
                            </td>
                        </tr>
                    @endforeach
                </table>
                <table>
                    <tr>
                        <td class="border-r-1 border-t-1 px-1">Volumen total de dilución (ml)*:
                            {{ $mezcla->volumen_dilucion }}</td>
                        <td class="border-t-1 px-1">Tiempo de infusión (min)*:
                            {{ $mezcla->tiempo_infusion }}</td>
                    </tr>
                </table>
            </div>
        @endforeach
    </div>

</body>


</html>
