<x-admin-layout>

    <div class="flex flex-col items-center">
        <div class="mt-2 mb-4">
            <h1 class="text-2xl font-medium text-gray-800 text-center">SOLICITUD DE NUTRICIÓN PARENTERAL</h1>
        </div>
        <form action="{{ route('admin.solicitudes.store') }}" method="POST" class="bg-white rounded-lg p-6 shadow-lg">
            @csrf



            <div class="flex gap-4">
                <div class="mb-4 flex  items-baseline gap-2">
                    <x-label class="mb-2">
                        Paciente Nombre(s)*:
                    </x-label>
                    <div class="flex flex-col">
                        <x-input-solicitud value="{{ old('nombre_paciente') }}" name="nombre_paciente" class=""
                            placeholder="" />
                        <!-- Mensaje de error -->
                        @error('nombre_paciente')
                            <div class="text-red-500 text-sm">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="mb-4 flex items-baseline gap-2">
                    <x-label class="mb-2">
                        Paciente Apellidos*:
                    </x-label>
                    <div class="flex flex-col">
                        <x-input-solicitud value="{{ old('apellidos_paciente') }}" name="apellidos_paciente"
                            class="" placeholder="" />
                        <!-- Mensaje de error -->
                        @error('apellidos_paciente')
                            <div class="text-red-500 text-sm">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="mb-4 flex items-baseline gap-2">
                    <x-label class="mb-2">
                        Servicio*:
                    </x-label>
                    <div class="flex flex-col">
                        <x-input-solicitud value="{{ old('servicio') }}" name="servicio" class=""
                            placeholder="" />
                        <!-- Mensaje de error -->
                        @error('servicio')
                            <div class="text-red-500 text-sm">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="mb-4 flex items-baseline gap-2">
                    <x-label class="mb-2">
                        Cama:
                    </x-label>
                    <div class="flex flex-col">
                        <x-input-solicitud value="{{ old('cama') }}" name="cama" class="" placeholder="" />
                        <!-- Mensaje de error -->
                        @error('cama')
                            <div class="text-red-500 text-sm">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="mb-4 flex items-baseline gap-2">
                    <x-label class="mb-2">
                        Piso:
                    </x-label>
                    <div class="flex flex-col">
                        <x-input-solicitud value="{{ old('piso') }}" name="piso" class="" placeholder="" />
                        <!-- Mensaje de error -->
                        @error('piso')
                            <div class="text-red-500 text-sm">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="flex gap-4">
                <div class="mb-4 flex items-baseline gap-2">
                    <x-label class="mb-2">
                        Registro:
                    </x-label>
                    <x-input-solicitud value="{{ old('registro') }}" name="registro" class="" placeholder="" />
                    <!-- Mensaje de error -->
                    @error('registro')
                        <div class="text-red-500 text-sm">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-4 flex items-baseline gap-2">
                    <x-label class="mb-2">
                        Diagnóstico:
                    </x-label>
                    <x-input-solicitud value="{{ old('diagnostico') }}" name="diagnostico" class=""
                        placeholder="" />
                    <!-- Mensaje de error -->
                    @error('diagnostico')
                        <div class="text-red-500 text-sm">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="flex gap-4">
                <div class="mb-4 flex items-baseline gap-2">
                    <x-label class="mb-2">
                        Peso:
                    </x-label>
                    <div class="flex flex-col">
                        <x-input-solicitud type="number" value="{{ old('peso') }}" step="0.001" name="peso"
                            class="" placeholder="" />
                        <!-- Mensaje de error -->
                        @error('peso')
                            <div class="text-red-500 text-sm">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="mb-4 flex items-baseline gap-2">
                    <x-label class="mb-2">
                        Sexo:
                    </x-label>
                    <x-select class="" name="sexo">
                        <option value="Femenino" @if (old('sexo') == 'Femenino') selected @endif>Femenino</option>
                        <option value="Masculino" @if (old('sexo') == 'Masculino') selected @endif>Masculino</option>
                    </x-select>
                    <!-- Mensaje de error -->
                    @error('sexo')
                        <div class="text-red-500 text-sm">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-4 flex items-baseline gap-2">
                    <x-label class="mb-2">
                        Fecha de nacimiento*:
                    </x-label>
                    <div class="flex flex-col">
                        <x-input-solicitud type="date" value="{{ old('fecha_nacimiento') }}"
                            max="{{ date('Y-m-d') }}" name="fecha_nacimiento" class="" placeholder=""
                            onchange="calcularEdad(this.value)" />
                        <!-- Mensaje de error -->
                        @error('fecha_nacimiento')
                            <div class="text-red-500 text-sm">{{ $message }}</div>
                        @enderror
                    </div>
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
                    <!-- Mensaje de error -->
                    @error('via_administracion')
                        <div class="text-red-500 text-sm">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-4 flex items-baseline gap-2">
                    <x-label class="mb-2">
                        Tiempo de infusión (h):
                    </x-label>
                    <x-input-solicitud type="number" value="{{ old('tiempo_infusion_min') }}"
                        name="tiempo_infusion_min" class="" placeholder="" />
                    <!-- Mensaje de error -->
                    @error('tiempo_infusion_min')
                        <div class="text-red-500 text-sm">{{ $message }}</div>
                    @enderror
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
                        <!-- Mensaje de error -->
                        @error('sobrellenado_ml')
                            <div class="text-red-500 text-sm">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="mb-4 flex items-baseline gap-2">
                    <x-label class="mb-2">
                        Vol. Total* (mL):
                    </x-label>
                    <x-input-solicitud type="number" value="{{ old('volumen_total') }}" name="volumen_total"
                        step="0.0001" class="" placeholder="" />
                    <!-- Mensaje de error -->
                    @error('volumen_total')
                        <div class="text-red-500 text-sm">{{ $message }}</div>
                    @enderror
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
                    <!-- Mensaje de error -->
                    @error('npt')
                        <div class="text-red-500 text-sm">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <h2 class="mb-4">MACRONUTRIENTES:</h2>
            <hr>
            <div class="flex flex-row gap-4 items-center">
                <div>
                    <h3 class="mt-4">AMINOÁCIDOS</h3>

                    @foreach ($inputs as $input)
                        @if ($input->category_id == 1)
                            <div>
                                <div class="mb-4 flex items-baseline gap-2">
                                    <x-label class="mb-2">
                                        {{ $input->description }}
                                    </x-label>
                                    <div class="flex flex-col">

                                        <x-input-solicitud type="number"
                                            value="{{ old('i_' . $input->input_id . '_' . $input->unidad) }}"
                                            name="i_{{ $input->input_id }}_{{ $input->unidad }}"
                                            id="i_{{ $input->input_id }}_{{ $input->unidad }}" step="0.0001"
                                            placeholder="i_{{ $input->input_id }}_{{ $input->unidad }}" />
                                        <span>{{ $input->unidad }}</span>
                                        {{-- @error('i_' . $input->input_id . '_' . $input->unidad)
                                            <div class="text-red-500">{{ $message }}</div>
                                        @enderror --}}
                                    </div>
                                </div>

                            </div>
                        @endif
                    @endforeach
                    @foreach ($inputs as $input)
                        @if ($input->category_id == 8)
                            <div>
                                <div class="mb-4 flex items-baseline gap-2">
                                    <x-label class="mb-2">
                                        {{ $input->description }}
                                    </x-label>
                                    <div class="flex flex-col">
                                        <x-input-solicitud type="number"
                                            value="{{ old('i_' . $input->input_id . '_' . $input->unidad) }}"
                                            name="i_{{ $input->input_id }}_{{ $input->unidad }}"
                                            id="i_{{ $input->input_id }}_{{ $input->unidad }}" step="0.0001"
                                            placeholder="i_{{ $input->input_id }}_{{ $input->unidad }}" />
                                        <span>{{ $input->unidad }}</span>
                                        {{-- @error('i_' . $input->input_id . '_' . $input->unidad)
                                            <div class="text-red-500">{{ $message }}</div>
                                        @enderror --}}
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
                <div>
                    <h3>CARBOHIDRATOS:</h3>
                    @foreach ($inputs as $input)
                        @if ($input->category_id == 2)
                            <div>
                                <div class="mb-4 flex items-baseline gap-2">
                                    <x-label class="mb-2">
                                        {{ $input->description }}
                                    </x-label>
                                    <div class="flex flex-col">
                                        <x-input-solicitud type="number"
                                            value="{{ old('i_' . $input->input_id . '_' . $input->unidad) }}"
                                            name="i_{{ $input->input_id }}_{{ $input->unidad }}"
                                            id="i_{{ $input->input_id }}_{{ $input->unidad }}" step="0.0001"
                                            placeholder="i_{{ $input->input_id }}_{{ $input->unidad }}" />
                                        <span>{{ $input->unidad }}</span>
                                        {{-- @error('i_' . $input->input_id . '_' . $input->unidad)
                                            <div class="text-red-500">{{ $message }}</div>
                                        @enderror --}}
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                    <H3>LÍPIDOS:</H3>
                    <div>
                        @foreach ($inputs as $input)
                            @if ($input->category_id == 3)
                                <div>
                                    <div class="mb-4 flex items-baseline gap-2">
                                        <x-label class="mb-2">
                                            {{ $input->description }}
                                        </x-label>
                                        <div class="flex flex-col">
                                            <x-input-solicitud type="number"
                                                value="{{ old('i_' . $input->input_id . '_' . $input->unidad) }}"
                                                name="i_{{ $input->input_id }}_{{ $input->unidad }}"
                                                id="i_{{ $input->input_id }}_{{ $input->unidad }}" step="0.0001"
                                                class=""
                                                placeholder="i_{{ $input->input_id }}_{{ $input->unidad }}" />
                                            <span>{{ $input->unidad }}</span>
                                            {{-- @error('i_' . $input->input_id . '_' . $input->unidad)
                                                <div class="text-red-500">{{ $message }}</div>
                                            @enderror --}}
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
            <h2 class="mb-4">ELECTROLITOS</h2>
            <hr>
            <div class="flex flex-row gap-4 items-start mt-4">
                <div class="grid grid-cols-2 gap-4">
                    @foreach ($inputs as $input)
                        @if ($input->category_id == 4)
                            <div>
                                <div class="mb-4 flex items-baseline gap-2">
                                    <x-label class="mb-2">
                                        {{ $input->description }}
                                    </x-label>
                                    <div class="flex flex-col">
                                        <x-input-solicitud type="number"
                                            value="{{ old('i_' . $input->input_id . '_' . $input->unidad) }}"
                                            name="i_{{ $input->input_id }}_{{ $input->unidad }}"
                                            id="i_{{ $input->input_id }}_{{ $input->unidad }}" step="0.0001"
                                            placeholder="i_{{ $input->input_id }}_{{ $input->unidad }}" />
                                        <span>{{ $input->unidad }}</span>
                                        {{-- @error('i_' . $input->input_id . '_' . $input->unidad)
                                            <div class="text-red-500">{{ $message }}</div>
                                        @enderror --}}
                                    </div>
                                </div>

                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
            <h2 class="mb-4">ADITIVOS:</h2>
            <hr>
            <div class="flex flex-row gap-4 items-start mt-4">
                <div class="grid grid-cols-2 gap-4">
                    @foreach ($inputs as $input)
                        @if ($input->category_id == 5)
                            <div>
                                <div class="mb-4 flex items-baseline gap-2">
                                    <x-label class="mb-2">
                                        {{ $input->description }}
                                    </x-label>
                                    <div class="flex flex-col">
                                        <x-input-solicitud type="number"
                                            value="{{ old('i_' . $input->input_id . '_' . $input->unidad) }}"
                                            name="i_{{ $input->input_id }}_{{ $input->unidad }}"
                                            id="i_{{ $input->input_id }}_{{ $input->unidad }}" step="0.0001"
                                            placeholder="i_{{ $input->input_id }}_{{ $input->unidad }}" />
                                        <span>{{ $input->unidad }}</span>
                                        {{-- @error('i_' . $input->input_id . '_' . $input->unidad)
                                            <div class="text-red-500">{{ $message }}</div>
                                        @enderror --}}
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>

            </div>
            <div class="mb-4">
                <x-label class="mb-2">
                    OBSERVACIONES
                </x-label>
                <textarea class="border-2 border-solid w-full resize-x overflow-auto h-20" name="observaciones">{{ old('observaciones') }}</textarea>
                <!-- Mensaje de error -->
                @error('observaciones')
                    <div class="text-red-500 text-sm">{{ $message }}</div>
                @enderror
            </div>
            <div class="flex flex-row gap-4 items-start">
                <div>
                    <div>
                        <div class="mb-4 flex items-baseline gap-2">
                            <x-label class="mb-2">
                                Hora de entrega
                            </x-label>
                            <div class="flex flex-col">
                                <x-input-solicitud type="datetime-local" value="{{ old('fecha_hora_entrega') }}"
                                    min="{{ \Carbon\Carbon::now()->format('Y-m-d\TH:i') }}" name="fecha_hora_entrega"
                                    class="" placeholder="" />
                                <!-- Mensaje de error -->
                                @error('fecha_hora_entrega')
                                    <div class="text-red-500 text-sm">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
                <div>
                    <div>
                        <div class="mb-4 flex items-baseline gap-2">
                            <x-label class="mb-2">
                                NOMBRE DEL MÉDICO*:
                            </x-label>
                            <div class="flex flex-col">
                                <x-input-solicitud value="{{ old('nombre_medico') }}" name="nombre_medico"
                                    class="" placeholder="" />
                                <!-- Mensaje de error -->
                                @error('nombre_medico')
                                    <div class="text-red-500 text-sm">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div>
                        <div class="mb-4 flex items-baseline gap-2">
                            <x-label class="mb-2">
                                CÉDULA*:
                            </x-label>
                            <div class="flex flex-col">
                                <x-input-solicitud value="{{ old('cedula') }}" name="cedula" class=""
                                    placeholder="" />
                                <!-- Mensaje de error -->
                                @error('cedula')
                                    <div class="text-red-500 text-sm">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex justify-end gap-5">
                <input type="checkbox" name="terminos" id="terminos"> Enviar de todas formas
                <x-button>
                    GUARDAR SOLICITUD
                </x-button>
                {{-- <x-button id="enviarSolicitud">
                    GUARDAR SOLICITUD SIN VALIDAR
                </x-button> --}}
            </div>

        </form>
        {{-- <!-- Modal -->
        <div id="termsModal" class="modal">
            <div class="modal-content">
                <h4>Aceptación de Términos</h4>
                <p>Por favor, lee y acepta los términos antes de enviar la solicitud.</p>
                <div class="modal-footer">
                    <button id="acceptTerms" class="waves-effect waves-light btn">Aceptar</button>
                </div>
            </div>
        </div> --}}

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


            // Obtener todos los elementos con la clase 'numeric-input'
            const numericInputs = document.querySelectorAll('.numeric-input');

            // Iterar sobre cada elemento y agregar un listener de evento 'input'
            numericInputs.forEach(input => {
                input.addEventListener('input', function(event) {
                    // Obtener el valor actual del campo de entrada
                    let inputValue = this.value;
                    // Reemplazar todos los caracteres que no son números
                    this.value = inputValue.replace(/\D/g, '');
                });
            });

            // Funcion que bloquea un campo si escribimos en otro especificamente
            // document.addEventListener("DOMContentLoaded", function() {
            //     var mct_lct = document.getElementById("i_9");
            //     var smof = document.getElementById("i_10");

            //     mct_lct.addEventListener("input", function() {
            //         if (mct_lct.value.trim() !== "") {
            //             smof.disabled = true;
            //         } else {
            //             smof.disabled = false;
            //         }
            //     });

            //     smof.addEventListener("input", function() {
            //         if (smof.value.trim() !== "") {
            //             mct_lct.disabled = true;
            //         } else {
            //             mct_lct.disabled = false;
            //         }
            //     });
            // });
        </script>
    @endpush
</x-admin-layout>
