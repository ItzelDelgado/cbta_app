<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Regristro de Envio</title>


    <style>
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
            padding: 2px 3px;
            font-size: 9px
        }

        th {
            background-color: #DEEAF6;
        }

        p {
            font-size: 10px
        }

        .tabla-format table {
            table-layout: fixed;
            border: 1px solid black;
        }

        .tabla-format td {
            border: 1px solid black;
        }

        .tabla-format td {
            word-wrap: break-word;
            /* Asegura que el texto se divida */
            /* max-width: 120px; */
            /* O el ancho específico que prefieras */
            overflow: auto;
            /* Hace desplazable el contenido que exceda el tamaño del td */
        }

    </style>

</head>


<body>

    <div class="contenedor">
        <!-- Contenedor principal con borde negro -->
        <div>
            <table class="introduccion" style="width: 650px; margin: 0 auto">
                <tr>
                    <td>
                        LOGO
                    </td>
                    <td style="text-align: center; padding-right: 2rem">
                        <strong>HOSPITAL ANGELES METROPOLITANO</strong>
                    </td>
                    <td>
                    </td>
                </tr>
            </table>
            <table>
                <tr>
                    <td style="border: none; border-top: 1px solid black; font-weight: bold">Fecha de envío:
                        <span>08-mar-24</span>
                    </td>
                    <td style="text-align: right; border: none; border-top: 1px solid black; ">DOMICILIO CLIENTE
                        RECEPTOR:</td>
                </tr>
                <tr>
                    <td style="border: none; font-weight: bold">No. 00018</td>
                    <td style="text-align: right; border: none">Tlacotlalpan 59 Col. Roma Sur, Cuauhtemoc CDMX, 06760
                    </td>
                </tr>
            </table>
            <table style="tabla-format">
                <tr>
                    <td style="text-align: center; border-top: 1px solid black; border-bottom: none"><strong>DATOS DEL
                            PACIENTE</strong></td>
                </tr>
            </table>
            <table style="tabla-format">
                <tr>
                    <th style="width: 50%">NOMBRE COMPLETO</th>
                    <th style="width: 20%">FECHA DE NACIMIENTO</th>
                    <th style="width: 5%">EDAD(a)</th>
                    <th style="width: 5%">GENERO</th>
                    <th style="width: 20%">SUPERFICIE CORPORAL (m2)</th>
                </tr>

                <tr>
                    <td>EDA BERNARDA SUAREZ RIOX</td>
                    <td>20/ago/1967</td>
                    <td>55</td>
                    <td>F</td>
                    <td>S/D</td>
                </tr>
            </table>
            <table style="tabla-format">
                <tr>
                    <th style="border-top: none; width: 50%">DIAGNOSTICO</th>
                    <th style="border-top: none; width: 20%">SERVICIOS</th>
                    <th style="border-top: none; width: 10%%">No. De EXPREDIENTE</th>
                    <th style="border-top: none; width: 20%">MÉDICO TRATANTE</th>
                </tr>
                <tr>
                    <td>ENFERMEDAD DE STILL</td>
                    <td>Medicina interna</td>
                    <td></td>
                    <td>Dr.Alfonso Fajardo R</td>
                </tr>
            </table>
            <table style="tabla-format">
                <tr>
                    <td style="border-top: none"><strong>DATOS DE LAS MEZCLAS</strong></td>
                </tr>
            </table>
            <table style="tabla-format">
                <tr>
                    <th style="border-top: none; width: 5%"><strong>No</strong></th>
                    <th style="border-top: none; width: 25%"><strong>MEDICAMENTO</strong></th>
                    <th style="border-top: none; width: 10%"><strong>DOSIS</strong></th>
                    <th style="border-top: none; width: 10%"><strong>VOLUMEN</strong></th>
                    <th style="border-top: none; width: 10%"><strong>LOTE DE LA MEZCLA</strong></th>
                    <th style="border-top: none; width: 20%"><strong>FECHA\HORA DE PREPARACIÓN</strong></th>
                    <th style="border-top: none; width: 20%"><strong>FECHA\HORA DE LIMITE DE USO</strong></th>
                </tr>
                <tr>
                    <td>1</td>
                    <td><strong>AMINOACIDOS 8%CR</strong></td>
                    <td><strong>70</strong>g</td>
                    <td>1967,00mL</td>
                    <td>N16122302</td>
                    <td>16/dic/2023 16:00h</td>
                    <td>18/dic/2023 16:00h</td>
                </tr>
            </table>
            <table>
                <tr>
                    <td style="text-align: right; border-top: none"><strong>Cantidad total de mezclas 1</strong></td>
                </tr>
            </table>
            <br>
            <table style="margin: 0 5rem">
                <tr>
                    <td style="border: none;">Recepción Cliente</td>
                </tr>
                <tr>
                    <td style="border: none;">
                        Fecha: <span style="border-bottom: 1px solid black;">16/20/28</span>
                    </td>
                </tr>
                <tr>
                    <td style="border: none;">Hora de recibido:</td>
                </tr>
                <tr>
                    <td style="border: none;">Temperatura:</td>
                </tr>
                <tr>
                    <td style="border: none;">Nombre completo/firma y sello:</td>
                </tr>
            </table>
        </div>
</body>

</html>
