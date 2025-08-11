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

        .bg-cbta {
            background-color: #1F4E78;
            color: white;
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
            font-size: 11px
        }

        .border-1 {
            border: 1px solid black;
        }

        .border-x-1 {
            border-left: 1px solid black;
            border-right: 1px solid black;
        }

        .border-y-1 {
            border-top: 1px solid black;
            border-bottom: 1px solid black;
        }

        .border-l-0 {
            border-left: none;
        }

        .border-r-0 {
            border-right: none;
        }

        .mx-1 {
            margin-left: 0.25rem;
            margin-right: 0.5rem;
        }

        .mt-8 {
            margin-top: 2rem;
        }

        .mt-12 {
            margin-top: 3rem;
        }

        .mt-2 {
            margin-top: 0.5rem;
        }

        .mt-4 {
            margin-top: 1rem;
        }

        .mb-4 {
            margin-bottom: 1rem;
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

        .font-bold {
            font-weight: bold;
        }

        .text-center {
            text-align: center
        }
    </style>


<body>
    <div class="contenedor border-1">
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
                <td style="text-align: center;">ORDEN DE PREPARACIÓN DE NUTRICIÓN PARENTERAL TOTAL</td>
            </tr>
        </table>
        <table class="mx-1">
            <tr>
                <td></td>
                <td>Fecha de elaboración</td>
            </tr>
            <tr>
                <td><strong>LIBERACIÓN DE ÁREA</strong></td>
                <td></td>
            </tr>
        </table>
        <div class="border-1 mx-1 px-1 py-1">
            <table>
                <tr>
                    <td style="width: 80%"></td>
                    <td style="width: 10%" class="border-1 text-center">Si</td>
                    <td style="width: 10%" class="border-1 text-center">No</td>
                </tr>
                <tr>
                    <td>¿El personal encargado de la preparación porta el uniforme correspondiente a la actividad a
                        realizar?</td>
                    <td class="border-1"></td>
                    <td class="border-1"></td>
                </tr>
                <tr>
                    <td>¿El área de cuarto de mezclas y aislador se encuentran limpios?</td>
                    <td class="border-1"></td>
                    <td class="border-1"></td>
                </tr>
                <tr>
                    <td>¿La zona de paso de material y de trabajo del aislador se encuentra libre de material?</td>
                    <td class="border-1"></td>
                    <td class="border-1"></td>
                </tr>
                <tr>
                    <td>¿El cuarto de mezclas se encuentra libre de material y documentos ajenos a la mezcla en turno a
                        preparar?</td>
                    <td class="border-1"></td>
                    <td class="border-1"></td>
                </tr>
                <tr>
                    <td><strong>¿Se considera liberada el área para la preparación de la mezcla?</strong></td>
                    <td class="border-1"></td>
                    <td class="border-1"></td>
                </tr>
            </table>
            <table>
                <tr>
                    <td style="width: 5%">Sanitizante:</td>
                    <td style="width: 5%" class="border-1"></td>
                    <td style="width: 15%">Alcohol Isoopropílico</td>
                    <td style="width: 5%" class="border-1"></td>
                    <td style="width: 10%">Critical 0.2%</td>
                    <td style="width: 60%"></td>
                </tr>
            </table>
            <table>
                <tr>
                    <td style="width: 50%"></td>
                    <td style="width: 50%"></td>
                </tr>
            </table>
            <table>
                <td style="width: 20%">Selecciona con una ü lo que corresponda.</td>
                <td style="width: 35%"></td>
                <td style="width: 45%">Verifico requerimientos y liberó el área (nombre y firma)</td>
            </table>
        </div>

        <table class="mx-1">
            <tr>
                <td><strong>No. de Lote de la mezcla:</strong></td>
                <td><strong>No. de orden de preparación:</strong></td>
            </tr>
        </table>
        <table class="mx-1 mt-2">
            <tr>
                <td><strong>DATOS DEL PACIENTE</strong></td>
            </tr>
        </table>
        <div class="border-1 mx-1 px-1 py-1">
            <table>
                <tr>
                    <td style="width: 5%">Nombre: </td>
                    <td style="width: 40%" class="border-b-1">{{ $mezcla->solicitud['nombre_paciente'] }}</td>
                    <td style="width: 11.5%">Médico tratante:</td>
                    <td style="width: 43%" class="border-b-1">{{ $mezcla->solicitud['nombre_medico'] }}</td>
                </tr>
            </table>
            <table>
                <tr>
                    <td style="width: 5%">Registro:</td>
                    <td style="width: 30%" class="border-b-1">{{ $mezcla->solicitud['registro_paciente'] }}</td>
                    <td style="width: 5%">Diagnostico:</td>
                    <td style="" class="border-b-1">{{ $mezcla->solicitud['diagnostico'] }}</td>
                </tr>
            </table>
            <table>
                <tr>
                    <td style="width: 15.4%">Fecha de nacimiento:</td>
                    <td class="border-b-1">{{ $mezcla->solicitud['fecha_nacimiento'] }}</td>
                    <td style="width: 5%">Genero:</td>
                    <td class="border-b-1">{{ $mezcla->solicitud['sexo'] }}</td>
                    <td style="width: 4%">Peso:</td>
                    <td class="border-b-1">{{ $mezcla->solicitud['peso'] }} Kg</td>
                </tr>
            </table>
            <table>
                <tr>
                    <td style="width: 15%">Sitio de procedencia:</td>
                    <td class="border-b-1"></td>
                    <td style="width: 10%">Dosis/Volumen:</td>
                    <td class="border-b-1">{{$mezcla->volumen_dilucion}} ML</td>
                </tr>
            </table>
        </div>
        <p class="mx-1"><strong>MEZCLA ESTERIL ONCOLÓGICA </strong></p>
        <div class="border-1 mx-1 px-1 py-1">
            <table>
                <tr>
                    <td colspan="1">Hora de preparación:</td>
                    <td colspan="2" class="border-b-1">
                        {{ $fecha_preparacion ? \Carbon\Carbon::parse($fecha_preparacion)->format('H:i') : '—' }}
                    </td>
                    <td colspan="5"></td>
                </tr>
            </table>
            <table>
                <tr>
                    <td style="width: 40%"></td>
                    <td style="width: 20%" class="text-center"><strong>Medicamentos</strong></td>
                    <td style="width: 40%"></td>
                </tr>
            </table>


            <table>
                <tr>
                    <td class="border-1 text-center" style="width: 1%"><strong></strong></td>
                    <td class="border-1 text-center" style="width: 5%"><strong>No. de lote</strong></td>
                    <td class="border-1 text-center" style="width: 6%"><strong>Caducidad</strong></td>
                    <td class="border-1 text-center" style="width: 10%"><strong>Denominación comercial</strong></td>
                    <td class="border-1 text-center" style="width: 10%"><strong>Denominación genérica</strong></td>
                    <td class="border-1 text-center" style="width: 7%"><strong>Presentación</strong></td>
                    <td class="border-1 text-center" style="width: 5%"><strong>Dosis</strong></td>
                    <td class="border-1 text-center" style="width: 5%"><strong>Volumen</strong></td>
                </tr>

                @foreach ($medicamentos as $i => $med)
                    <tr>
                        <td class="border-1 border-t-0 text-center"><strong>{{ $i + 1 }}</strong></td>
                        <td class="border-1 border-t-0 text-center">{{ $med->lote ?? '—' }}</td>
                        <td class="border-1 border-t-0 text-center">
                            {{ $med->caducidad ? \Carbon\Carbon::parse($med->caducidad)->format('d/m/Y') : '—' }}</td>
                        <td class="border-1 border-t-0 text-center">{{ $med->denominacion_comercial ?? '—' }}</td>
                        <td class="border-1 border-t-0 text-center">{{ $med->nombre_medicamento ?? '—' }}</td>
                        <td class="border-1 border-t-0 text-center">{{ $med->presentacion ?? '—' }}</td>
                        <td class="border-1 border-t-0 text-center">{{ $med->dosis ?? '—' }}</td>
                        <td class="border-1 border-t-0 text-center">{{ $med->precio_unitario ?? '—' }}</td>
                    </tr>
                @endforeach
            </table>

            <table>
                <tr>
                    <td colspan="6"></td>
                    <td colspan="1">Contenedor y volumen final: {{$mezcla->volumen_dilucion}} ML</td>
                    <td colspan="1"></td>
                </tr>
                <tr>
                    <td colspan="8" style="text-align: center"><strong>Equipo de infusión/Infusor</strong></td>
                </tr>
                <tr>
                    <td rowspan="2" class="border-1 text-center"><strong>1</strong></td>
                    <td colspan="1" class="border-1 text-center"><strong>No. de lote</strong></td>
                    <td colspan="1" class="border-1 text-center"><strong>Caducidad</strong></td>
                    <td colspan="2" class="border-1 text-center"><strong>Nombre comercial</strong></td>
                    <td colspan="3" class="border-1 text-center"><strong>Nombre genérico</strong></td>
                </tr>
                <tr>
                    <td colspan="1" class="border-1 text-center"></td>
                    <td colspan="1" class="border-1 text-center"></td>
                    <td colspan="2" class="border-1 text-center"></td>
                    <td colspan="3" class="border-1 text-center"></td>
                </tr>
            </table>


            <table>
                <tr>
                    <td>Cálculos y forma de preparación:</td>
                </tr>
            </table>
            <table>
                <tr>
                    <td style="width: 5%">Reconstituir:</td>
                    <td class="border-b-1"></td>
                    <td></td>
                </tr>
            </table>

            <table>
                <tr>
                    <td style="width: 5%">Extraer:</td>
                    <td class="border-b-1" style="width: 10%"></td>
                    <td style="width: 30%">Dx 5% / CS 0.9% / SH / Agua inyectable</td>
                    <td style="width: 15%"></td>
                    <td style="width: 15%">Dx=5% dextrosa 5%</td>
                    <td></td>
                    <td style="width: 15%">SH=sln. Hartman</td>
                    <td></td>
                </tr>
            </table>
            <table>
                <tr>
                    <td style="width: 5%">Agregar:</td>
                    <td class="border-b-1"></td>
                    <td style="width: 5%"></td>
                    <td style="width: 20%">CS=Cloruro de sodio 0.9%</td>
                    <td></td>
                </tr>

            </table>
            <table>
                <tr>
                    <td style="width: 5%">Cálculos:</td>
                    <td style="width: 10%" class="border-b-1"></td>
                    <td style="width: 6%">UL mg /</td>
                    <td style="width: 10%" class="border-b-1"></td>
                    <td style="width: 3%">ml = </td>
                    <td style="width: 10%" class="border-b-1"></td>
                    <td style="width: 50%"></td>
                </tr>
                {{-- <tr>
                <td>Selecciona encerrando en un circulo la solución y las unidades correspondientes.</td>
                <td></td>
            </tr> --}}
            </table>
        </div>
        <table class="mt-8">
            <tr>
                <td style="width: 5%"></td>
                <td style="width: 26.66%" class="border-b-1"></td>
                <td style="width: 5%"></td>
                <td style="width: 26.66%" class="border-b-1"></td>
                <td style="width: 5%"></td>
                <td style="width: 26.66%" class="border-b-1"></td>
                <td style="width: 5%"></td>
            </tr>
            <tr>
                <td style="width: 5%"></td>
                <td style="width: 26.66%" class="text-center">Elaboró</td>
                <td style="width: 5%"></td>
                <td style="width: 26.66%" class="text-center">Validó</td>
                <td style="width: 5%"></td>
                <td style="width: 26.66%" class="text-center">Preparó</td>
                <td style="width: 5%"></td>
            </tr>
            <tr>
                <td></td>
                <td class="text-center">Nombre/sello y forma</td>
                <td></td>
                <td class="text-center">Nombre/sello y firma</td>
                <td></td>
                <td class="text-center">Nombre/sello y firma</td>
                <td></td>
            </tr>
        </table>

    </div>




</body>

</html>
