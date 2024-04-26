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

        .border-top{
            border-right: 1px solid #000;
            border-left: 1px solid #000;
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
        }

        .border-top td {
            padding-left: 4px;
        }

        .border-bottom{
            border-right: 1px solid #000;
            border-left: 1px solid #000;
            border-top: none;
            border-bottom: 1px solid #000;
        }

        .border-bottom td {
            padding-left: 4px;
        }

        .bold{
            font-weight: bold;
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
            <table>
                <tr>
                    <td style="border-top: 2px solid black; text-align: right; color: blue;">
                        FTO-NPT-023-005
                    </td>
                </tr>
                <tr style="background-color: #1F4E78; color: white; font-weight: bold;">
                    <td style="text-align: center;">SOLICITUD DE NUTRICIÓN PARENTERAL</td>
                </tr>
            </table>
            <table>
                <tr class="border-top">
                    <td style="width: 50%; border-right: 1px solid #000;">Cliente/hospital:<span>Hospita Angeles</span></td>
                    <td style="width: 50%">Fecha:<span>20/08/1998</span></td>
                </tr>
            </table>
            <table>
                <tr class="border-bottom">
                    <td style="width: 40%; border-right: 1px solid #000;">Paciente: Luis Angel Rojas Espinoza</td>
                    <td style="width: 20%; border-right: 1px solid #000;">servicio:</td>
                    <td style="width: 20%; border-right: 1px solid #000;">Cama:</td>
                    <td style="width: 20%">Piso:</td>
                </tr>
            </table>
            <table>
                <tr class="border-bottom">
                    <td style="width: 50%; border-right: 1px solid #000;">Registro:</td>
                    <td style="width: 50%">Diagnóstico:</td>
                </tr>
            </table>
            <table>
                <tr class="border-bottom">
                    <td style="width: 25%; border-right: 1px solid #000;">Edad:</td>
                    <td style="width: 25%; border-right: 1px solid #000;">Peso:</td>
                    <td style="width: 25%; border-right: 1px solid #000;">Sexo:</td>
                    <td style="width: 25%">Fecha de nacimiento:</td>
                </tr>
            </table>
            <table>
                <tr class="border-bottom">
                    <td style="width: 33.33%; border-right: 1px solid #000;">Vía de administración:</td>
                    <td style="width: 33.33%; border-right: 1px solid #000;">Tiempo de infusión:</td>
                    <td style="width: 33.33%">No. Bolsa:</td>
                </tr>
                <tr class="border-bottom">
                    <td style="width: 33.33%; border-right: 1px solid #000;">Sobrellenado:</td>
                    <td style="width: 33.33%; border-right: 1px solid #000;">Vol. Total:</td>
                    <td style="width: 33.33%">NPT:</td>
                </tr>
            </table>
            <table>
                <tr>
                    <td class="bold" style="border-bottom: 2px solid #000; text-align:right; width: 30%">MACRONUTRIENTES:</td>
                    <td></td>
                </tr>
            </table>
            <table>
                <tr>
                    <td class="bold" style="width: 50%">AMINOÁCIDOS:</td>
                    <td class="bold" style="width: 50%">CARBOHIDRATOS:</td>
                </tr>
                <tr>
                    <td style="width: 50%">Aminoácidos Adulto 10%</td>
                    <td style="width: 50%">Dextrosa 50%</td>
                </tr>
            </table>
            <table>
                <tr>
                    <td class="bold" style="width: 50%">LÍPIDOS</td>
                </tr>
                <tr>
                    <td style="width: 50%">MCT/LCT 20%</td>
                </tr>
            </table>
            <table>
                <tr>
                    <td class="bold" style="border-bottom: 2px solid #000; text-align:right; width: 30%">ELECTROLITOS:</td>
                    <td></td>
                </tr>
            </table>
            <table>
                <tr>
                    <td style="width: 50%">Cloruro de Sodio (3 mEq/mL Na)</td>
                    <td style="width: 50%">Cloruro de Potasio (4 mEq/mL)</td>
                </tr>
            </table>
            <table>
                <tr>
                    <td class="bold" style="border-bottom: 2px solid #000; text-align:right; width: 30%">ADITIVOS:</td>
                    <td></td>
                </tr>
            </table>
            <table>
                <tr>
                    <td style="width: 50%">Ácidos Grasos Omega 3 10%</td>
                    <td style="width: 50%">Manganeso (100 mcg/mL)</td>
                </tr>
            </table>
            <table>
                <tr>
                    <td class="bold">OBSERVACIONES</td>
                    <td></td>
                </tr>
            </table>
            <table>
                <tr class="border-top">
                    <td>Fecha y hora de entrega:</td>
                    <td>Nombre del médico:</td>
                </tr>
                <tr class="border-bottom">
                    <td>Hora y hora de preparación:</td>
                    <td>Cédula profesional:</td>
                </tr>
            </table>
        </div>
    </div>
</body>

</html>
