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

        .introduccion table {
            width: 100%;
            /* Ajusta esto según necesites */
            border-collapse: collapse;
            border: none;
        }

        .introduccion td {
            border: none;
        }

        /* Contenedor principal con borde negro */
        .contenedor {
            border: 2px solid black;
            padding: 0 2px;
            font-family: "Arial", sans-serif;
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
    </style>

</head>


<body>

    <div class="contenedor">
        <!-- Contenedor principal con borde negro -->
        <div>
            <table class="introduccion">
                <tr>
                    <td>
                        <img style="width: 7rem" src="{{ asset('img/Centro Biotecnologico de Terapias Avanzadas.png') }}"
                            alt="">
                    </td>
                    <td style="text-align: center; padding-right: 171px; font-weight: bold; font-size: 15px">
                        <strong>HOSPITAL ANGELES PRDREGAL</strong>
                    </td>
                    <td>

                    </td>
                </tr>
            </table>
            <table>
                <tr>
                    <td style="border: none; border-top: 1px solid black; font-weight: bold">Fecha de envío:</td>
                    <td style="text-align: right; border: none; border-top: 1px solid black; ">DOMICILIO CLIENTE
                        RECEPTOR:</td>
                </tr>
                <tr>
                    <td style="border: none; font-weight: bold">No. 00018</td>
                    <td style="text-align: right; border: none">Tlacotlalpan 59 Col. Roma Sur, Cuauhtemoc CDMX, 06760
                    </td>
                </tr>
            </table>
            <table>
                <tr>
                    <td style="text-align: center; border-top: 1px solid black; border-bottom: none; background-color: #1F4E78; color: white; font-weight: bold;"><strong>DATOS DEL
                            PACIENTE</strong></td>
                </tr>
            </table>
            <table>
                <tr>
                    <th style="background: #D9E2F3; width: 40%; text-align: center">NOMBRE COMPLETO</th>
                    <th style="background: #D9E2F3; width: 20%; text-align: center">FECHA DE NACIMIENTO</th>
                    <th style="background: #D9E2F3; width: 10%; text-align: center">EDAD(a)</th>
                    <th style="background: #D9E2F3; width: 10%; text-align: center">GENERO</th>
                    <th style="background: #D9E2F3; width: 20%; text-align: center">SUPERFICIE CORPORAL (m2)</th>
                </tr>
                <tr>
                    <td>EDA BERNARDA SUAREZ RIOX</td>
                    <td>20/ago/1967</td>
                    <td>55</td>
                    <td>F</td>
                    <td>S/D</td>
                </tr>
            </table>
            <table>
                <tr>
                    <th style="border-top: none; background: #D9E2F3; width: 40%; text-align: center">DIAGNOSTICO</th>
                    <th style="border-top: none; background: #D9E2F3; width: 20%; text-align: center">SERVICIOS</th>
                    <th style="border-top: none; background: #D9E2F3; width: 20%; text-align: center">No. De EXPREDIENTE</th>
                    <th style="border-top: none; background: #D9E2F3; width: 20%; text-align: center">MÉDICO TRATANTE</th>
                </tr>
                <tr>
                    <td>ENFERMEDAD DE STILL</td>
                    <td>Medicina interna</td>
                    <td></td>
                    <td>Dr.Alfonso Fajardo R</td>
                </tr>
            </table>
            <table>
                <tr>
                    <td style="border-top: none; width: 50%; text-align: center; border-top: 1px solid black; border-bottom: none; background-color: #1F4E78; color: white; font-weight: bold; padding: 8px 0"><strong>DATOS DE LAS MEZCLAS</strong></td>
                    <td style="border-top: none; width: 50%; text-align: center; text-align: center; border-top: 1px solid black; border-bottom: none; background-color: #1F4E78; color: white; font-weight: bold; padding: 8px 0"><strong>COSTO MEDICAMENTO</strong></td>
                </tr>
            </table>
            <table>
                <tr>
                    <th style="border-top: none; background: #D9E2F3; width: 10%; text-align: center"><strong>No</strong></th>
                    <th style="border-top: none; background: #D9E2F3; width: 20%; text-align: center"><strong>MEDICAMENTO</strong></th>
                    <th style="border-top: none; background: #D9E2F3; width: 10%; text-align: center"><strong>DOSIS</strong></th>
                    <th style="border-top: none; background: #D9E2F3; width: 10%; text-align: center"><strong>LOTE DE LA MEZCLA</strong></th>
                    <th style="border-top: none; background: #D9E2F3; width: 12.5%; text-align: center"><strong>PRESENTACIÓN</strong></th>
                    <th style="border-top: none; background: #D9E2F3; width: 12.5%; text-align: center"><strong>CANTIDAD</strong></th>
                    <th style="border-top: none; background: #D9E2F3; width: 12.5%; text-align: center"><strong>PRECIO (ml)</strong></th>
                    <th style="border-top: none; background: #D9E2F3; width: 12.5%; text-align: center"><strong>SUBTOTAL</strong></th>
                </tr>
                <tr>
                    <td>1</td>
                    <td><strong>AMINOACIDOS 8%CR</strong></td>
                    <td><strong>721,8</strong>mL</td>
                    <td>N10122304</td>
                    <td>FRASCO 500ML</td>
                    <td>721,80mL</td>
                    <td>$5,220</td>
                    <td>$3.767,80</td>
                </tr>
                <tr>
                    <td>1</td>
                    <td><strong>AMINOACIDOS 8%CR</strong></td>
                    <td><strong>721,8</strong>mL</td>
                    <td>N10122304</td>
                    <td>FRASCO 500ML</td>
                    <td>721,80mL</td>
                    <td>$5,220</td>
                    <td>$3.767,80</td>
                </tr>
                <tr>
                    <td>1</td>
                    <td><strong>AMINOACIDOS 8%CR</strong></td>
                    <td><strong>721,8</strong>mL</td>
                    <td>N10122304</td>
                    <td>FRASCO 500ML</td>
                    <td>721,80mL</td>
                    <td>$5,220</td>
                    <td>$3.767,80</td>
                </tr>
            </table>
            <table>
                <tr>
                    <td style="text-align: right; border-top: none"><strong>Total $12.876,40</strong></td>
                </tr>
            </table>
            <br>
            <table>
                <tr>
                    <td style="border: none;">
                        <hr style="width: 170px;  background-color: black; margin: 0; padding: 0; margin: 0 auto">
                    </td>
                </tr>
                <tr style="margin: 0; padding: 0">
                    <td style="border: none; text-align: center; border-top: none; margin: 0; padding: 0">Nombre
                        completo/firma <br> Fecha de recibido</td>
                </tr>
            </table>
        </div>


</body>

</html>
