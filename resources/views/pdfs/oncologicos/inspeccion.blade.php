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

        .fond-bold {
            font-weight: bold;
        }

        .text-center {
            text-align: center
        }
    </style>

</head>


<body>

    <div class="contenedor border-1 px-1">
        <!-- Contenedor principal con borde negro -->
        <div class="introduccion">
            <table style="">
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
                <td style="text-align: center;">INSPECCIÓN DE MEZCLAS ÉSTERILES ONCOLOGICAS</td>
            </tr>
        </table>
        <p>VERIFICACIÓN DE ÁREAS</p>
        <div class="border-1 py-1 px-1">
            <table>
                <tr>
                    <td style="width: 90%"></td>
                    <td class="border-1 text-center" style="width: 5%"><strong>SI</strong></td>
                    <td class="border-1 text-center" style="width: 5%"><strong>NO</strong></td>
                </tr>
                <tr>
                    <td class="text-right px-1">¿El área se encuentra limpia y disponible?</td>
                    <td class="border-1"></td>
                    <td class="border-1"></td>
                </tr>
                <tr>
                    <td class="text-right px-1">¿El área se encuentra libre de material y documentos ajenos a la mezcla
                        en
                        turno a inspeccionar?</td>
                    <td class="border-1"></td>
                    <td class="border-1"></td>
                </tr>
            </table>
        </div>

        <div class="mx-1">
            <table>
                <tr>
                    <td style="width: 70%" class="text-right">No. de lote:</td>
                    <td style="width: 30%" class="border-b-1"></td>
                </tr>
                <tr>
                    <td style="width: 70%" class="text-right">No. de orden de preparación:</td>
                    <td style="width: 30%" class="border-b-1"></td>
                </tr>
            </table>
            <table>
                <tr>
                    <td style="width: 5%">Fecha:</td>
                    <td style="width: 10%" class="border-b-1"></td>
                    <td style="width: 35%"></td>
                    <td style="width: 20%" class="text-right">Hora de inspección:</td>
                    <td style="width: 30%" class="border-b-1"></td>
                </tr>
            </table>
            <table class="mt-2">
                <tr>
                    <td style="width: 20%" class="text-left"><strong>Tipo de contenedor</strong></td>
                    <td style="width: 10%" class="text-left"><strong>Frasco/bolsa</strong></td>
                    <td style="width: 5%"></td>
                    <td style="width: 5%" class="border-1"></td>
                    <td style="width: 10%" class="text-center"><strong>Jeringa</strong></td>
                    <td style="width: 5%" class="border-1"></td>
                    <td style="width: 10%" class="text-center"><strong>Otro</strong></td>
                    <td style="width: 5%" class="border-1"></td>
                    <td style="width: 5%"></td>
                    <td style="width: 10%" class="border-b-1"></td>
                </tr>
            </table>
            <table class="mt-2">
                <tr>
                    <td style="width: 48%">
                        <table class="">
                            <tr>
                                <td class="border-1 px-1 text-center"><strong>Inspección fisica del contenedor</strong></td>
                            </tr>
                        </table>
                        <table>
                            <tr>
                                <td style="width: 70%" class="border-l-1 px-1"></td>
                                <td style="width: 15%" class=" text-center"><strong>Si</strong></td>
                                <td style="width: 15%" class="border-r-1 text-center"><strong>No</strong></td>
                            </tr>
                            <tr>
                                <td class="border-1 px-1">¿Esta rotulado</td>
                                <td class="border-1 px-1"></td>
                                <td class="border-1 px-1"></td>
                            </tr>
                            <tr>
                                <td class="border-1 px-1">No. De lote</td>
                                <td class="border-1 px-1"></td>
                                <td class="border-1 px-1"></td>
                            </tr>
                            <tr>
                                <td class="border-1 px-1">Medicamento</td>
                                <td class="border-1 px-1"></td>
                                <td class="border-1 px-1"></td>
                            </tr>
                            <tr>
                                <td class="border-1 px-1">Dosis / volumen total</td>
                                <td class="border-1 px-1"></td>
                                <td class="border-1 px-1"></td>
                            </tr>
                            <tr>
                                <td class="border-1 px-1">Volumen de medicamento</td>
                                <td class="border-1 px-1"></td>
                                <td class="border-1 px-1"></td>
                            </tr>
                            <tr>
                                <td class="border-1 px-1">Rubrica del preparador</td>
                                <td class="border-1 px-1"></td>
                                <td class="border-1 px-1"></td>
                            </tr>
                            <tr>
                                <td class="border-1 px-1">¿Tiene sello de seguridad?</td>
                                <td class="border-1 px-1"></td>
                                <td class="border-1 px-1"></td>
                            </tr>
                            <tr>
                                <td class="border-1 px-1">¿Presenta grietas?</td>
                                <td class="border-1 px-1"></td>
                                <td class="border-1 px-1"></td>
                            </tr>
                            <tr>
                                <td class="border-1 px-1">¿Presenta fugas?</td>
                                <td class="border-1 px-1"></td>
                                <td class="border-1 px-1"></td>
                            </tr>
                            <tr>
                                <td class="border-1 px-1">¿Se encuentra roto?</td>
                                <td class="border-1 px-1"></td>
                                <td class="border-1 px-1"></td>
                            </tr>
                        </table>


                        <table class="border-1 mt-2">
                            <tr>
                                <td style="width: 70%" class="border-1 px-1">¿Aprueba la inspección física del
                                    contenedor?</td>
                                <td style="width: 15%" class="border-1 px-1"></td>
                                <td style="width: 15%" class="border-1 px-1"></td>
                            </tr>
                        </table>

                    </td>
                    <td style="width: 4%"></td>
                    <td style="width: 48%">
                        <table>
                            <tr>
                                <td class="border-1 px-1 text-center"><strong>Inspección del contenido</strong></td>
                            </tr>
                        </table>
                        <table>
                            <tr>
                                <td style="width: 70%" class="px-1 border-l-1"></td>
                                <td style="width: 15%" class="text-center px-1"><strong>Si</strong></td>
                                <td style="width: 15%" class="text-center px-1 border-r-1"><strong>No</strong></td>
                            </tr>
                            <tr>
                                <td class="border-1 px-1">¿La coloración es apropiada?</td>
                                <td class="border-1 px-1"></td>
                                <td class="border-1 px-1"></td>
                            </tr>
                            <tr>
                                <td class="border-1 px-1">¿Es homogéneo?</td>
                                <td class="border-1 px-1"></td>
                                <td class="border-1 px-1"></td>
                            </tr>
                            <tr>
                                <td class="border-1 px-1">¿Presenta partículas?</td>
                                <td class="border-1 px-1"></td>
                                <td class="border-1 px-1"></td>
                            </tr>
                            <tr>
                                <td class="border-1 px-1">¿Presenta turbidez?</td>
                                <td class="border-1 px-1"></td>
                                <td class="border-1 px-1"></td>
                            </tr>
                            <tr>
                                <td class="border-1 px-1">¿Volumen correcto?</td>
                                <td class="border-1 px-1"></td>
                                <td class="border-1 px-1"></td>
                            </tr>
                        </table>
                        <table class="mt-2">
                            <tr>
                                <td style="width: 70%" class="border-1 px-1">¿Aprueba la inspección del contenido?
                                </td>
                                <td style="width: 15%" class="border-1 px-1"></td>
                                <td style="width: 15%" class="border-1 px-1"></td>
                            </tr>
                        </table>
                        <table class="mt-2">
                            <tr>
                                <td style="width: 50%" class="text-right">Dosis en volumen (mL):</td>
                                <td style="width: 50%" class="border-b-1"></td>
                            </tr>
                            <tr>
                                <td style="width: 50%" class="text-right">Peso de la mezcla (g):</td>
                                <td style="width: 50%" class="border-b-1"></td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
            <div class="mt-2">
                <table class="px-1">
                    <tr class="">
                        <td class="border-1 px-1" style="width: 60%">LA MEZCLA SE CONSIDERA APROBADA:</td>
                        <td class="border-1 text-center" style="width: 5%"><strong>Si</strong></td>
                        <td class="border-1" style="width: 5%"></td>
                        <td class="border-1 text-center" style="width: 5%"><strong>No</strong></td>
                        <td class="border-1" style="width: 5%"></td>
                        <td style="width: 20%"></td>
                    </tr>
                </table>
            </div>
            <div class="mt-2 border-1">
                <table class="px-1">
                    <tr class="">
                        <td>Observaciones:</td>
                    </tr>
                </table>
            </div>
            <table class="mt-2" style="margin-top: 4rem; margin-bottom: 3rem;">
                <tr>
                    <td style="width: 10%"></td>
                    <td style="width: 35%" class="border-b-1"></td>
                    <td style="width: 10%"></td>
                    <td style="width: 35%" class="border-b-1"></td>
                    <td style="width: 10%"></td>
                </tr>
                <tr>
                    <td style="width: 10%"></td>
                    <td style="width: 35%" class="text-center">Revisó</td>
                    <td style="width: 10%"></td>
                    <td style="width: 35%" class="text-center">Aprobó</td>
                    <td style="width: 10%"></td>
                </tr>
                <tr>
                    <td style="width: 10%"></td>
                    <td style="width: 35%" class="text-center">Nombre/sello y firma</td>
                    <td style="width: 10%"></td>
                    <td style="width: 35%" class="text-center">Nombre/sello y firma</td>
                    <td style="width: 10%"></td>
                </tr>
                <tr>
                    <td style="width: 10%"></td>
                    <td style="width: 35%" class="text-center">Unidad de Calidad</td>
                    <td style="width: 10%"></td>
                    <td style="width: 35%" class="text-center">Unidad de Calidad</td>
                    <td style="width: 10%"></td>
                </tr>
            </table>
        </div>





    </div>




</body>

</html>
