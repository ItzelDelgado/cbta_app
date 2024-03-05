<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Orden de preparación</title>


    <style>
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
            padding: 0 2px;
            font-family: "Arial", sans-serif;
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

        p {
            font-size: 10px
        }

        .liberacion-area td {
            margin: 0;
            padding: 0 8px;
        }

        .firmas td{
            border: none;
            text-align: center;
            border-top: 1px solid black;
        }

        .elementos td{
            margin: 0;
            padding: 0 8px;
            
        }

    </style>

</head>


<body>

    <div class="contenedor">
        <!-- Contenedor principal con borde negro -->
        <div class="introduccion">
            <table>
                <tr>
                    <td>
                        {{ $ordenPreparacion }}
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
            <tr style="background-color: #1F4E78; color: white; font-weight: bold;">
                <td style="text-align: center">ORDEN DE PREPARACIÓN DE NUTRICIÓN PARENTERAL TOTAL</td>
            </tr>
        </table>

        <table>
            <tr>
                <td style="border: none; text-align: right"><strong>Fecha de elaboración:</strong>
                    <span>29-nov-23</span></td>
            </tr>
            <tr>
                <td style="border: none;"><strong>LIBERACIÓN DE ÁREA</strong></td>
            </tr>
        </table>
        <table class="liberacion-area" style="margin-bottom: 0.3rem;">
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
            <tr>
                <td style="border: none"><strong>¿Se considera liberada el área para la preparación de la
                        mezcla?</strong></td>
                <td></td>
                <td></td>
            </tr>
        </table>
        <table style="width: 350px; margin-bottom: 0.5rem;">
            <tr>
                <td>Sanitizante:</td>
                <td style="width: 20px;"></td>
                <td>Alcohol Isopropilico</td>
                <td style="width: 20px;"></td>
                <td>Critical 0.2%</td>
            </tr>
        </table>
        <div style=" border: 2px solid black; margin-bottom: 0.5rem;">
            <table>
                <tr>
                    <td style="border: none"><strong>No. de lote de la mezcla:</strong> <span>L29112301</span></td>
                    <td style="border: none"><strong>No. de orden de preparación:</strong> NUT 29112301</td>
                </tr>
            </table>
            <p style="padding: 8px; border-top: 1px solid black; border-bottom: 1px solid black;"><strong>DATOS DEL
                    PACIENTE</strong></p>
            <table>
                <tr>
                    <td style="border: none;"><strong>Nombre:</strong> <span>ALFREDO LOPEZ CARDONA</span></td>
                    <td style="border: none;"><strong>Médico tratante:</strong> <span>Dr. Alfonso Fajardo R.</span></td>
                </tr>
                <tr>
                    <td style="border: none;"><strong>Registro:</strong> <span
                            style="border-bottom: 2px solid black;">10053619</span></td>
                    <td style="border: none"><strong>Diagnostico:</strong> <span
                            style="border-bottom: 2px solid black;">SANGRADO DEL TUBO DIGESTIVO ALTO</span></td>
                </tr>
            </table>
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="border: none"><strong>Fecha de nacimiento:</strong> <span
                            style="border-bottom: 2px solid black;">14/ago/1976</span></td>
                    <td style="border: none"><strong>Genero:</strong> <span
                            style="border-bottom: 2px solid black;">M</span></td>
                    <td style="border: none"><strong>Talla:</strong> <span
                            style="border-bottom: 2px solid black;">S/D</span></td>
                    <td style="border: none"><strong>Peso:</strong> <span
                            style="border-bottom: 2px solid black;">S/D</span></td>
                    <td style="border: none"><strong>SC:</strong> <span
                            style="border-bottom: 2px solid black;">S/D</span></td>
                </tr>
                <tr>
                    <td style="border: none" colspan="5"><strong>Sitio de procedencia:</strong> <span
                            style="border-bottom: 2px solid black;">S/D</span></td>

                </tr>
            </table>
        </div>
        <p style="padding: 8px; border: 1px solid black; margin: 0;"><strong>NUTRICIÓN PARENTERAL TOTAL</strong></p>
        <table class="elementos">
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
        <table>
            <tr>
                <td>Volumen Total <span style="border: 2px solid black;">1200 ml</span></td>
                <td>Contenedor <span style="border: 2px solid black;">2000 ml</span></td>
            </tr>
        </table>
        <p style="padding: 8px; text-align: center; margin: 0;"><strong>Bolsa EVA</strong></p>
        <table>
            <tr>
                <td></td>
                <td><strong>No. de lote</strong></td>
                <td><strong>Caducidad</strong></td>
                <td><strong>Nombre comercial</strong></td>
                <td><strong>Nombre genérico</strong></td>
            </tr>
            <tr>
                <td>1</td>
                <td>10C3</td>
                <td>01/abr/2023</td>
                <td>BOLSA EVA IPM</td>
                <td>BOLSA EVA 3000mL</td>
            </tr>
        </table>
        <p style="padding: 8px; text-align: center; margin: 0;"><strong>Bolsa EVA</strong></p>
        <table>
            <tr>
                <td></td>
                <td><strong>No. de lote</strong></td>
                <td><strong>Caducidad</strong></td>
                <td><strong>Nombre comercial</strong></td>
                <td><strong>Nombre genérico</strong></td>
            </tr>
            <tr>
                <td>1</td>
                <td>N/A</td>
                <td>N/A</td>
                <td>N/A</td>
                <td>N/A</td>
            </tr>
        </table>
        <br>
        <table class="firmas">
            <tr>
                <td>Elaboró <br> Nombre y firma</td>
                <td>Validó <br> Nombre y firma</td>
                <td>Preparó <br> Nombre y firma</td>
            </tr>
        </table>
    </div>


</body>

</html>
