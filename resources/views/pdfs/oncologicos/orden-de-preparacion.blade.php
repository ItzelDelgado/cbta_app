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

        .bg-cbta {
            background-color: #1F4E78;
            color: white;
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
            font-size: 11px
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

        .mx-2{
            margin-left: 0.5rem;
            margin-right: 1rem;
        }

        .mt-8 {
            margin-top: 2rem;
        }

        .mt-12 {
            margin-top: 3rem;
        }

        .mt-2 {
            margin-top: 0.5rem;
        }

        .mt-4 {
            margin-top: 1rem;
        }

        .mb-4 {
            margin-bottom: 1rem;
        }

        .px-1 {
            padding-left: 0.25rem;
            padding-right: 0.25rem;
        }

        .py-1 {
            padding-top: 0.25rem;
            padding-bottom: 0.25rem;
        }

        .pt-2{
            padding-top: 0.5rem;
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


<body>
    <div class="contenedor border-1">
        <!-- Contenedor principal con borde negro -->
        <div class="introduccion">
            <table class="mt-2">
                <tr>
                    <td style="width: 20%">
                        <img style="width: 10rem" src="{{ asset('img/logo-cbta.jpg') }}" alt="">
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
                <td style="text-align: right; color: blue; padding: 2px 8px;">
                    FTO-NPT-023-005
                </td>
            </tr>
            <tr style="background-color: #1F4E78; color: white; font-weight: bold;">
                <td style="text-align: center;">ORDEN DE PREPARACIÓN DE NUTRICIÓN PARENTERAL TOTAL</td>
            </tr>
        </table>
        <table class="mx-1">
            <tr>
                <td></td>
                <td class="text-right px-1"><strong>Fecha de elaboración:</strong></td>
            </tr>

        </table>

        <table class="mx-1">
            <tr>
                <td><strong>No. de Lote de la mezcla:</strong></td>
                <td><strong>No. de orden de preparación:</strong></td>
            </tr>
        </table>
        <table class="mx-1 mt-2">
            <tr>
                <td><strong>DATOS DEL PACIENTE</strong></td>
            </tr>
        </table>
        <div class="border-1 mx-1 px-1 py-1">
            <table>
                <tr>
                    <td style="width: 5%">Nombre: </td>
                    <td style="width: 40%" class="border-b-1">{{ $mezcla->solicitud['nombre_paciente'] }}</td>
                    <td style="width: 11.5%">Médico tratante:</td>
                    <td style="width: 43%" class="border-b-1">{{ $mezcla->solicitud['nombre_medico'] }}</td>
                </tr>
            </table>
            <table>
                <tr>
                    <td style="width: 5%">Registro:</td>
                    <td style="width: 30%" class="border-b-1">{{ $mezcla->solicitud['registro_paciente'] }}</td>
                    <td style="width: 5%">Diagnostico:</td>
                    <td style="" class="border-b-1">{{ $mezcla->solicitud['diagnostico'] }}</td>
                </tr>
            </table>
            <table>
                <tr>
                    <td style="width: 15.4%">Fecha de nacimiento:</td>
                    <td class="border-b-1">{{ $mezcla->solicitud['fecha_nacimiento'] }}</td>
                    <td style="width: 5%">Genero:</td>
                    <td class="border-b-1">{{ $mezcla->solicitud['sexo'] }}</td>
                    <td style="width: 4%">Peso:</td>
                    <td class="border-b-1">{{ $mezcla->solicitud['peso'] }} Kg</td>
                </tr>
            </table>
            <table>
                <tr>
                    <td style="width: 15%">Hospital que solicita:</td>
                    <td class="border-b-1"></td>
                    <td style="width: 10%">Dosis/Volumen:</td>
                    <td class="border-b-1">{{ $mezcla->volumen_dilucion }} ML</td>
                </tr>
            </table>
        </div>
        <p class="mx-1"><strong>MEZCLA ESTERIL ONCOLÓGICA </strong></p>

        <div class="border-1 mx-1 px-1 py-1">
            <table>
                <tr>
                    <td colspan="1"><strong>Hora de preparación:</strong>
                        {{ $fecha_preparacion ? \Carbon\Carbon::parse($fecha_preparacion)->format('H:i') : '—' }}</td>
                    <td colspan="2" class="border-b-1">
                </tr>
            </table>
            <table>
                <tr>
                    <td class="text-center border-x-1 border-t-1"><strong>Medicamentos</strong></td>
                </tr>
            </table>
            <table>
                <tr>
                    <td class="border-1 text-center" style="width: 1%"><strong></strong></td>
                    <td class="border-1 text-center" style="width: 5%"><strong>No. de lote</strong></td>
                    <td class="border-1 text-center" style="width: 6%"><strong>Caducidad</strong></td>
                    <td class="border-1 text-center" style="width: 10%"><strong>Denominación comercial</strong></td>
                    <td class="border-1 text-center" style="width: 10%"><strong>Denominación genérica</strong></td>
                    <td class="border-1 text-center" style="width: 7%"><strong>Presentación</strong></td>
                    <td class="border-1 text-center" style="width: 5%"><strong>Dosis</strong></td>
                    <td class="border-1 text-center" style="width: 5%"><strong>Volumen</strong></td>
                </tr>

                @foreach ($medicamentos as $i => $med)
                    <tr>
                        <td class="border-1 border-t-0 text-center"><strong>{{ $i + 1 }}</strong></td>
                        <td class="border-1 border-t-0 text-center">{{ $med->lote ?? '—' }}</td>
                        <td class="border-1 border-t-0 text-center">
                            {{ $med->caducidad ? \Carbon\Carbon::parse($med->caducidad)->format('d/m/Y') : '—' }}</td>
                        <td class="border-1 border-t-0 text-center">{{ $med->denominacion_comercial ?? '—' }}</td>
                        <td class="border-1 border-t-0 text-center">{{ $med->denominacion ?? '—' }}</td>
                        <td class="border-1 border-t-0 text-center">{{ $med->presentacion ?? '—' }}</td>
                        <td class="border-1 border-t-0 text-center">{{ $med->dosis ?? '—' }}</td>
                        <td class="border-1 border-t-0 text-center">{{ $med->dosis_ml ?? '—' }} mL</td>
                    </tr>
                @endforeach
            </table>
            <table>
                <tr>
                    <td class="text-right border-x-1 border-b-1"><strong>Contenedor y volumen final: {{ $mezcla->volumen_dilucion }}
                            ML</strong></td>
                </tr>
            </table>

            <table class="mt-2">
                <tr>
                    <td class="border-1 text-center"><strong>Solución y volumen</strong></td>
                </tr>
                <tr>
                    <td class="border-1 text-center"><em>Verificar que los lotes y caduciodades coincidan con los
                            medicamentos
                            entregados</em></td>
                </tr>
            </table>
            <table>
                <tr>
                    <td class="border-x-1 border-b-1 text-center"><strong>1</strong></td>
                    <td class="border-x-1 border-b-1 text-center"><strong>No. de lote</strong></td>
                    <td class="border-x-1 border-b-1 text-center"><strong>Caducidad</strong></td>
                    <td class="border-x-1 border-b-1 text-center"><strong>Denominación comercial</strong></td>
                    <td class="border-x-1 border-b-1 text-center"><strong>Deniminación genética</strong></td>
                    <td class="border-x-1 border-b-1 text-center"><strong>Presentación</strong></td>
                    <td class="border-x-1 border-b-1 text-center"><strong>Volumen total</strong></td>
                    <td class="border-x-1 border-b-1 text-center"><strong>Volumen</strong></td>
                </tr>
            </table>

            <table class="mt-2">
                <tr>
                    <td class="border-x-1 border-t-1" colspan="8" style="text-align: center"><strong>Equipo de infusión/Infusor</strong></td>
                </tr>
                <tr>
                    <td rowspan="2" class="border-1 text-center"><strong>1</strong></td>
                    <td colspan="1" class="border-1 text-center"><strong>No. de lote</strong></td>
                    <td colspan="1" class="border-1 text-center"><strong>Caducidad</strong></td>
                    <td colspan="2" class="border-1 text-center"><strong>Nombre comercial</strong></td>
                    <td colspan="3" class="border-1 text-center"><strong>Nombre genérico</strong></td>
                </tr>
                <tr>
                    <td colspan="1" class="border-1 text-center"></td>
                    <td colspan="1" class="border-1 text-center"></td>
                    <td colspan="2" class="border-1 text-center"></td>
                    <td colspan="3" class="border-1 text-center"></td>
                </tr>
            </table>


            <table>
                <tr>
                    <td>Cálculos y forma de preparación:</td>
                </tr>
            </table>
            <table>
                <tr>
                    <td style="">Extraer:</td>
                </tr>
            </table>
            <table>
                <tr>
                    <td style="width: 5%">Agregar:</td>
                </tr>
                <tr>
                    <td>Leyenda de protección</td>
                </tr>
            </table>
        </div>
        <table class="mx-2">
            <tr>
                <td>Recepción y validación:</td>
            </tr>
            <tr>
                <td>Preparación</td>
            </tr>
            <tr>
                <td>Liberación:</td>
            </tr>
        </table>

    </div>




</body>

</html>
