@php
    // Incluye manualmente el archivo helpers.php
    require_once app_path('Helpers/helpers.php');
@endphp

<x-admin-layout>
    <div class="mt-2">
        <h1 class="text-2xl font-medium text-gray-800">Detalles de solicitud</h1>
    </div>

    {{-- <div class="flex justify-end mb-4">
        <a class="text-white bg-azul-prodifem hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-sm px-5 py-2.5 text-center me-2 mb-2 dark:bg-blue-600 dark:hover:bg-azul-prodifem dark:focus:ring-blue-800"
            href="{{ route('admin.solicitudes.create') }}">Agregar</a>
        <a class="text-white bg-azul-prodifem hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-sm px-5 py-2.5 text-center me-2 mb-2 dark:bg-blue-600 dark:hover:bg-azul-prodifem dark:focus:ring-blue-800"
            href="">pdf</a>
    </div> --}}
    <div class="mt-4">
        <a class="text-white bg-azul-prodifem hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-sm px-5 py-2.5 text-center me-2 mb-2 dark:bg-blue-600 dark:hover:bg-azul-prodifem dark:focus:ring-blue-800"
            href="{{ route('admin.solicitudes.ordenPreparacion', $solicitud) }}" target="_blank">Orden de preparación</a>
    </div>

    <div class="mt-5">
        <a class="text-white bg-azul-prodifem hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-sm px-5 py-2.5 text-center me-2 mb-2 dark:bg-blue-600 dark:hover:bg-azul-prodifem dark:focus:ring-blue-800"
            href="{{ route('admin.solicitudes.remision', $solicitud) }}" target="_blank">Remisión</a>
    </div>

    <div class="mt-5">
        <a class="text-white bg-azul-prodifem hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-sm px-5 py-2.5 text-center me-2 mb-2 dark:bg-blue-600 dark:hover:bg-azul-prodifem dark:focus:ring-blue-800"
            href="{{ route('admin.solicitudes.envio', $solicitud) }}" target="_blank">Envío</a>
    </div>

    <div class="mt-5">
        <a class="text-white bg-azul-prodifem hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-sm px-5 py-2.5 text-center me-2 mb-2 dark:bg-blue-600 dark:hover:bg-azul-prodifem dark:focus:ring-blue-800"
            href="{{ route('admin.solicitudes.etiqueta', $solicitud) }}" target="_blank">Etiqueta</a>
    </div>



    {{-- Para visualizar la solicitud en cualquier caso, es decir, si fue aceptada o no, no se puede editar --}}

    <div class="flex flex-col items-center">
        <div class="mt-2 mb-4">
            <h1 class="text-2xl font-medium text-gray-800 text-center">SOLICITUD DE NUTRICIÓN PARENTERAL</h1>
        </div>
        <form id="solicitudForm" class="bg-white rounded-lg p-6 shadow-lg">
            @csrf

            @method('PUT')
            <x-validation-errors class="mb-4" />

            <div class="flex gap-4">
                <div class="mb-4 flex  items-baseline gap-2 w-full">
                    <x-label class="mb-2 whitespace-nowrap">
                        Paciente Nombre(s)*:
                    </x-label>
                    <div class="flex flex-col w-full">
                        <x-input-solicitud
                            value="{{ old('nombre_paciente', $solicitud->solicitud_patient->nombre_paciente) }}"
                            name="nombre_paciente" class="w-full" placeholder="" readonly/>
                    </div>
                </div>
                <div class="mb-4 flex items-baseline gap-2 w-full">
                    <x-label class="mb-2 whitespace-nowrap">
                        Paciente Apellidos*:
                    </x-label>
                    <div class="flex flex-col w-full">
                        <x-input-solicitud
                            value="{{ old('apellidos_paciente', $solicitud->solicitud_patient->apellidos_paciente) }}"
                            name="apellidos_paciente" class="w-full" placeholder="" readonly/>
                    </div>
                </div>
            </div>
            <div class="flex gap-4 ">
                <div class="mb-4 flex items-baseline gap-2 w-full">
                    <x-label class="mb-2">
                        Servicio*:
                    </x-label>
                    <div class="flex flex-col w-full">
                        <x-input-solicitud value="{{ old('servicio', $solicitud->solicitud_patient->servicio) }}"
                            name="servicio" class="" placeholder="" readonly/>
                    </div>
                </div>
                <div class="mb-4 flex items-baseline gap-2 w-full">
                    <x-label class="mb-2">
                        Cama:
                    </x-label>
                    <div class="flex flex-col w-full">
                        <x-input-solicitud value="{{ old('cama', $solicitud->solicitud_patient->cama) }}" name="cama"
                            class="" placeholder="" readonly/>
                    </div>
                </div>
                <div class="mb-4 flex items-baseline gap-2 w-full">
                    <x-label class="mb-2">
                        Piso:
                    </x-label>
                    <div class="flex flex-col w-full">
                        <x-input-solicitud value="{{ old('piso', $solicitud->solicitud_patient->piso) }}" name="piso"
                            class="" placeholder="" readonly/>
                    </div>
                </div>
            </div>
            <div class="flex gap-4">
                <div class="mb-4 flex items-baseline gap-2 w-full">
                    <x-label class="mb-2">
                        Registro:
                    </x-label>
                    <x-input-solicitud value="{{ old('registro', $solicitud->solicitud_patient->registro) }}"
                        name="registro" class="w-full" placeholder="" readonly/>
                </div>
                <div class="mb-4 flex items-baseline gap-2 w-full">
                    <x-label class="mb-2">
                        Diagnóstico:
                    </x-label>
                    <x-input-solicitud value="{{ old('diagnostico', $solicitud->solicitud_patient->diagnostico) }}"
                        name="diagnostico" class="w-full" placeholder="" readonly/>r
                </div>
            </div>
            <div class="flex gap-4">
                <div class="mb-4 flex items-baseline gap-2 w-full">
                    <x-label class="mb-2">
                        Peso:
                    </x-label>
                    <div class="flex flex-col w-full">
                        <x-input-solicitud type="number"
                            value="{{ old('peso', $solicitud->solicitud_patient->peso) }}" step="0.001"
                            name="peso" class="w-full" placeholder="" readonly/>
                    </div>
                </div>
                <div class="mb-4 flex items-stretch gap-2 w-full">
                    <x-label class="mb-2">
                        Sexo:
                    </x-label>
                    <x-select class="w-full" name="sexo" disabled>
                        <option value="Femenino" @if (old('sexo', $solicitud->solicitud_patient->sexo) == 'Femenino') selected @endif>Femenino</option>
                        <option value="Masculino" @if (old('sexo', $solicitud->solicitud_patient->sexo) == 'Masculino') selected @endif>Masculino</option>
                    </x-select>
                </div>
                <div class="mb-4 flex items-baseline gap-2 w-full">
                    <x-label class="mb-2 whitespace-nowrap">
                        Fecha de nacimiento*:
                    </x-label>
                    <div class="flex flex-col">
                        <x-input-solicitud type="date"
                            value="{{ old('fecha_nacimiento', $solicitud->solicitud_patient->fecha_nacimiento) }}"
                            max="{{ date('Y-m-d') }}" name="fecha_nacimiento" class="" placeholder=""
                            onchange="calcularEdad(this.value)" readonly/>
                    </div>
                </div>

            </div>
            <div class="flex gap-4">
                <div class="mb-4 flex items-stretch gap-2 w-full">
                    <x-label class="mb-2 whitespace-nowrap">
                        Vía de administración*:
                    </x-label>
                    <x-select class="w-full" name="via_administracion" disabled>
                        <option value="Central" @if (old('via_administracion', $solicitud->solicitud_detail->via_administracion) == 'Central') selected @endif>Central</option>
                        <option value="Periférica" @if (old('via_administracion', $solicitud->solicitud_detail->via_administracion) == 'Periférica') selected @endif>Periférica
                        </option>
                    </x-select>
                </div>
                <div class="mb-4 flex items-baseline gap-2 w-full">
                    <x-label class="mb-2 whitespace-nowrap">
                        Tiempo de infusión (h):
                    </x-label>
                    <x-input-solicitud type="number"
                        value="{{ old('tiempo_infusion_min', $solicitud->solicitud_detail->tiempo_infusion_min) }}"
                        name="tiempo_infusion_min" class="w-full" placeholder="" readonly/>
                </div>
            </div>
            <div class="flex gap-4">
                <div>
                    <div class="mb-4 flex items-baseline gap-2 w-full">
                        <x-label class="mb-2 whitespace-nowrap">
                            Sobrellenado* (mL):
                        </x-label>
                        <x-input-solicitud type="number"
                            value="{{ old('sobrellenado_ml', $solicitud->solicitud_detail->sobrellenado_ml) }}"
                            step="0.0001" name="sobrellenado_ml" class="w-32" placeholder="" readonly/>
                    </div>
                </div>
                <div class="mb-4 flex items-baseline gap-2 w-full">
                    <x-label class="mb-2 whitespace-nowrap">
                        Vol. Total* (mL):
                    </x-label>
                    <x-input-solicitud type="number"
                        value="{{ old('volumen_total', $solicitud->solicitud_detail->volumen_total) }}"
                        name="volumen_total" step="0.0001" class="w-full" placeholder="" readonly/>
                </div>
                <div class="mb-4 flex items-stretch gap-2 w-full">
                    <x-label class="mb-2">
                        NPT*:
                    </x-label>
                    <x-select class="w-full" name="npt" disabled>

                        <option value="RNPT" @if (old('npt', $solicitud->solicitud_detail->npt) == 'RNPT') selected @endif>RNPT</option>
                        <option value="LACT" @if (old('npt', $solicitud->solicitud_detail->npt) == 'LACT') selected @endif>LACT</option>
                        <option value="INF" @if (old('npt', $solicitud->solicitud_detail->npt) == 'INF') selected @endif>INF</option>
                        <option value="ADOL" @if (old('npt', $solicitud->solicitud_detail->npt) == 'ADOL') selected @endif>ADOL</option>
                        <option value="ADULT" @if (old('npt', $solicitud->solicitud_detail->npt) == 'ADULT') selected @endif>ADULT</option>
                    </x-select>

                </div>
            </div>

            <h2 class="mb-4 font-medium">MACRONUTRIENTES:</h2>
            <hr>
            <div class=" gap-4 items-center">
                <div class="w-full">
                    <h3 class="mt-4 font-medium">AMINOÁCIDOS</h3>

                    @foreach ($inputs as $input)
                        @if ($input->category_id == 1)
                            <div>
                                <div class="mb-4 flex items-baseline gap-2 w-full">
                                    <x-label class="mb-2 whitespace-nowrap">
                                        {{ $input->description }}
                                    </x-label>
                                    <div class="flex w-full">
                                        <x-input-solicitud type="number" class="w-full"
                                            value="{{ old('i_' . $input->input_id, renderInputSection($input->input_id, $inputs_solicitud)) }}"
                                            name="i_{{ $input->input_id }}" id="i_{{ $input->input_id }}"
                                            step="0.0001" placeholder="" readonly/>
                                        <span>{{ $input->unidad }}</span>

                                    </div>

                                    <x-label class="mb-2 whitespace-nowrap">
                                        Lote
                                    </x-label>
                                    <div class="flex w-full">
                                        <x-input-solicitud class="w-full" value="{{ old('l_' . $input->input_id, renderLoteSection($input->input_id, $inputs_solicitud)) }}"
                                            name="l_{{ $input->input_id }}" id="l_{{ $input->input_id }}"
                                            placeholder="" readonly/>
                                    </div>

                                    <x-label class="mb-2 whitespace-nowrap">
                                        Caducidad
                                    </x-label>
                                    <div class="flex w-full">

                                        <x-input-solicitud type="date" value="{{ old('c_' . $input->input_id,renderCaducidadSection($input->input_id, $inputs_solicitud)) }}"
                                            min="{{ \Carbon\Carbon::now()->format('Y-m-d') }}"
                                            id="c_{{ $input->input_id }}" name="c_{{ $input->input_id }}"
                                            class="" placeholder="" readonly/>
                                    </div>

                                </div>

                            </div>
                        @endif
                    @endforeach
                    @foreach ($inputs as $input)
                        @if ($input->category_id == 8)
                            <div>
                                <div class="mb-4 flex items-baseline gap-2 w-full">
                                    <x-label class="mb-2 whitespace-nowrap">
                                        {{ $input->description }}
                                    </x-label>
                                    <div class="flex  w-full">
                                        <x-input-solicitud type="number" class="w-full"
                                            value="{{ old('i_' . $input->input_id, renderInputSection($input->input_id, $inputs_solicitud)) }}"
                                            name="i_{{ $input->input_id }}" id="i_{{ $input->input_id }}"
                                            step="0.0001" placeholder="" readonly/>
                                        <span>{{ $input->unidad }}</span>
                                    </div>
                                    <x-label class="mb-2 whitespace-nowrap">
                                        Lote
                                    </x-label>
                                    <div class="flex w-full">
                                        <x-input-solicitud class="w-full" value="{{ old('l_' . $input->input_id,renderLoteSection($input->input_id, $inputs_solicitud)) }}"
                                            name="l_{{ $input->input_id }}" id="l_{{ $input->input_id }}"
                                            step="0.0001" placeholder="" readonly/>
                                    </div>

                                    <x-label class="mb-2 whitespace-nowrap">
                                        Caducidad
                                    </x-label>
                                    <div class="flex w-full">

                                        <x-input-solicitud type="date" value="{{ old('c_' . $input->input_id,renderCaducidadSection($input->input_id, $inputs_solicitud)) }}"
                                            min="{{ \Carbon\Carbon::now()->format('Y-m-d') }}"
                                            id="c_{{ $input->input_id }}" name="c_{{ $input->input_id }}"
                                            class="" placeholder="" readonly/>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
                <div class="w-full">
                    <h3 class="mt-4 font-medium">CARBOHIDRATOS:</h3>
                    @foreach ($inputs as $input)
                        @if ($input->category_id == 2)
                            <div>
                                <div class="mb-4 flex items-baseline gap-2 w-full">
                                    <x-label class="mb-2 whitespace-nowrap">
                                        {{ $input->description }}
                                    </x-label>
                                    <div class="flex w-full">
                                        <x-input-solicitud type="number" class="w-full"
                                            value="{{ old('i_' . $input->input_id, renderInputSection($input->input_id, $inputs_solicitud)) }}"
                                            name="i_{{ $input->input_id }}" id="i_{{ $input->input_id }}"
                                            step="0.0001" placeholder="" readonly/>
                                        <span>{{ $input->unidad }}</span>

                                    </div>
                                    <x-label class="mb-2 whitespace-nowrap">
                                        Lote
                                    </x-label>
                                    <div class="flex w-full">
                                        <x-input-solicitud class="w-full" value="{{ old('l_' . $input->input_id,renderLoteSection($input->input_id, $inputs_solicitud)) }}"
                                            name="l_{{ $input->input_id }}" id="l_{{ $input->input_id }}"
                                            step="0.0001" placeholder="" readonly/>
                                    </div>

                                    <x-label class="mb-2 whitespace-nowrap">
                                        Caducidad
                                    </x-label>
                                    <div class="flex w-full">

                                        <x-input-solicitud type="date" value="{{ old('c_' . $input->input_id,renderCaducidadSection($input->input_id, $inputs_solicitud)) }}"
                                            min="{{ \Carbon\Carbon::now()->format('Y-m-d') }}"
                                            id="c_{{ $input->input_id }}" name="c_{{ $input->input_id }}"
                                            class="" placeholder="" readonly/>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                    <H3 class="mt-4 font-medium">LÍPIDOS:</H3>
                    <div class="w-full">
                        @foreach ($inputs as $input)
                            @if ($input->category_id == 3)
                                <div class="w-full">
                                    <div class="mb-4 flex items-baseline gap-2 w-full">
                                        <x-label class="mb-2 whitespace-nowrap">
                                            {{ $input->description }}
                                        </x-label>
                                        <div class="flex w-full">
                                            <x-input-solicitud type="number" class="w-full"
                                                value="{{ old('i_' . $input->input_id, renderInputSection($input->input_id, $inputs_solicitud)) }}"
                                                name="i_{{ $input->input_id }}" id="i_{{ $input->input_id }}"
                                                step="0.0001" class="w-full" placeholder="" readonly/>
                                            <span>{{ $input->unidad }}</span>

                                        </div>
                                        <x-label class="mb-2 whitespace-nowrap">
                                            Lote
                                        </x-label>
                                        <div class="flex w-full">
                                            <x-input-solicitud class="w-full"
                                                value="{{ old('l_' . $input->input_id,renderLoteSection($input->input_id, $inputs_solicitud)) }}"
                                                name="l_{{ $input->input_id }}" id="l_{{ $input->input_id }}"
                                                step="0.0001" placeholder="" readonly/>
                                        </div>

                                        <x-label class="mb-2 whitespace-nowrap">
                                            Caducidad
                                        </x-label>
                                        <div class="flex w-full">

                                            <x-input-solicitud type="date"
                                                value="{{ old('c_' . $input->input_id,renderCaducidadSection($input->input_id, $inputs_solicitud)) }}"
                                                min="{{ \Carbon\Carbon::now()->format('Y-m-d') }}"
                                                id="c_{{ $input->input_id }}" name="c_{{ $input->input_id }}"
                                                class="" placeholder="" readonly/>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
            <h2 class="mb-4 font-medium">ELECTROLITOS</h2>
            <hr>
            <div class="gap-4 items-start mt-4">

                <div class=" grid-flow-col gap-4">
                    @foreach ($inputs as $input)
                        @if ($input->category_id == 4)
                            <div>
                                <div class="mb-4 flex items-baseline gap-2 w-full">
                                    <x-label class="mb-2 whitespace-nowrap">
                                        {{ $input->description }}
                                    </x-label>
                                    <div class="flex w-full">
                                        <x-input-solicitud type="number" class="w-full"
                                            value="{{ old('i_' . $input->input_id, renderInputSection($input->input_id, $inputs_solicitud)) }}"
                                            name="i_{{ $input->input_id }}" id="i_{{ $input->input_id }}"
                                            step="0.0001" placeholder="" readonly/>
                                        <span>{{ $input->unidad }}</span>

                                    </div>
                                    <x-label class="mb-2 whitespace-nowrap">
                                        Lote
                                    </x-label>
                                    <div class="flex w-full">
                                        <x-input-solicitud class="w-full" value="{{ old('l_' . $input->input_id,renderLoteSection($input->input_id, $inputs_solicitud)) }}"
                                            name="l_{{ $input->input_id }}" id="l_{{ $input->input_id }}"
                                            step="0.0001" placeholder="" readonly/>
                                    </div>

                                    <x-label class="mb-2 whitespace-nowrap">
                                        Caducidad
                                    </x-label>
                                    <div class="flex w-full">

                                        <x-input-solicitud type="date" value="{{ old('c_' . $input->input_id,renderCaducidadSection($input->input_id, $inputs_solicitud)) }}"
                                            min="{{ \Carbon\Carbon::now()->format('Y-m-d') }}"
                                            id="c_{{ $input->input_id }}" name="c_{{ $input->input_id }}"
                                            class="" placeholder="" readonly/>
                                    </div>
                                </div>

                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
            <h2 class="mb-4 font-medium">ADITIVOS:</h2>
            <hr>
            <div class=" gap-4 items-start mt-4">
                
                <div class="  gap-4 w-full">

                    @foreach ($inputs as $input)
                        @if ($input->category_id == 5)
                            <div>
                                <div class="mb-4 flex items-baseline gap-2 w-full">
                                    <x-label class="mb-2 whitespace-nowrap">
                                        {{ $input->description }}
                                    </x-label>
                                    <div class="flex w-full">
                                        <x-input-solicitud type="number" class="w-full"
                                            value="{{ old('i_' . $input->input_id, renderInputSection($input->input_id, $inputs_solicitud)) }}"
                                            name="i_{{ $input->input_id }}" id="i_{{ $input->input_id }}"
                                            step="0.0001" placeholder="" readonly/><span>{{ $input->unidad }}</span>
                                    </div>
                                    <x-label class="mb-2 whitespace-nowrap">
                                        Lote
                                    </x-label>
                                    <div class="flex w-full">
                                        <x-input-solicitud class="w-full" value="{{ old('l_' . $input->input_id,renderLoteSection($input->input_id, $inputs_solicitud)) }}"
                                            name="l_{{ $input->input_id }}" id="l_{{ $input->input_id }}"
                                            step="0.0001" placeholder="" readonly/>
                                    </div>

                                    <x-label class="mb-2 whitespace-nowrap">
                                        Caducidad
                                    </x-label>
                                    <div class="flex w-full">
                                        <x-input-solicitud type="date" value="{{ old('c_' . $input->input_id,renderCaducidadSection($input->input_id, $inputs_solicitud)) }}"
                                            min="{{ \Carbon\Carbon::now()->format('Y-m-d') }}"
                                            id="c_{{ $input->input_id }}" name="c_{{ $input->input_id }}"
                                            class="" placeholder="" readonly/>
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
                <textarea class="border-2 border-solid w-full resize-x overflow-auto h-20" name="observaciones" readonly>{{ old('observaciones', $solicitud->solicitud_detail->observaciones) }}</textarea>
            </div>
            <div class="flex flex-row gap-4 items-start w-full">
                <div class="w-full">
                    <div class="w-full">
                        <div class="mb-4 flex items-baseline gap-2 w-full">
                            <x-label class="mb-2">
                                Fecha y hora de entrega
                            </x-label>
                            <div class="flex flex-col w-full">
                                <x-input-solicitud type="datetime-local"
                                    value="{{ old('fecha_hora_entrega', $solicitud->solicitud_detail->fecha_hora_entrega) }}"
                                    min="{{ \Carbon\Carbon::now()->format('Y-m-d\TH:i') }}"
                                    name="fecha_hora_entrega" class="" placeholder="" readonly/>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="w-full">
                    <div class="w-full">
                        <div class="mb-4 flex items-baseline gap-2 w-full">
                            <x-label class="mb-2">
                                NOMBRE DEL MÉDICO*:
                            </x-label>
                            <div class="flex flex-col w-full">
                                <x-input-solicitud
                                    value="{{ old('nombre_medico', $solicitud->solicitud_detail->nombre_medico) }}"
                                    name="nombre_medico" class="w-full" placeholder="" readonly/>
                            </div>
                        </div>
                    </div>
                    <div class="w-full">
                        <div class="mb-4 flex items-baseline gap-2 w-full">
                            <x-label class="mb-2">
                                CÉDULA*:
                            </x-label>
                            <div class="flex flex-col w-full">
                                <x-input-solicitud value="{{ old('cedula', $solicitud->solicitud_detail->cedula) }}"
                                    name="cedula" class="w-full" placeholder="" readonly/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </form>

    </div>

    @push('js')
        <script>
            

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

        </script>
    @endpush







</x-admin-layout>
