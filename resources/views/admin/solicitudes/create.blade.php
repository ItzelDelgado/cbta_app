<x-admin-layout>

    <div class="flex flex-col items-center">
        <div class="mt-2 mb-4">
            <h1 class="text-2xl font-medium text-gray-800 text-center">SOLICITUD DE NUTRICIÓN PARENTERAL</h1>
        </div>
        <form action="{{ route('admin.solicitudes.store') }}" method="POST" class="bg-white rounded-lg p-6 shadow-lg">
            @csrf

            <x-validation-errors class="mb-4" />

            <div class="flex gap-4">
                <div class="mb-4 flex items-baseline gap-2">
                    <x-label class="mb-2">
                        Paciente*:
                    </x-label>
                    <x-input-solicitud value="{{ old('nombre_paciente') }}" name="nombre_paciente" class=""
                        placeholder="" />
                </div>
                <div class="mb-4 flex items-baseline gap-2">
                    <x-label class="mb-2">
                        Servicio*:
                    </x-label>
                    <x-input-solicitud value="{{ old('servicio') }}" name="servicio" class="" placeholder="" />
                </div>
                <div class="mb-4 flex items-baseline gap-2">
                    <x-label class="mb-2">
                        Cama:
                    </x-label>
                    <x-input-solicitud value="{{ old('cama') }}" name="cama" class="" placeholder="" />
                </div>
                <div class="mb-4 flex items-baseline gap-2">
                    <x-label class="mb-2">
                        Piso:
                    </x-label>
                    <x-input-solicitud value="{{ old('piso') }}" name="piso" class="" placeholder="" />
                </div>
            </div>
            <div class="flex gap-4">
                <div class="mb-4 flex items-baseline gap-2">
                    <x-label class="mb-2">
                        Registro:
                    </x-label>
                    <x-input-solicitud value="{{ old('registro') }}" name="registro" class="" placeholder="" />
                </div>
                <div class="mb-4 flex items-baseline gap-2">
                    <x-label class="mb-2">
                        Diagnóstico:
                    </x-label>
                    <x-input-solicitud value="{{ old('diagnostico') }}" name="diagnostico" class=""
                        placeholder="" />
                </div>
            </div>
            <div class="flex gap-4">
                <div class="mb-4 flex items-baseline gap-2">
                    <x-label class="mb-2">
                        Peso:
                    </x-label>
                    <x-input-solicitud type="number" value="{{ old('peso') }}" step="0.001" name="peso"
                        class="" placeholder="" />
                </div>
                <div class="mb-4 flex items-baseline gap-2">
                    <x-label class="mb-2">
                        Sexo:
                    </x-label>
                    <x-select class="" name="sexo">
                        <option value="Femenino" @if (old('sexo') == 'Femenino') selected @endif>Femenino</option>
                        <option value="Masculino" @if (old('sexo') == 'Masculino') selected @endif>Masculino</option>
                    </x-select>
                </div>
                <div class="mb-4 flex items-baseline gap-2">
                    <x-label class="mb-2">
                        Fecha de nacimiento*:
                    </x-label>
                    <x-input-solicitud type="date" value="{{ old('fecha_nacimiento') }}" max="{{ date('Y-m-d') }}"
                        name="fecha_nacimiento" class="" placeholder="" onchange="calcularEdad(this.value)" />
                </div>
                {{-- <div id="edad"></div> --}}

            </div>
            <div class="flex gap-4">
                <div class="mb-4 flex items-baseline gap-2">
                    <x-label class="mb-2">
                        Vía de administración*:
                    </x-label>
                    <x-select class="" name="via_administracion">

                        <option value="Central" @if (old('via_administracion') == 'Central') selected @endif>Central</option>
                        <option value="Periférica" @if (old('via_administracion') == 'Periférica') selected @endif>Periférica</option>
                    </x-select>
                </div>
                <div class="mb-4 flex items-baseline gap-2">
                    <x-label class="mb-2">
                        Tiempo de infusión (h):
                    </x-label>
                    <x-input-solicitud type="number" value="{{ old('tiempo_infusion_min') }}" name="tiempo_infusion_min"
                        class="" placeholder="" />
                </div>
            </div>
            <div class="flex gap-4">
                <div>
                    <div class="mb-4 flex items-baseline gap-2">
                        <x-label class="mb-2">
                            Sobrellenado* (mL):
                        </x-label>
                        <x-input-solicitud type="number" value="{{ old('sobrellenado_ml') }}" step="0.0001"
                            name="sobrellenado_ml" class="" placeholder="" />
                    </div>
                </div>
                <div class="mb-4 flex items-baseline gap-2">
                    <x-label class="mb-2">
                        Vol. Total* (mL):
                    </x-label>
                    <x-input-solicitud type="number" value="{{ old('volumen_total') }}" name="volumen_total"
                        step="0.0001" class="" placeholder="" />
                </div>
                <div class="mb-4 flex items-baseline gap-2">
                    <x-label class="mb-2">
                        NPT*:
                    </x-label>
                    <x-select class="" name="npt">

                        <option value="RNPT" @if (old('npt') == 'RNPT') selected @endif>RNPT</option>
                        <option value="LACT" @if (old('npt') == 'LACT') selected @endif>LACT</option>
                        <option value="INF" @if (old('npt') == 'INF') selected @endif>INF</option>
                        <option value="ADOL" @if (old('npt') == 'ADOL') selected @endif>ADOL</option>
                        <option value="ADULT" @if (old('npt') == 'ADULT') selected @endif>ADULT</option>
                    </x-select>
                </div>
            </div>

            <h2 class="mb-4">MACRONUTRIENTES:</h2>
            <hr>
            <div class="flex flex-row gap-4 items-center">
                <div>
                    <h3 class="mt-4">AMINOÁCIDOS</h3>
                    <div>
                        <div class="mb-4 flex items-baseline gap-2">
                            <x-label class="mb-2">
                                Aminoácidos Adulto 10%
                            </x-label>
                            <x-input-solicitud type="number" value="{{ old('i_4') }}" name="i_4"
                                step="0.0001" class="" placeholder="" /><span>g/Kg</span>
                        </div>
                    </div>
                    <div>
                        <div class="mb-4 flex items-baseline gap-2">
                            <x-label class="mb-2">
                                Aminoácidos pediátricos 10%
                            </x-label>
                            <x-input-solicitud type="number" value="{{ old('i_5') }}" name="i_5"
                                step="0.0001" class="" placeholder="" /><span>g/Kg</span>
                        </div>
                    </div>
                    <div>
                        <div class="mb-4 flex items-baseline gap-2">
                            <x-label class="mb-2">
                                Aminoácidos de cadena ramificada 8%
                            </x-label>
                            <x-input-solicitud type="number" value="{{ old('i_6') }}" name="i_6"
                                step="0.0001" class="" placeholder="" /><span>g/Kg</span>
                        </div>
                    </div>
                    <div>
                        <div class="mb-4 flex items-baseline gap-2">
                            <x-label class="mb-2">
                                Aminoácidos esenciales 5.4%
                            </x-label>
                            <x-input-solicitud type="number" value="{{ old('i_7') }}" name="i_7"
                                step="0.0001" class="" placeholder="" /><span>g/Kg</span>
                        </div>
                    </div>
                </div>
                <div>
                    <h3>CARBOHIDRATOS:</h3>
                    <div>
                        <div>
                            <div class="mb-4 flex items-baseline gap-2">
                                <x-label class="mb-2">
                                    Dextrosa 50%
                                </x-label>
                                <x-input-solicitud type="number" value="{{ old('i_8') }}" name="i_8"
                                    step="0.0001" class="" placeholder="" /><span>g/Kg</span>
                            </div>
                        </div>
                    </div>
                    <H3>LÍPIDOS:</H3>
                    <div>
                        <div>
                            <div class="mb-4 flex items-baseline gap-2">
                                <x-label class="mb-2">
                                    MCT/LCT 20%
                                </x-label>
                                <x-input-solicitud type="number" value="{{ old('i_9') }}" name="i_9"
                                    step="0.0001" class="" placeholder="" /><span>g/Kg</span>
                            </div>
                        </div>
                        <div>
                            <div class="mb-4 flex items-baseline gap-2">
                                <x-label class="mb-2">
                                    SMOF 20%
                                </x-label>
                                <x-input-solicitud type="number" value="{{ old('i_10') }}" name="i_10"
                                    step="0.0001" class="" placeholder="" /><span>g/Kg</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <h2 class="mb-4">ELECTROLITOS</h2>
            <hr>
            <div class="flex flex-row gap-4 items-start mt-4">
                <div>
                    <div>
                        <div class="mb-4 flex items-baseline gap-2">
                            <x-label class="mb-2">
                                Cloruro de Sodio (3 mEq/mL Na)
                            </x-label>
                            <x-input-solicitud type="number" value="{{ old('i_11') }}" name="i_11"
                                step="0.0001" class="" placeholder="" /><span>mEq/Kg</span>
                        </div>
                    </div>
                    <div>
                        <div class="mb-4 flex items-baseline gap-2">
                            <x-label class="mb-2">
                                Acetato de sodio (4 mEq/mL)
                            </x-label>
                            <x-input-solicitud type="number" value="{{ old('i_12') }}" name="i_12"
                                step="0.0001" class="" placeholder="" /><span>mEq/Kg</span>
                        </div>
                    </div>
                    <div>
                        <div class="mb-4 flex items-baseline gap-2">
                            <x-label class="mb-2">
                                Fosfato de sodio (4 mEq/mL)
                            </x-label>
                            <x-input-solicitud type="number" value="{{ old('i_13') }}" name="i_13"
                                step="0.0001" class="" placeholder="" /><span>mEq/Kg</span>
                        </div>
                    </div>
                    <div>
                        <div class="mb-4 flex items-baseline gap-2">
                            <x-label class="mb-2">
                                Sulfato de Magnesio (0.81mEq/mL)
                            </x-label>
                            <x-input-solicitud type="number" value="{{ old('i_14') }}" name="i_14"
                                step="0.0001" class="" placeholder="" /><span>mEq/Kg</span>
                        </div>
                    </div>
                </div>
                <div>
                    <div>
                        <div class="mb-4 flex items-baseline gap-2">
                            <x-label class="mb-2">
                                Cloruro de Potasio (4 mEq/mL)
                            </x-label>
                            <x-input-solicitud type="number" value="{{ old('i_15') }}" name="i_15"
                                step="0.0001" class="" placeholder="" /><span>mEq/Kg</span>
                        </div>
                    </div>
                    <div>
                        <div class="mb-4 flex items-baseline gap-2">
                            <x-label class="mb-2">
                                Acetato de Potasio (2mEq/mL)
                            </x-label>
                            <x-input-solicitud type="number" value="{{ old('i_16') }}" name="i_16"
                                step="0.0001" class="" placeholder="" /><span>mEq/Kg</span>
                        </div>
                    </div>
                    <div>
                        <div class="mb-4 flex items-baseline gap-2">
                            <x-label class="mb-2">
                                Fosfato de Potasio (2mEq/mL)
                            </x-label>
                            <x-input-solicitud type="number" value="{{ old('i_17') }}" name="i_17"
                                step="0.0001" class="" placeholder="" /><span>mEq/Kg</span>
                        </div>
                    </div>
                    <div>
                        <div class="mb-4 flex items-baseline gap-2">
                            <x-label class="mb-2">
                                Gluconato de Calcio (0.465 mEq/mL)
                            </x-label>
                            <x-input-solicitud type="number" value="{{ old('i_18') }}" name="i_18"
                                step="0.0001" class="" placeholder="" /><span>mEq/Kg</span>
                        </div>
                    </div>
                </div>
            </div>
            <h2 class="mb-4">ADITIVOS:</h2>
            <hr>
            <div class="flex flex-row gap-4 items-start mt-4">
                <div>
                    <div>
                        <div class="mb-4 flex items-baseline gap-2">
                            <x-label class="mb-2">
                                Ácidos Grasos Omega 3 10%
                            </x-label>
                            <x-input-solicitud type="number" value="{{ old('i_19') }}" name="i_19"
                                step="0.0001" class="" placeholder="" /><span>mL</span>
                        </div>
                    </div>
                    <div>
                        <div class="mb-4 flex items-baseline gap-2">
                            <x-label class="mb-2">
                                Albúmina 25% (0.25 g/mL)
                            </x-label>
                            <x-input-solicitud type="number" value="{{ old('i_20') }}" name="i_20"
                                step="0.0001" class="" placeholder="" /><span>g</span>
                        </div>
                    </div>
                    <div>
                        <div class="mb-4 flex items-baseline gap-2">
                            <x-label class="mb-2">
                                Albúmina 20% (0.20 g/mL)
                            </x-label>
                            <x-input-solicitud type="number" value="{{ old('i_21') }}" name="i_21"
                                step="0.0001" class="" placeholder="" /><span>g</span>
                        </div>
                    </div>
                    <div>
                        <div class="mb-4 flex items-baseline gap-2">
                            <x-label class="mb-2">
                                Glutamina 20%
                            </x-label>
                            <x-input-solicitud type="number" value="{{ old('i_22') }}" name="i_22"
                                step="0.0001" class="" placeholder="" /><span>g</span>
                        </div>
                    </div>
                    <div>
                        <div class="mb-4 flex items-baseline gap-2">
                            <x-label class="mb-2">
                                Cromo (4 mcg/mL)
                            </x-label>
                            <x-input-solicitud type="number" value="{{ old('i_23') }}" name="i_23"
                                step="0.0001" class="" placeholder="" /><span>mcg</span>
                        </div>
                    </div>
                    <div>
                        <div class="mb-4 flex items-baseline gap-2">
                            <x-label class="mb-2">
                                Heparina (1000 UI/mL)
                            </x-label>
                            <x-input-solicitud type="number" value="{{ old('i_24') }}" name="i_24"
                                step="0.0001" class="" placeholder="" /><span>UI</span>
                        </div>
                    </div>
                    <div>
                        <div class="mb-4 flex items-baseline gap-2">
                            <x-label class="mb-2">
                                L-Carnitina (200 mg/mL)
                            </x-label>
                            <x-input-solicitud type="number" value="{{ old('i_25') }}" name="i_25"
                                step="0.0001" class="" placeholder="" /><span>mg</span>
                        </div>
                    </div>
                    <div>
                        <div class="mb-4 flex items-baseline gap-2">
                            <x-label class="mb-2">
                                Insulina (100 UI/mL)
                            </x-label>
                            <x-input-solicitud type="number" value="{{ old('i_26') }}" name="i_26"
                                step="0.0001" class="" placeholder="" /><span>UI</span>
                        </div>
                    </div>
                </div>
                <div>
                    <div>
                        <div class="mb-4 flex items-baseline gap-2">
                            <x-label class="mb-2">
                                Manganeso (100 mcg/mL)
                            </x-label>
                            <x-input-solicitud type="number" value="{{ old('i_27') }}" name="i_27"
                                step="0.0001" class="" placeholder="" /><span>mcg</span>
                        </div>
                    </div>
                    <div>
                        <div class="mb-4 flex items-baseline gap-2">
                            <x-label class="mb-2">
                                MVI
                            </x-label>
                            <x-input-solicitud type="number" value="{{ old('i_28') }}" name="i_28"
                                step="0.0001" class="" placeholder="" /><span>mL</span>
                        </div>
                    </div>
                    <div>
                        <div class="mb-4 flex items-baseline gap-2">
                            <x-label class="mb-2">
                                Oligoelementos
                            </x-label>
                            <x-input-solicitud type="number" value="{{ old('i_29') }}" name="i_29"
                                step="0.0001" class="" placeholder="" /><span>mL</span>
                        </div>
                    </div>
                    <div>
                        <div class="mb-4 flex items-baseline gap-2">
                            <x-label class="mb-2">
                                Ácido Folínico (12.5 mg/mL)
                            </x-label>
                            <x-input-solicitud type="number" value="{{ old('i_30') }}" name="i_30"
                                step="0.0001" class="" placeholder="" /><span>mg</span>
                        </div>
                    </div>
                    <div>
                        <div class="mb-4 flex items-baseline gap-2">
                            <x-label class="mb-2">
                                Selenio (40 mcg/mL)
                            </x-label>
                            <x-input-solicitud type="number" value="{{ old('i_31') }}" name="i_31"
                                step="0.0001" class="" placeholder="" /><span>mcg</span>
                        </div>
                    </div>
                    <div>
                        <div class="mb-4 flex items-baseline gap-2">
                            <x-label class="mb-2">
                                Vitamina C (100 mg/mL)
                            </x-label>
                            <x-input-solicitud type="number" value="{{ old('i_32') }}" name="i_32"
                                step="0.0001" class="" placeholder="" /><span>mg</span>
                        </div>
                    </div>
                    <div>
                        <div class="mb-4 flex items-baseline gap-2">
                            <x-label class="mb-2">
                                Vitamina K (10 mg/mL)
                            </x-label>
                            <x-input-solicitud type="number" value="{{ old('i_33') }}" name="i_33"
                                step="0.0001" class="" placeholder="" /><span>mg</span>
                        </div>
                    </div>
                    <div>
                        <div class="mb-4 flex items-baseline gap-2">
                            <x-label class="mb-2">
                                Zinc (1 mg/mL)
                            </x-label>
                            <x-input-solicitud type="number" value="{{ old('i_34') }}" name="i_34"
                                step="0.0001" class="" placeholder="" /><span>mg</span>
                        </div>
                    </div>
                    <div>
                        <div class="mb-4 flex items-baseline gap-2">
                            <x-label class="mb-2">
                                L-Cisteina (50mg/mL)
                            </x-label>
                            <x-input-solicitud type="number" value="{{ old('i_35') }}" name="i_35"
                                step="0.0001" class="" placeholder="" /><span>mg</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mb-4">
                <x-label class="mb-2">
                    OBSERVACIONES
                </x-label>
                <textarea class="border-2 border-solid w-full resize-x overflow-auto h-20" value="{{ old('observaciones') }}"
                    name="observaciones" id=""></textarea>
            </div>
            <div class="flex flex-row gap-4 items-start">
                <div>
                    <div>
                        <div class="mb-4 flex items-baseline gap-2">
                            <x-label class="mb-2">
                                Fecha de entrega:
                            </x-label>
                            <x-input-solicitud type="date" value="{{ old('fecha_entrega') }}"
                                name="fecha_entrega" class="" placeholder="" />
                        </div>
                    </div>
                    <div>
                        <div class="mb-4 flex items-baseline gap-2">
                            <x-label class="mb-2">
                                Hora de entrega
                            </x-label>
                            <x-input-solicitud type="time" value="{{ old('hora_entrega') }}" name="hora_entrega"
                                id="hora_entrega" class="" placeholder="" />
                        </div>
                    </div>
                </div>
                <div>
                    <div>
                        <div class="mb-4 flex items-baseline gap-2">
                            <x-label class="mb-2">
                                NOMBRE DEL MÉDICO*:
                            </x-label>
                            <x-input-solicitud value="{{ old('nombre_medico') }}" name="nombre_medico"
                                class="" placeholder="" />
                        </div>
                    </div>
                    <div>
                        <div class="mb-4 flex items-baseline gap-2">
                            <x-label class="mb-2">
                                CÉDULA*:
                            </x-label>
                            <x-input-solicitud value="{{ old('cedula') }}" name="cedula" class=""
                                placeholder="" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex justify-end">
                <x-button>
                    ENVIAR SOLICITUD
                </x-button>
            </div>
        </form>
    </div>



    @push('js')
        <script>
            function calcularEdad(fechaNacimiento) {
                var fechaNacimiento = new Date(fechaNacimiento);
                var fechaActual = new Date();

                var edadAnios = fechaActual.getFullYear() - fechaNacimiento.getFullYear();
                var edadMeses = fechaActual.getMonth() - fechaNacimiento.getMonth();
                var edadDias = fechaActual.getDate() - fechaNacimiento.getDate();

                if (edadDias < 0) {
                    edadMeses--;
                    var ultimoDiaMesAnterior = new Date(fechaActual.getFullYear(), fechaActual.getMonth(), 0).getDate();
                    edadDias = ultimoDiaMesAnterior + edadDias;
                }

                if (edadMeses < 0) {
                    edadAnios--;
                    edadMeses = 12 + edadMeses;
                }

                var edad = '';

                if (edadAnios > 0) {
                    edad += edadAnios + ' año(s) ';
                }

                if (edadMeses > 0) {
                    edad += edadMeses + ' mes(es) ';
                }

                if (edadDias > 0) {
                    edad += edadDias + ' día(s)';
                }

                //document.getElementById('edad').innerHTML = 'Edad: ' + edad;
                console.log('Edad: ' + edad);
            }


            document.addEventListener('DOMContentLoaded', function() {
                var inputHoraEntrega = document.getElementById('hora_entrega');

                // Escuchar el evento 'change' del campo de entrada
                inputHoraEntrega.addEventListener('change', function() {
                    // Obtener el valor del campo de entrada
                    var hora = this.value;

                    // Convertir la hora al formato de 24 horas
                    var hora24h = convertirHoraA24(hora);

                    // Establecer el valor convertido en el campo de entrada
                    this.value = hora24h;
                });

                // Función para convertir la hora al formato de 24 horas
                function convertirHoraA24(hora12h) {
                    var partes = hora12h.split(':'); // Dividir la hora en horas y minutos
                    var horas = parseInt(partes[0]); // Convertir las horas a un número entero

                    // Si el sufijo es 'p.m.' y las horas no son 12, sumar 12 para convertir a formato de 24 horas
                    if (hora12h.includes('p.m.') && horas !== 12) {
                        horas += 12;
                    }
                    // Si el sufijo es 'a.m.' y las horas son 12, establecer las horas a 0 para convertir a formato de 24 horas
                    else if (hora12h.includes('a.m.') && horas === 12) {
                        horas = 0;
                    }

                    // Formatear la hora como 'HH:mm' (formato de 24 horas)
                    var hora24h = horas.toString().padStart(2, '0') + ':' + partes[1];

                    return hora24h;
                }
            });
        </script>
    @endpush
</x-admin-layout>
