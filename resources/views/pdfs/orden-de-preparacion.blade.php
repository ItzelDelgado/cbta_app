<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    {{-- Font Awesome --}}
    <script src="https://kit.fontawesome.com/b023f039d3.js" crossorigin="anonymous"></script>
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Scripts sweetalert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Styles -->
    @livewireStyles

    @stack('css')

</head>


<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Contenido con Borde Negro</title>
    <style>
        /* Estilos básicos */
        body {
            font-family: Arial, sans-serif;
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
    </style>
</head>

<body>

    <div class="contenedor"> <!-- Contenedor principal con borde negro -->
        <div class="introduccion">
            <table>
                <tr>
                    <td>
                        {{$ordenPreparacion}}
                    </td>
                    <td style="text-align: center; padding-right: 2rem">
                        <strong>CENTRAL DE MEZCLAS ESTÉRILES PRODIFEM <br> NUTRICIONES PARENTERALES</strong>
                    </td>
                    <td>

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
            <tr style="background-color: #1F4E78; color: white; font-weight: 600;">
                <td style="text-align: center">ORDEN DE PREPARACIÓN DE NUTRICIÓN PARENTERAL TOTAL</td>
            </tr>
        </table>

        <table>
            <tr>
                <td style="border: none; text-align: right">Fecha de elaboración: <span>29-nov-23</span></td>
            </tr>
            <tr >
                <td style="border: none;">LIBERACIÓN DE ÁREA</td>
            </tr>
        </table>
        <table>
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
        </table>
        <table>
            <tr>
                <td>Sanitizante:</td>
                <td></td>
                <td>Alcohol Isopropilico</td>
                <td></td>
                <td>Critical 0.2%</td>
            </tr>
        </table>
        <table class="blueTable">
            <thead>
                <tr>
                    <th>&nbsp;</th>
                    <th>Volumen(mL)</th>
                    <th>Dosis</th>
                    <th>Denominaci&oacute;n g&eacute;nerica</th>
                    <th>Denominaci&oacute;n comercial</th>
                    <th>Presentaci&oacute;n</th>
                    <th>Lote</th>
                    <th>Caducidad</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td>455</td>
                    <td>455mL</td>
                    <td>AMINOACIDOS 8%CR</td>
                    <td>Aminosteril N-Hepa 8%</td>
                    <td>500 mL</td>
                    <td>23D0586</td>
                    <td>31-mar-25</td>
                </tr>
                <tr>
                    <td>2</td>
                    <td>cell2_2</td>
                    <td>cell3_2</td>
                    <td>cell4_2</td>
                    <td>cell5_2</td>
                    <td>cell6_2</td>
                    <td>cell7_2</td>
                    <td>cell8_2</td>
                </tr>
                <tr>
                    <td>3</td>
                    <td>cell2_3</td>
                    <td>cell3_3</td>
                    <td>cell4_3</td>
                    <td>cell5_3</td>
                    <td>cell6_3</td>
                    <td>cell7_3</td>
                    <td>cell8_3</td>
                </tr>
                <tr>
                    <td>4</td>
                    <td>cell2_4</td>
                    <td>cell3_4</td>
                    <td>cell4_4</td>
                    <td>cell5_4</td>
                    <td>cell6_4</td>
                    <td>cell7_4</td>
                    <td>cell8_4</td>
                </tr>
            </tbody>
        </table>
    </div>

</body>

</html>
