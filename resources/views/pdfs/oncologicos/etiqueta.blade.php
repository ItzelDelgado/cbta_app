<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;700&display=swap" rel="stylesheet">
    <title>Etiqueta</title>

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

        /* Estilos para la tabla */
        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 0px solid black;
            padding: 0px;
            font-size: 9px
        }

        .border-1 {
            border: 1px solid black;
        }

        .border-dotted {
            border: dotted black;
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

        .p-1 {
            padding: 0.25rem;
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

        .fond-bold {
            font-weight: bold;
        }

        .text-center {
            text-align: center
        }
    </style>
</head>


<body>
    <div class="border-1 border-dotted">
        <div>
            <table>
                <tr class="text-center">
                    <td style="font-size: 11px"><strong>CENTRAL DE MEZCLAS ESTÉRILES PRODIFEM</strong></td>
                </tr>
            </table>
            <table>
                <tr>
                    <td class="px-1">Cliente:</td>
                    <td class="px-1">Lote: {{ $mezcla->lote ?? '—' }}</td>
                </tr>
                <tr>
                    <td class="px-1">Paciente: {{ $solicitud->nombre_paciente }}</td>
                    <td class="px-1">F. Nac:
                        {{ $solicitud->fecha_nacimiento ? \Carbon\Carbon::parse($solicitud->fecha_nacimiento)->format('d/m/Y') : '—' }}
                    </td>
                </tr>
                <tr>
                    <td class="px-1">Edad: {{ $solicitud->edad ?? '—' }}</td>
                    <td class="px-1">Género: {{ $solicitud->sexo ?? '—' }}</td>
                </tr>
                <tr>
                    <td class="px-1">Médico: {{ $solicitud->nombre_medico }}</td>
                    <td class="px-1">No. Registro: {{ $solicitud->registro_paciente ?? '—' }}</td>
                </tr>
                <tr>
                    <td colspan="2" class="px-1"
                        style="border-top: 2px dotted black; border-bottom: 2px dotted black;">
                        <strong>Medicamentos:</strong>
                    </td>
                </tr>
                @foreach ($medicamentos as $med)
                    <tr>
                        <td class="px-1 text-left">{{ $med->nombre }}</td>
                        <td class="px-1 text-left">{{ $med->dosis }} mg</td>
                    </tr>
                @endforeach

                <tr>
                    <td class="px-1">{{ $mezcla->volumen_dilucion }}</td>
                    <td class="px-1">ml</td>
                </tr>
            </table>
            <table>
                <tr>
                    <td style="border-top: 2px dotted black; border-bottom: 2px dotted black; border-left: none; border-right: none;"
                        class="px-1">
                        Fecha y hora de preparación:
                        {{ $aprobada ? \Carbon\Carbon::parse($aprobada->fecha_hora_preparacion)->format('d/m/Y H:i') : '—' }}
                    </td>
                </tr>
            </table>
            <table>
                <tr>
                    <td class="px-1">Usese antes de:
                        {{ $aprobada && $aprobada->fecha_hora_limite_uso ? \Carbon\Carbon::parse($aprobada->fecha_hora_limite_uso)->format('d/m/Y') : '—' }}
                    </td>
                    <td class="px-1">Vel. de infusión: {{ $solicitud->velocidad_infusion ?? '—' }}</td>
                </tr>
                <tr>
                    <td class="px-1">a las:
                        {{ $aprobada && $aprobada->fecha_hora_limite_uso ? \Carbon\Carbon::parse($aprobada->fecha_hora_limite_uso)->format('H:i') : '—' }}
                    </td>
                    <td class="px-1">Administrar en: {{ $mezcla->tiempo_infusion }} min IV</td>
                </tr>
            </table>
            <table>
                <tr>
                    <td style="border-top: 2px dotted black;" class="px-1">Leyenda de proyección:
                        {{ $mezcla->leyenda ?? '—' }}</td>
                </tr>
                <tr>
                    <td class="px-1">Preparada por:</td>
                </tr>
            </table>
        </div>
    </div>


</body>

</html>
