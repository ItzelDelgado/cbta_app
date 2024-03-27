<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Regristro de Envio</title>


    <style>
        @page {
            margin: 0px;
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
            <table class="introduccion">
                <tr>
                    <td style="text-align: center; padding-right: 2rem">
                        <strong>CENTRO DE MEZCLAS ESTÉRILES PRODIFEM</strong>
                    </td>
                </tr>
                <tr>
                    <td>Cliente: HOSPITAL ÁNGELES METROPOLITANO</td>
                    <td>Lote: N17122301</td>
                </tr>
                <tr>
                    <td>Paciente: MARCO ANTONIO PEREZ MORENO</td>
                    <td>FN: 31-oct-66</td>
                </tr>
                <tr>
                    <td>Médico: Dr. Antonio Faiardo</td>
                </tr>
                <tr>
                    <td>NUTRICIÓN PARENTERAL</td>
                </tr>
            </table>
            <table>
                <tr>
                    <td>COMPONENTES</td>
                    <td>CONTENIDO</td>
                </tr>
                <tr>
                    <td>AMINOACIDOS  8% CR</td>
                    <td>52,8 g</td>
                </tr>
            </table>
            <table>
                <tr>
                    <td>Osmolaridad: 1209,823077</td>
                    <td>Vol. tot: 1572,77</td>
                </tr>
                <tr>
                    <td>Sobrellenado: 0</td>
                </tr>
                <tr>
                    <td>Administrar en: 24</td>
                    <td>Vel. Infusión: 1,092201389 ml/min</td>
                </tr>
            </table>
            <table>
                <tr>
                    <td>MANTENER EN REFIGERACIÓN <br>
                        HASTA UNA HORA ANTES DE SU ADMINISTRACIÓN</td>
                </tr>
                <tr>
                    <td>Fecha y hora de preparación: 17-dic-23 8:30:00</td>
                    <td>Fecha y hora límite de uso: 19-dic-23 8:30:00</td>
                </tr>
            </table>
        </div>
</body>

</html>
