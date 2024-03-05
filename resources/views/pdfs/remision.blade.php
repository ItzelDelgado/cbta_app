<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Remisión</title>


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



    </style>

</head>


<body>

    <div class="contenedor">
        <!-- Contenedor principal con borde negro -->
        <div class="introduccion">
         <h1>Hospital nombre</h1>
         <table>
            <tr>
                <td>fecha de envío:</td>
                <td>DOMICILIO CLIENTE RECEPTOR:</td>
            </tr>
            <tr>
                <td>No. 00018</td>
                <td>Tlacotlalpan 59 Col. Roma Sur, Cuauhtemoc CDMX, 06760</td>
            </tr>
         </table>
         <p>DATOS DEL PACIENTE</p>
         <table>
            <thead>
                <tr>
                    <th>NOMBRE COMPLETO</th>
                    <th>FECHA DE NACIMIENTO</th>
                    <th>EDAD(a)</th>
                    <th>GENERO</th>
                    <th>SUPERFICIE CORPORAL (m2)</th>
                </tr>
            </thead>
           <tbody>
            <tr>
                <td>EDA BERNARDA SUAREZ RIOX</td>
                <td>20/ago/1967</td>
                <td>55</td>
                <td>F</td>
                <td>S/D</td>
            </tr>
           </tbody>

         </table>
         <table>
            <thead>
                <tr>
                    <th>DIAGNOSTICO</th>
                    <th></th>
                </tr>
            </thead>
         </table>
    </div>


</body>

</html>
