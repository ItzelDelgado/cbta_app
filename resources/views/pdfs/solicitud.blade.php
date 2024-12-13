@php
    $variables = [];
    foreach ($arreglo_resultado as $input_completo) {
        $variables['variable' . $input_completo['input_id']] = $input_completo['valor'];
    }
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Solicitud</title>
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
            font-size: 13px;
        }

        /* Contenedor principal con borde negro */
        .contenedor {
            border: 2px solid black;
            padding: 0 1px;
            font-family: "Arial", sans-serif;
        }

        /* Estilos para el texto introductorio */
        .introduccion table {
            width: 100%;
            /* Ajusta esto según necesites */
            border-collapse: collapse;
            border: none;
        }

        .linea-input {
            border-top-width: 0px;
            border-right-width: 0px;
            border-left-width: 0px;
            border-bottom-width: 2px;
            border-style: solid;
        }

        .border-top {
            border-right: 1px solid #000;
            border-left: 1px solid #000;
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
        }

        .border-top td {
            padding-left: 4px;
        }

        .border-bottom {
            border-right: 1px solid #000;
            border-left: 1px solid #000;
            border-top: none;
            border-bottom: 1px solid #000;
        }

        .border-bottom td {
            padding-left: 4px;
        }

        .bold {
            font-weight: bold;
        }

        .m-0 {
            margin: 0;
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
                        <img style="width: 7rem; padding: 5px" src="{{ asset('img/logo-cbta.png') }}" alt="">
                    </td>
                    <td style="width: 60%; margin: 0 auto; text-align: center; font-size: 13px">
                        <strong>CENTRAL DE MEZCLAS ESTÉRILES PRODIFEM <br> NUTRICIONES PARENTERALES</strong>
                    </td>
                    <td style="width: 20%">

                    </td>
                </tr>
            </table>
            <table>
                <tr>
                    <td style="border-top: 2px solid black; text-align: left; color: blue; width: 50%;">
                        No. {{ $solicitud_detalles->id }}
                    </td>
                    <td style="border-top: 2px solid black; text-align: right; color: blue; width: 50%;">
                        FTO-NPT-023-005
                    </td>
                </tr>
            </table>
            <table>

                <tr style="background-color: #1F4E78; color: white; font-weight: bold;">
                    <td style="text-align: center; ">SOLICITUD DE NUTRICIÓN PARENTERAL</td>
                </tr>
            </table>
            <table>
                <tr class="border-top">
                    <td style="width: 50%; border-right: 1px solid #000;">
                        Cliente/hospital:<span>{{ $solicitud_detalles->user->hospital->name }}</span></td>
                    <td style="width: 50%">Fecha:<span>{{ $solicitud_detalles->created_at }}</span></td>
                </tr>
            </table>
            <table>
                <tr class="border-bottom">
                    <td style="width: 40%; border-right: 1px solid #000;">Paciente:
                        {{ $solicitud_detalles->solicitud_patient['nombre_paciente'] }}
                        {{ $solicitud_detalles->solicitud_patient['apellidos_paciente'] }}</td>
                    <td style="width: 20%; border-right: 1px solid #000;">servicio:
                        {{ $solicitud_detalles->solicitud_patient['servicio'] }}</td>
                    <td style="width: 20%; border-right: 1px solid #000;">Cama:
                        {{ $solicitud_detalles->solicitud_patient['cama'] }}</td>
                    <td style="width: 20%">Piso: {{ $solicitud_detalles->solicitud_patient['piso'] }}</td>
                </tr>
            </table>
            <table>
                <tr class="border-bottom">
                    <td style="width: 50%; border-right: 1px solid #000;">Registro:
                        {{ $solicitud_detalles->solicitud_patient['registro'] }}</td>
                    <td style="width: 50%">Diagnóstico: {{ $solicitud_detalles->solicitud_patient['diagnostico'] }}
                    </td>
                </tr>
            </table>
            <table>
                <tr class="border-bottom">
                    <td style="width: 25%; border-right: 1px solid #000;">Edad:
                        {{ $solicitud_detalles->solicitud_patient['edad'] }}</td>
                    <td style="width: 25%; border-right: 1px solid #000;">Peso:
                        {{ $solicitud_detalles->solicitud_patient['peso'] }} kg</td>
                    <td style="width: 25%; border-right: 1px solid #000;">Sexo:
                        {{ $solicitud_detalles->solicitud_patient['sexo'] }}</td>
                    <td style="width: 25%">Fecha de nacimiento:
                        {{ date('d-m-Y', strtotime($solicitud_detalles->solicitud_patient['fecha_nacimiento'])) }}</td>
                </tr>
            </table>
            <table>
                <tr class="border-bottom">
                    <td style="width: 33.33%; border-right: 1px solid #000;">Vía de administración:
                        {{ $solicitud_detalles->solicitud_patient['via_administracion'] }}</td>
                    <td style="width: 33.33%; border-right: 1px solid #000;">Tiempo de infusión:
                        {{ $solicitud_detalles->solicitud_patient['tiempo_infusion_min'] }}</td>
                    <td style="width: 33.33%">No. Bolsa: 1</td>
                </tr>
                <tr class="border-bottom">
                    <td style="width: 33.33%; border-right: 1px solid #000;">Sobrellenado:
                        {{ $solicitud_detalles->solicitud_detail['sobrellenado_ml'] }}</td>
                    <td style="width: 33.33%; border-right: 1px solid #000;">Vol. Total:
                        {{ $solicitud_detalles->solicitud_detail['volumen_total'] }}</td>
                    <td style="width: 33.33%">NPT: {{ $solicitud_detalles->solicitud_detail['npt'] }}</td>
                </tr>
            </table>
            <table style="margin-top: 0.5rem">
                <tr>
                    <td class="bold" style="border-bottom: 2px solid #000; text-align:right; width: 30%">
                        MACRONUTRIENTES:</td>
                    <td></td>
                </tr>
            </table>
            <table style="margin-top: 0.5rem">
                <tr>
                    <td class="bold" style="width: 50%">AMINOÁCIDOS:</td>
                    <td class="bold" style="width: 50%">CARBOHIDRATOS:</td>
                </tr>
                <tr>
                <tr>
                    <td>
                        Aminoácidos Adulto 10%: {{ isset($variables['variable4']) ? $variables['variable4'] : '' }}
                    </td>
                    <td>
                        Dextrosa 50%: {{ isset($variables['variable8']) ? $variables['variable8'] : '' }}
                    </td>
                </tr>
                <tr>
                    <td>
                        Aminoácidos pediátricos 10%:
                        {{ isset($variables['variable5']) ? $variables['variable5'] : '' }}
                    </td>
                    <td>
                        <p class="bold m-0">LÍPIDOS</p>
                        MCT/LCT 20%: {{ isset($variables['variable9']) ? $variables['variable9'] : '' }}
                    </td>
                </tr>
                <tr>
                    <td>
                        Aminoácidos de cadena ramificada 8%:
                        {{ isset($variables['variable6']) ? $variables['variable6'] : '' }}
                    </td>
                    <td>SMOF 20%: {{ isset($variables['variable10']) ? $variables['variable10'] : '' }}</td>
                </tr>
                <tr>
                    <td>
                        Aminoácidos esenciales 5.4%:
                        {{ isset($variables['variable7']) ? $variables['variable7'] : '' }}
                    </td>
                </tr>
                <tr>
                    <td>
                        Cloruro de Sodio 0.9%: {{ isset($variables['variable36']) ? $variables['variable36'] : '' }}
                    </td>
                </tr>
            </table>
            <table style="margin-top: 0.5rem">
                <tr>
                    <td class="bold" style="width: 50%">ELECTROLITOS:</td>
                    <td class="bold" style="width: 50%"></td>
                </tr>
                <tr>
                    <td>
                        Cloruro de Sodio (3 mEq/mL Na):
                        {{ isset($variables['variable11']) ? $variables['variable11'] : '' }}
                    </td>
                    <td>
                        Cloruro de Potasio (4 mEq/mL):
                        {{ isset($variables['variable15']) ? $variables['variable15'] : '' }}
                    </td>
                </tr>
                <tr>
                    <td>
                        Acetato de Sodio (4 mEq/mL):
                        {{ isset($variables['variable12']) ? $variables['variable12'] : '' }}
                    </td>
                    <td>
                        Acetato de Potasio (2 mEq/mL):
                        {{ isset($variables['variable16']) ? $variables['variable16'] : '' }}
                    </td>
                </tr>
                <tr>
                    <td>
                        Fosfato de Sodio (4 mEq/mL):
                        {{ isset($variables['variable13']) ? $variables['variable13'] : '' }}
                    </td>
                    <td>
                        Fosfato de Potasio (2 mEq/mL): {{ isset($variables['variable17']) ? $variables['variable17'] : '' }}
                    </td>
                </tr>
                <tr>
                    <td>
                        Sulfato de Magnesio (0.81 mEq/mL): {{ isset($variables['variable14']) ? $variables['variable14'] : '' }}
                    </td>
                    <td>
                        Gluconato de Calcio (0.465 mEq/mL): {{ isset($variables['variable18']) ? $variables['variable18'] : '' }}
                    </td>
                </tr>
            </table>
            <table style="margin-top: 0.5rem">
                <tr>
                    <td class="bold" style="width: 50%">ADITIVOS:</td>
                    <td class="bold" style="width: 50%"></td>
                </tr>
                <tr>
                    <td>Ácidos Grasos Omega 3 10%: {{ isset($variables['variable19']) ? $variables['variable19'] : '' }}</td>
                    <td>MVI: {{ isset($variables['variable28']) ? $variables['variable28'] : '' }}</td>
                </tr>
                <tr>
                    <td>Albúmina 25% (0.25 g/mL): {{ isset($variables['variable20']) ? $variables['variable20'] : '' }}</td>
                    <td>MVI: {{ isset($variables['variable29']) ? $variables['variable29'] : '' }}</td>
                </tr>
                <tr>
                    <td>Albúmina 20% (0.20 g/mL): {{ isset($variables['variable21']) ? $variables['variable21'] : '' }}</td>
                    <td>Ácido Folínico (12.5 mg/mL): {{ isset($variables['variable30']) ? $variables['variable30'] : '' }}</td>
                </tr>
                <tr>
                    <td>Glutamina 20%: {{ isset($variables['variable22']) ? $variables['variable22'] : '' }}</td>
                    <td>Selenio (40 mcg/mL): {{ isset($variables['variable31']) ? $variables['variable31'] : '' }}</td>
                </tr>
                <tr>
                    <td>Cromo (4 mcg/mL): {{ isset($variables['variable23']) ? $variables['variable23'] : '' }}</td>
                    <td>Vitamina C (100 mg/mL): {{ isset($variables['variable32']) ? $variables['variable32'] : '' }}</td>
                </tr>
                <tr>
                    <td>Heparina (1000 UI/mL): {{ isset($variables['variable24']) ? $variables['variable24'] : '' }}</td>
                    <td>Vitamina K (10 mg/mL): {{ isset($variables['variable33']) ? $variables['variable33'] : '' }}</td>
                </tr>
                <tr>
                    <td>L-Carnitina (200 mg/mL): {{ isset($variables['variable25']) ? $variables['variable25'] : '' }}</td>
                    <td>Zinc (1 mg/mL): {{ isset($variables['variable34']) ? $variables['variable34'] : '' }}</td>
                </tr>
                <tr>
                    <td>Insulina (100 UI/mL): {{ isset($variables['variable26']) ? $variables['variable26'] : '' }}</td>
                    <td>L-Cisteina (50mg/mL): {{ isset($variables['variable35']) ? $variables['variable35'] : '' }}</td>
                </tr>
                <tr>
                    <td>Manganeso (100 mcg/mL): {{ isset($variables['variable27']) ? $variables['variable27'] : '' }}</td>
                    <td>Set de Infusión: {{ isset($variables['variable40']) ? 'Si' : 'No' }}</td>
                </tr>
            </table>
            <table>
                <tr>
                    <td style="width: 50%">Cloruro de Sodio (3 mEq/mL Na)</td>
                    <td style="width: 50%">Cloruro de Potasio (4 mEq/mL)</td>
                </tr>
            </table>
            <table style="margin-top: 0.5rem">
                <tr>
                    <td class="bold">OBSERVACIONES</td>
                </tr>
                <tr>
                    <td style="border-style: solid; border-width: 1px; height: 4rem;">
                        {{ $solicitud_detalles->solicitud_detail->observaciones }}

                    </td>
                </tr>
            </table>
            <table>
                <tr>
                    <td>Fecha y hora de entrega: {{ $solicitud_detalles->solicitud_detail->fecha_hora_entrega }}</td>
                    <td>Cédula profesional: {{ $solicitud_detalles->solicitud_detail->cedula }}</td>
                </tr>
                <tr>
                     <td>Nombre de quien entrega:</td>
                    <td>Nombre del médico: {{ $solicitud_detalles->solicitud_detail->nombre_medico }}</td>
                </tr>
                <tr>
                    <td>Temperatura en la que se entrega:</td>
                    <td></td>
                </tr>

            </table>
        </div>
    </div>
</body>

</html>
