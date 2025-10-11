@php
    use Carbon\Carbon;

    // ===== Datos del paciente / solicitud =====
    $pacienteNombre = $solicitud->nombre_paciente ?? '—';
    $fechaNac = $solicitud->fecha_nacimiento ? Carbon::parse($solicitud->fecha_nacimiento)->format('d/m/Y') : '—';
    $observaciones = $solicitud->observaciones;
    $edad = $solicitud->edad ?? ($solicitud->fecha_nacimiento ? Carbon::parse($solicitud->fecha_nacimiento)->age : '—');
    $sexo = $solicitud->sexo === 'M' ? 'Masculino' : ($solicitud->sexo === 'F' ? 'Femenino' : '—');
    $diagnostico = $solicitud->diagnostico ?? '—';
    $servicio = $solicitud->servicio ?? '—';
    $expediente = $solicitud->registro_paciente ?? '—';
    $medico = $solicitud->nombre_medico ?? '—';

    // ===== Auxiliares de cálculo =====
    $total = 0;
    $contador = 1;

    function money_fmt($v)
    {
        return '$' . number_format((float) $v, 2, '.', ',');
    }

    // Colección de medicamentos en la lista ASIGNADA del usuario (si existe)
    $listaAsig = optional($solicitud->user->assignedMedicineList)->medicines ?? collect();
@endphp

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
        <!-- Encabezado -->
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
                <td style="text-align: center;">ENTREGA DE LAS MEZCLAS ONCOLÓGICAS PREPARADAS EN CMP</td>
            </tr>
        </table>

        <table>
            <tr>
                <td class="px-1">Fecha de envío: <strong>{{ $fechaEmision ?? now()->format('d/m/Y H:i') }}</strong>
                </td>
                <td class="px-1 text-right">DOMICILIO CLIENTE RECEPTOR:</td>
            </tr>
        </table>

        <!-- Datos del paciente -->
        <table>
            <tr>
                <td class="px-1 text-center">DATOS DEL PACIENTE</td>
            </tr>
        </table>
        <table>
            <tr>
                <td class="border-1 border-l-0 px-1 bg-cbta text-center font-bold">Nombre completo</td>
                <td class="border-1 px-1 bg-cbta text-center font-bold">Fecha de nacimiento</td>
                <td class="border-1 px-1 bg-cbta text-center font-bold">Edad</td>
                <td class="border-1 border-r-0 px-1 bg-cbta text-center font-bold">Género</td>
            </tr>
            <tr>
                <td class="border-x-1 border-l-0 px-1 text-center">{{ $pacienteNombre }}</td>
                <td class="border-x-1 px-1 text-center">{{ $fechaNac }}</td>
                <td class="border-x-1 px-1 text-center">{{ $edad }}</td>
                <td class="border-x-1 border-r-0 px-1 text-center">{{ $sexo }}</td>
            </tr>
        </table>

        <table>
            <tr>
                <td class="border-1 border-l-0 px-1 bg-cbta text-center font-bold">Diagnóstico</td>
                <td class="border-1 px-1 bg-cbta text-center font-bold">Servicio</td>
                <td class="border-1 px-1 bg-cbta text-center font-bold">No. de Expediente</td>
                <td class="border-1 border-r-0 px-1 bg-cbta text-center font-bold">Médico tratante</td>
            </tr>
            <tr>
                <td class="border-1 border-l-0 px-1 text-center">{{ $diagnostico }}</td>
                <td class="border-1 px-1 text-center">{{ $servicio }}</td>
                <td class="border-1 px-1 text-center">{{ $expediente }}</td>
                <td class="border-1 border-r-0 px-1 text-center">{{ $medico }}</td>
            </tr>
        </table>

        <!-- Encabezado de tabla de remisión -->
        <table>
            <tr>
                <td class="text-center">DATOS DE LAS MEZCLAS</td>
                <td class="text-center">COSTO MEDICAMENTO</td>
            </tr>
        </table>

        <table>
            <tr>
                <td class="border-1 border-l-0 px-1 text-center bg-cbta font-bold">No</td>
                <td class="border-1 px-1 text-center bg-cbta font-bold">Medicamento</td>
                <td class="border-1 px-1 text-center bg-cbta font-bold">Dosis</td>
                <td class="border-1 px-1 text-center bg-cbta font-bold">Diluyente</td>
                <td class="border-1 px-1 text-center bg-cbta font-bold">Volumen</td>
                <td class="border-1 px-1 text-center bg-cbta font-bold">Lote de la mezcla</td>
                <td class="border-1 px-1 text-center bg-cbta font-bold">No. Remisión</td>
                <td class="border-1 px-1 text-center bg-cbta font-bold">Unidad de medida</td>
                <td class="border-1 px-1 text-center bg-cbta font-bold">Cantidad</td>
                <td class="border-1 px-1 text-center bg-cbta font-bold">Precio unitario</td>
                <td class="border-1 border-r-0 px-1 text-center bg-cbta font-bold">Subtotal</td>
            </tr>

            @foreach ($mezclas as $mezcla)
                @php
                    $loteMezcla = $mezcla->lote ?? '—';
                    $remision = $mezcla->remision ?? '—';
                    // volumen_dilucion está en la mezcla (ml)
                    $volumenMezcla = isset($mezcla->volumen_dilucion) ? $mezcla->volumen_dilucion . ' ml' : '—';
                @endphp

                @forelse($mezcla->medicamentos as $med)
                    @php
                        // Denominación desde el catálogo (si existe) o el nombre capturado
                        $denom =
                            optional(optional($med->medicamentoOnco)->catalog)->denominacion ??
                            ($med->nombre_medicamento ?? '—');

                        // Dosis en mg (ajusta si usas otra unidad)
                        $dosis = $med->dosis ?? 0;

                        // Cantidad por presentación (mg por vial, por ejemplo) para calcular piezas
                        $cantPorPieza =
                            optional(optional($med->medicamentoOnco)->catalog)->cantidad_medicamento ?: null;
                        $piezas = $cantPorPieza && $dosis ? (int) ceil($dosis / (float) $cantPorPieza) : 1;

                        // Diluyente
                        $diluyente = optional($med->diluyente)->name ?? '—';

                        // ===== Precio unitario con prioridad =====
                        // 1) precio_unitario capturado en mezcla_medicamentos
                        // 2) precio de la lista asignada del usuario (pivot)
                        // 3) precio base del onco
                        $itemLista = $listaAsig->firstWhere('id', $med->medicamento_id);
                        $precioLista = optional($itemLista)->pivot->precio ?? null;

                        $precioUnit =
                            $med->precio_unitario ?? ($precioLista ?? (optional($med->medicamentoOnco)->precio ?? 0));

                        // Subtotal y acumulado
                        $subtotal = $piezas * (float) $precioUnit;
                        $total += $subtotal;

                        $unidad = 'pieza';
                    @endphp

                    <tr>
                        <td class="border-1 border-l-0 px-1 text-center">{{ $contador++ }}</td>
                        <td class="border-1 px-1 text-center">{{ $denom }}</td>
                        <td class="border-1 px-1 text-center">
                            {{ is_numeric($dosis) ? rtrim(rtrim(number_format($dosis, 2, '.', ''), '0'), '.') . ' mg' : $dosis }}
                        </td>
                        <td class="border-1 px-1 text-center">{{ $diluyente }}</td>
                        <td class="border-1 px-1 text-center">{{ $volumenMezcla }}</td>
                        <td class="border-1 px-1 text-center">{{ $loteMezcla }}</td>
                        <td class="border-1 px-1 text-center">{{ $remision }}</td>
                        <td class="border-1 px-1 text-center">{{ ucfirst($unidad) }}</td>
                        <td class="border-1 px-1 text-center">{{ $piezas }}</td>
                        <td class="border-1 px-1 text-center">{{ money_fmt($precioUnit) }}</td>
                        <td class="border-1 border-r-0 px-1 text-center">{{ money_fmt($subtotal) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td class="border-1 border-l-0 px-1 text-center" colspan="10">Sin medicamentos en esta mezcla.
                        </td>
                    </tr>
                @endforelse
            @endforeach
        </table>

        <table>
            <tr>
                <td class="text-right">Total {{ money_fmt($total) }}</td>
            </tr>
        </table>
        <table>
            <tr>
                <td class="border-1 px-1" style="width: 10%">Observaciones:</td>
                <td class="border-1 px-1">{{$observaciones}}</td>
            </tr>
        </table>

        <table class="mt-12">
            <tr>
                <td style="width: 35%"></td>
                <td style="width: 30%" class="border-b-1"></td>
                <td style="width: 35%"></td>
            </tr>
        </table>
        <table class="mt-2 mb-4">
            <tr>
                <td style="width: 35%"></td>
                <td style="width: 30%" class="text-center">Nombre completo/firma<br>Fecha de recibido</td>
                <td style="width: 35%"></td>
            </tr>
        </table>

    </div>

</body>

</html>
