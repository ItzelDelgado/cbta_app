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

@php
    $fmtDate = function ($v) {
        if (!$v) return '—';
        try { return \Carbon\Carbon::parse($v)->format('d/m/Y'); }
        catch (\Exception $e) { return '—'; }
    };

    $fmtDateTime = function ($v) {
        if (!$v) return '—';
        try { return \Carbon\Carbon::parse($v)->format('d/m/Y H:i'); }
        catch (\Exception $e) { return '—'; }
    };

    // Edad
    $edadTexto = '—';
    if (!empty($solicitud->fecha_nacimiento)) {
        $fnac = \Carbon\Carbon::parse($solicitud->fecha_nacimiento);
        $hoy = \Carbon\Carbon::now();
        $diff = $fnac->diff($hoy);
        if ($diff->y > 0) $edadTexto = $diff->y . ' años';
        elseif ($diff->m > 0) $edadTexto = $diff->m . ' meses';
        else $edadTexto = $diff->d . ' días';
    }

    // Preparación (fecha/hora)
    $prep = $fechaPreparacion
        ? $fechaPreparacion
        : (!empty($aprobada?->fecha_hora_preparacion)
            ? \Carbon\Carbon::parse($aprobada->fecha_hora_preparacion)
            : null);

    // Límite
    $limite = $fechaLimiteUso;

    // Leyenda y condiciones
    $legend = $legendEtiqueta ?? null;
    $tmin = $tempMinEtiqueta ?? null;
    $tmax = $tempMaxEtiqueta ?? null;
    $stab = $stabilityEtiqueta ?? null;

    // Observaciones (puede venir null)
    $obs = $observaciones ?? null;
@endphp


<body>
    <div class="border-1">
        {{-- Título --}}
        <table>
            <tr>
                <td class="text-center" style="font-size: 11px; padding: 6px 4px;">
                    <strong>ETIQUETA <br> MEZCLAS ESTÉRILES ONCOLÓGICAS</strong>
                </td>
            </tr>
        </table>

        {{-- Datos generales --}}
        <table>
            <tr>
                <td class="px-1">Cliente:</td>
                <td class="px-1 text-right">Lote mezcla: {{ $mezcla->lote ?? '—' }}</td>
            </tr>
            <tr>
                <td class="px-1">Paciente: {{ $solicitud->nombre_paciente ?? '—' }}</td>
                <td class="px-1 text-right">
                    F. Nac: {{ !empty($solicitud->fecha_nacimiento) ? \Carbon\Carbon::parse($solicitud->fecha_nacimiento)->format('d/m/Y') : '—' }}
                </td>
            </tr>
            <tr>
                <td class="px-1">Edad: {{ $edadTexto }}</td>
                <td class="px-1 text-right">Género: {{ $solicitud->sexo ?? '—' }}</td>
            </tr>
            <tr>
                <td class="px-1">Alergias: {{ $solicitud->alergias ?? '—' }}</td>
                <td class="px-1 text-right"></td>
            </tr>
            <tr>
                <td class="px-1">Médico: {{ $solicitud->nombre_medico ?? '—' }}</td>
                <td class="px-1 text-right">No. Registro: {{ $solicitud->registro_paciente ?? '—' }}</td>
            </tr>
        </table>

        {{-- Medicamentos --}}
        <table>
            <tr><td colspan="2" class="px-1 sep rowline"><strong>Medicamentos:</strong></td></tr>

            @forelse ($medicamentos as $med)
                <tr>
                    <td class="px-1 text-left">{{ $med->nombre ?? '—' }}</td>
                    <td class="px-1 text-right">{{ $med->dosis ?? 0 }} mg</td>
                </tr>
                <tr>
                    <td class="px-1 text-left xs" colspan="2">
                        Lote: {{ $med->lote ?? '—' }} |
                        Cad: {{ !empty($med->cad) ? $fmtDate($med->cad) : '—' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td class="px-1" colspan="2">—</td>
                </tr>
            @endforelse

            {{-- Diluyente + lote/cad --}}
            <tr><td colspan="2" class="px-1 sep rowline"><strong>Diluyente:</strong> {{ $diluyenteTexto ?? '—' }}</td></tr>
            <tr>
                <td class="px-1 xs" colspan="2">
                    Lote: {{ $diluyenteLote ?? '—' }} |
                    Cad: {{ !empty($diluyenteCad) ? $fmtDate($diluyenteCad) : '—' }}
                </td>
            </tr>
        </table>

        {{-- Fechas --}}
        <table>
            <tr>
                <td class="px-1 sep rowline" colspan="2">
                    Fecha y hora de preparación: {{ $prep ? $prep->format('d/m/Y H:i') : '—' }}
                </td>
            </tr>
        </table>

        <table>
            <tr>
                <td class="px-1">
                    Úsese antes de: {{ $limite ? $limite->format('d/m/Y H:i') : '—' }}
                </td>
                <td class="px-1 text-right">
                    Vel. infusión:
                    {{
                        ($mezcla->tiempo_infusion ?? 0) > 0
                        ? number_format((float) $mezcla->volumen_dilucion / (float) $mezcla->tiempo_infusion, 3, '.', '')
                        : '—'
                    }}
                </td>
            </tr>
            <tr>
                <td class="px-1">
                    A las: {{ $limite ? $limite->format('H:i') : '—' }}
                </td>
                <td class="px-1 text-right">
                    Administrar en: {{ $mezcla->tiempo_infusion ?? '—' }} min
                </td>
            </tr>
        </table>

        {{-- Observaciones --}}
        <table>
            <tr>
                <td class="px-1 sep rowline" colspan="2"><strong>Observaciones:</strong></td>
            </tr>
            <tr>
                <td class="px-1" colspan="2">{{ !empty($obs) ? $obs : '—' }}</td>
            </tr>
        </table>

        {{-- Leyenda + condiciones --}}
        <table>
            <tr>
                <td class="px-1 sep-top rowline" colspan="2">Leyenda de protección:</td>
            </tr>
            <tr>
                <td class="px-1" colspan="2">{{ $legend ?: '—' }}</td>
            </tr>
            <tr>
                <td class="px-1" colspan="2">
                    <strong>Condiciones:</strong>
                    @php
                        $cond = [];
                        if ($tmin !== null || $tmax !== null) {
                            $cond[] = 'Temp. ' . ($tmin !== null ? $tmin : '—') . '–' . ($tmax !== null ? $tmax : '—') . ' °C';
                        }
                        if ($stab) $cond[] = 'Estabilidad ' . $stab . ' h';
                    @endphp
                    {{ count($cond) ? implode(' | ', $cond) : '—' }}
                </td>
            </tr>
            <tr>
                <td class="px-1" colspan="2">Preparada por:</td>
            </tr>
        </table>
    </div>
</body>



</html>
