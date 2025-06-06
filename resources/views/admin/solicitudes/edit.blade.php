@php
    // Incluye manualmente el archivo helpers.php
    require_once app_path('Helpers/helpers.php');

    $aguaCalculada = 0; // Inicializa la variable

    foreach ($inputs as $input) {
        if ($input->category_id == 7) {
            $valor = renderInputMLSection($input->input_id, $inputs_solicitud);
            // Limpia el valor para que sea numérico
            $valor = str_replace(',', '', $valor); // Elimina comas (u otros separadores)
            $aguaCalculada = floatval($valor); // Convierte el valor a número flotante
        }
    }

    $bolsaSeleccionadaId = null;
    $volumenTotalFinal = $solicitud->solicitud_detail->volumen_total_final;

    // Filtrar bolsas Eva que pueden contener el volumen total
    $bolsaSeleccionada = $inputs
        ->filter(function ($input) use ($volumenTotalFinal) {
            return $input->category_id == 6 && $input->presentacion_ml >= $volumenTotalFinal; // Bolsas válidas
        })
        ->sortBy('presentacion_ml') // Ordenar por tamaño de presentación ascendente
        ->first(); // Tomar la más pequeña que sea suficiente

    $bolsaSeleccionadaId = $bolsaSeleccionada ? $bolsaSeleccionada->input_id : null;

    $loteBolsaEva = $bolsaSeleccionada ? $bolsaSeleccionada->lote : null;
    $caducidadBolsaEva = $bolsaSeleccionada ? $bolsaSeleccionada->caducidad : null;
@endphp


<x-admin-layout>


    <div class="flex flex-col items-center">
        <div class="mt-2 mb-4">
            <h1 class="text-2xl font-medium text-gray-800 text-center">SOLICITUD DE NUTRICIÓN PARENTERAL</h1>
        </div>
        @if ($solicitud->solicitud_detail->sobrellenado_ml !== null)
            <p>El usuario ingreso un valor en el campo de sobrellenado:
                {{ $solicitud->solicitud_detail->sobrellenado_ml }} </p>
            @if ($solicitud->solicitud_detail->volumen_total !== null)
                <p>Volumen total ingresado por el usuario: {{ $solicitud->solicitud_detail->volumen_total }}</p>
                <p>Suma de elementos ingresados por el usuario en mL:
                    {{ number_format($solicitud->solicitud_detail->suma_volumen, 3, '.', '') }}
                <p>Agua calculada:
                    @foreach ($inputs as $input)
                        @if ($input->category_id == 7)
                            {{ number_format(renderInputMLSection($input->input_id, $inputs_solicitud), 3, '.', '') }}
                        @endif
                    @endforeach
                </p>
                @if ($solicitud->solicitud_detail->volumen_total < $solicitud->solicitud_detail->suma_volumen)
                    <h2 class="text-red-500">El volumen total en mL que ingresó el usuario es menor a la suma total en mL
                        de los elementos calculada. <br>
                        Verifica los valores, el cálculo del agua es negativo.</h2>
                @endif
                @if (60 < ($aguaCalculada / $solicitud->solicitud_detail->volumen_total) * 100)
                    <h2 class="text-red-500">El volumen total en mL que se genero es mayor al 60% del volumen total de la
                        mezcla. <br>
                        Reajusta el volumen total para generar un nuevo valor para el agua.</h2>
                @endif
                <p>Volumen total ingresado por el usuario con sobrellenado:
                    {{ number_format($solicitud->solicitud_detail->volumen_total_final, 2, '.', '') }}</p>
                <p>Suma total de los elementos ingresados por el usuario con sobrellenado:
                    {{ number_format($solicitud->solicitud_detail->suma_volumen_sobrellenado, 3, '.', '') }}</p>
                <p>Agua calculada con sobrellenado:
                    @foreach ($inputs as $input)
                        @if ($input->category_id == 7)
                            {{ number_format(renderInputMLSobrellenadoSection($input->input_id, $inputs_solicitud), 3, '.', '') }}
                        @endif
                    @endforeach
                </p>
            @else
                <p>El usuario no ingreso un volumen total.</p>
                <p>Suma de elementos ingresados por el usuario en mL:
                    {{ number_format($solicitud->solicitud_detail->suma_volumen, 3, '.', '') }}</p>
                <p>Suma de elementos ingresados por el usuario en mL con sobrellenado:
                    {{ number_format($solicitud->solicitud_detail->suma_volumen_sobrellenado, 3, '.', '') }}</p>
            @endif
        @else
            <p>El usuario no ingresó un valor en sobrellenado.</p>
            @if ($solicitud->solicitud_detail->volumen_total !== null)
                <p>Volumen total ingresado por el usuario: {{ $solicitud->solicitud_detail->volumen_total }}</p>
                <p>Suma de elementos ingresados por el usuario en mL:
                    {{ number_format($solicitud->solicitud_detail->suma_volumen, 3, '.', '') }}</p>
                <p>Agua calculada:
                    @foreach ($inputs as $input)
                        @if ($input->category_id == 7)
                            {{ number_format(renderInputMLSection($input->input_id, $inputs_solicitud), 3, '.', '') }}
                        @endif
                    @endforeach
                </p>
                @if ($solicitud->solicitud_detail->volumen_total < $solicitud->solicitud_detail->suma_volumen)
                    <h2 class="text-red-500">El volumen total en mL que ingresó el usuario es menor a la suma total en mL
                        de los elementos calculada. <br>
                        Verifica los valores, el cálculo del agua es negativo.</h2>
                @endif
                @if (60 < ($aguaCalculada / $solicitud->solicitud_detail->volumen_total) * 100)
                    <h2 class="text-red-500">El volumen total en mL que se genero es mayor al 60% del volumen total de la
                        mezcla. <br>
                        Reajusta el volumen total para generar un nuevo valor para el agua.</h2>
                @endif
            @else
                <p>El usuario no ingreso un volumen total.</p>
                <p>Suma de elementos ingresados por el usuario en mL:
                    {{ number_format($solicitud->solicitud_detail->suma_volumen, 3, '.', '') }}</p>
            @endif
        @endif

        <form id="solicitudForm" action="{{ route('admin.solicitudes.update', $solicitud) }}" method="POST"
            class="bg-white rounded-lg p-6 shadow-lg">
            @csrf

            @method('PUT')
            {{-- <x-validation-errors class="mb-4" /> --}}

            <div class="flex gap-4">
                <div class="mb-4 flex  items-baseline gap-2 w-full">
                    <x-label class="mb-2 whitespace-nowrap font-bold">
                        Paciente Nombre(s):
                    </x-label>
                    <div class="flex flex-col w-full">
                        <x-input-solicitud
                            value="{{ old('nombre_paciente', $solicitud->solicitud_patient->nombre_paciente) }}"
                            name="nombre_paciente" class="w-full" placeholder="" />
                        <!-- Mensaje de error -->
                        @error('nombre_paciente')
                            <div class="text-red-500 text-sm">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="mb-4 flex items-baseline gap-2 w-full">
                    <x-label class="mb-2 whitespace-nowrap font-bold">
                        Paciente Apellidos:
                    </x-label>
                    <div class="flex flex-col w-full">
                        <x-input-solicitud
                            value="{{ old('apellidos_paciente', $solicitud->solicitud_patient->apellidos_paciente) }}"
                            name="apellidos_paciente" class="w-full" placeholder="" />
                        <!-- Mensaje de error -->
                        @error('apellidos_paciente')
                            <div class="text-red-500 text-sm">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="flex gap-4 ">
                <div class="mb-4 flex items-baseline gap-2 w-full">
                    <x-label class="mb-2 font-bold">
                        Servicio:
                    </x-label>
                    <div class="flex flex-col w-full">
                        <x-input-solicitud value="{{ old('servicio', $solicitud->solicitud_patient->servicio) }}"
                            name="servicio" class="" placeholder="" />
                        <!-- Mensaje de error -->
                        @error('servicio')
                            <div class="text-red-500 text-sm">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="mb-4 flex items-baseline gap-2 w-full">
                    <x-label class="mb-2 font-bold">
                        Cama:
                    </x-label>
                    <div class="flex flex-col w-full">
                        <x-input-solicitud value="{{ old('cama', $solicitud->solicitud_patient->cama) }}"
                            name="cama" class="" placeholder="" />
                        <!-- Mensaje de error -->
                        @error('cama')
                            <div class="text-red-500 text-sm">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="mb-4 flex items-baseline gap-2 w-full">
                    <x-label class="mb-2 font-bold">
                        Piso:
                    </x-label>
                    <div class="flex flex-col w-full">
                        <x-input-solicitud value="{{ old('piso', $solicitud->solicitud_patient->piso) }}"
                            name="piso" class="" placeholder="" />
                        <!-- Mensaje de error -->
                        @error('piso')
                            <div class="text-red-500 text-sm">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="flex gap-4">
                <div class="mb-4 flex items-baseline gap-2 w-full">
                    <x-label class="mb-2 font-bold">
                        Registro:
                    </x-label>
                    <x-input-solicitud value="{{ old('registro', $solicitud->solicitud_patient->registro) }}"
                        name="registro" class="w-full" placeholder="" />
                    <!-- Mensaje de error -->
                    @error('registro')
                        <div class="text-red-500 text-sm">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-4 flex items-baseline gap-2 w-full">
                    <x-label class="mb-2 font-bold">
                        Diagnóstico:
                    </x-label>
                    <x-input-solicitud value="{{ old('diagnostico', $solicitud->solicitud_patient->diagnostico) }}"
                        name="diagnostico" class="w-full" placeholder="" />
                    <!-- Mensaje de error -->
                    @error('diagnostico')
                        <div class="text-red-500 text-sm">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="flex gap-4">
                <div class="mb-4 flex items-baseline gap-2 w-full">
                    <x-label class="mb-2 font-bold">
                        Peso:
                    </x-label>
                    <div class="flex flex-col w-full">
                        <x-input-solicitud type="number"
                            value="{{ old('peso', $solicitud->solicitud_patient->peso) }}" step="0.001"
                            name="peso" class="w-full" placeholder="" />
                        <!-- Mensaje de error -->
                        @error('peso')
                            <div class="text-red-500 text-sm">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="mb-4 flex items-stretch gap-2 w-full">
                    <x-label class="mb-2 font-bold">
                        Sexo:
                    </x-label>
                    <x-select class="w-full" name="sexo">
                        <option value="" disabled selected>Seleccionar Sexo</option>
                        <option value="Femenino" @if (old('sexo', $solicitud->solicitud_patient->sexo) == 'Femenino') selected @endif>Femenino</option>
                        <option value="Masculino" @if (old('sexo', $solicitud->solicitud_patient->sexo) == 'Masculino') selected @endif>Masculino</option>
                    </x-select>
                    <!-- Mensaje de error -->
                    @error('sexo')
                        <div class="text-red-500 text-sm">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-4 flex items-baseline gap-2 w-full">
                    <x-label class="mb-2 whitespace-nowrap font-bold">
                        Fecha de nacimiento*:
                    </x-label>
                    <div class="flex flex-col">
                        <x-input-solicitud type="date"
                            value="{{ old('fecha_nacimiento', $solicitud->solicitud_patient->fecha_nacimiento) }}"
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
                <div class="mb-4 flex items-stretch gap-2 w-full">
                    <x-label class="mb-2 whitespace-nowrap font-bold">
                        Vía de administración:
                    </x-label>
                    <x-select class="w-full" name="via_administracion">
                        <option value="Central" @if (old('via_administracion', $solicitud->solicitud_detail->via_administracion) == 'Central') selected @endif>Central</option>
                        <option value="Periférica" @if (old('via_administracion', $solicitud->solicitud_detail->via_administracion) == 'Periférica') selected @endif>Periférica
                        </option>
                    </x-select>
                    <!-- Mensaje de error -->
                    @error('via_administracion')
                        <div class="text-red-500 text-sm">{{ $message }}</div>
                    @enderror
                </div>
                @php
                    $inputValue = old('velocidad_infusion', $solicitud->solicitud_detail->velocidad_infusion);
                    $hasData = $inputValue ? true : false;
                @endphp
                <div class="mb-4 flex items-baseline gap-2 w-full">
                    <x-label class="mb-2 whitespace-nowrap font-bold">
                        Tiempo de infusión (h):
                    </x-label>
                    <x-input-solicitud type="number"
                        value="{{ $hasData ? '' : old('tiempo_infusion_min', $solicitud->solicitud_detail->tiempo_infusion_min) }}"
                        name="tiempo_infusion_min" class="w-full" placeholder="" />
                    <!-- Mensaje de error -->
                    @error('tiempo_infusion_min')
                        <div class="text-red-500 text-sm">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4 flex items-baseline gap-2 w-full">
                    <x-label class="mb-2 whitespace-nowrap">
                        Velocidad de infusión ml/hr:
                    </x-label>
                    <x-input-solicitud type="number"
                        value="{{ old('velocidad_infusion', $solicitud->solicitud_detail->velocidad_infusion) }}"
                        step="0.001" name="velocidad_infusion" class="w-full" placeholder="" />
                    <!-- Mensaje de error -->
                    @error('velocidad_infusion')
                        <div class="text-red-500 text-sm">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="flex gap-4">
                <div>
                    <div class="mb-4 flex items-baseline gap-2 w-full">
                        <x-label class="mb-2 whitespace-nowrap font-bold">
                            Sobrellenado (mL):
                        </x-label>
                        <x-input-solicitud type="number"
                            value="{{ old('sobrellenado_ml', $solicitud->solicitud_detail->sobrellenado_ml) }}"
                            step="0.0001" name="sobrellenado_ml" class="w-32" placeholder="" />
                        <!-- Mensaje de error -->
                        @error('sobrellenado_ml')
                            <div class="text-red-500 text-sm">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="mb-4 flex items-baseline gap-2 w-full">
                    <x-label class="mb-2 whitespace-nowrap font-bold">
                        Volumen total (mL):
                    </x-label>
                    <x-input-solicitud type="number"
                        value="{{ old('volumen_total', $solicitud->solicitud_detail->volumen_total) }}"
                        name="volumen_total" step="0.0001" class="w-full" placeholder="" />
                    <!-- Mensaje de error -->
                    @error('volumen_total')
                        <div class="text-red-500 text-sm">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-4 flex items-stretch gap-2 w-full">
                    <x-label class="mb-2 font-bold">
                        NPT*:
                    </x-label>
                    <x-select class="w-full" name="npt" id="npt-select">
                        {{-- <option value="" disabled selected>Seleccionar NPT</option>
                        {{-- <option value="RNPT" @if (old('npt', $solicitud->solicitud_detail->npt) == 'RNPT') selected @endif>RNPT</option>
                        <option value="LACT" @if (old('npt', $solicitud->solicitud_detail->npt) == 'LACT') selected @endif>LACT</option> --}}
                        <option value="INF" @if (old('npt', $solicitud->solicitud_detail->npt) == 'INF') selected @endif>PEDIÁTRICO</option>
                        {{-- <option value="ADOL" @if (old('npt', $solicitud->solicitud_detail->npt) == 'ADOL') selected @endif>ADOL</option> --}}
                        <option value="ADULT" @if (old('npt', $solicitud->solicitud_detail->npt) == 'ADULT') selected @endif>ADULTO</option> --}}
                    </x-select>
                    <!-- Mensaje de error -->
                    @error('npt')
                        <div class="text-red-500 text-sm">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class=" gap-4 items-start mt-4">
                <h2 class="mb-4 font-bold">MACRONUTRIENTES:</h2>
                <hr>
                {{-- agregar flexbox  flex flex-row --}}
                <div class=" gap-4 items-center">
                    <div class="w-full">
                        <h3 class="mt-4 font-bold">AMINOÁCIDOS</h3>

                        @foreach ($inputs as $input)
                            @if ($input->category_id == 1)
                                @php
                                    $inputValue = old(
                                        'i_' . $input->input_id,
                                        renderInputSection($input->input_id, $inputs_solicitud),
                                    );
                                    $hasData = $inputValue;
                                @endphp
                                <div
                                    class="mb-4 flex items-baseline gap-2 w-full {{ $hasData ? 'bg-yellow-200' : '' }}">
                                    <div class="flex w-[35%]">
                                        <x-label class="mb-2 whitespace-nowrap font-bold">
                                            {{ $input->description }}:
                                        </x-label>
                                        <div class="flex w-full">
                                            <x-input-solicitud type="number" class="w-full"
                                                value="{{ $inputValue }}" name="i_{{ $input->input_id }}"
                                                id="i_{{ $input->input_id }}" step="0.0001" placeholder="" />
                                            <span data-original-unidad="{{ $input->unidad }}"
                                                class="unidad-span">{{ $input->unidad }}</span>
                                        </div>
                                    </div>
                                    <div class="flex w-[10%] justify-center items-stretch">
                                        <x-label class="mb-2 whitespace-nowrap font-bold">
                                            ML:
                                        </x-label>
                                        <p
                                            class="flex border-t-0 border-r-0 border-l-0 border-b-2 border-dotted h-5 w-full pl-2 border-[#6b7280]">
                                            {{ renderInputMLSection($input->input_id, $inputs_solicitud) }}
                                        </p>
                                    </div>
                                    <div class="flex w-[20%] justify-center items-stretch">
                                        <x-label class="mb-2 whitespace-nowrap font-bold">
                                            Sobrellenado:
                                        </x-label>
                                        <p
                                            class="flex border-t-0 border-r-0 border-l-0 border-b-2 border-dotted h-5 w-full pl-2 border-[#6b7280]">
                                            {{ renderInputMLSobrellenadoSection($input->input_id, $inputs_solicitud) }}
                                        </p>
                                    </div>
                                    <div class="flex w-[15%]">
                                        <x-label class="mb-2 whitespace-nowrap font-bold">
                                            Lote:
                                        </x-label>
                                        <div class="flex w-full">
                                            <x-input-solicitud class="w-full" name="l_{{ $input->input_id }}"
                                                id="l_{{ $input->input_id }}" placeholder=""
                                                value="{{ $input->medicine->lote ?? '' }}" />
                                        </div>
                                    </div>
                                    <div class="flex w-[20%]">
                                        <x-label class="mb-2 whitespace-nowrap font-bold">
                                            Caducidad:
                                        </x-label>
                                        <div class="flex w-full">
                                            <x-input-solicitud type="date"
                                                min="{{ \Carbon\Carbon::now()->format('Y-m-d') }}"
                                                id="c_{{ $input->input_id }}" name="c_{{ $input->input_id }}"
                                                placeholder="" value="{{ $input->medicine->caducidad ?? '' }}" />
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach

                        @foreach ($inputs as $input)
                            @if ($input->category_id == 8)
                                @php
                                    $inputValue = old(
                                        'i_' . $input->input_id,
                                        renderInputSection($input->input_id, $inputs_solicitud),
                                    );
                                    $hasData = $inputValue;
                                @endphp
                                <div>
                                    <div
                                        class="mb-4 flex items-baseline gap-2 w-full {{ $hasData ? 'bg-yellow-200' : '' }}">
                                        <div class="flex w-[40%]">
                                            <x-label class="mb-2 whitespace-nowrap font-bold">
                                                {{ $input->description }}:
                                            </x-label>
                                            <div class="flex  w-full">
                                                <x-input-solicitud type="number" class="w-full"
                                                    value="{{ old('i_' . $input->input_id, renderInputSection($input->input_id, $inputs_solicitud)) }}"
                                                    name="i_{{ $input->input_id }}" id="i_{{ $input->input_id }}"
                                                    step="0.0001" placeholder="" />
                                                <span>{{ $input->unidad }}</span>
                                                {{-- @error('i_' . $input->input_id . '_' . $input->unidad)
                                                <div class="text-red-500">{{ $message }}</div>
                                            @enderror --}}
                                            </div>
                                        </div>
                                        <div class="flex w-[10%] justify-center items-stretch">
                                            <x-label class="mb-2 whitespace-nowrap font-bold">
                                                ML:
                                            </x-label>
                                            <p
                                                class="flex border-t-0 border-r-0 border-l-0 border-b-2 border-dotted h-5 w-full pl-2 border-[#6b7280]">
                                                {{ renderInputMLSection($input->input_id, $inputs_solicitud) }}
                                            </p>
                                        </div>
                                        <div class="flex w-[20%] justify-center items-stretch">
                                            <x-label class="mb-2 whitespace-nowrap font-bold">
                                                Sobrellenado:
                                            </x-label>
                                            <p
                                                class="flex border-t-0 border-r-0 border-l-0 border-b-2 border-dotted h-5 w-full pl-2 border-[#6b7280]">
                                                {{ renderInputMLSobrellenadoSection($input->input_id, $inputs_solicitud) }}
                                            </p>
                                        </div>
                                        <div class="flex w-[20%]">
                                            <x-label class="mb-2 whitespace-nowrap font-bold">
                                                Lote:
                                            </x-label>
                                            <div class="flex w-full">
                                                <x-input-solicitud class="w-full"
                                                    value="{{ old('l_' . $input->input_id, renderLoteSection($input->input_id, $inputs_solicitud)) }}"
                                                    name="l_{{ $input->input_id }}" id="l_{{ $input->input_id }}"
                                                    step="0.0001" placeholder=""
                                                    value="{{ $input->medicine->lote ?? '' }}" />
                                            </div>
                                        </div>
                                        <div class="flex w-[25%]">
                                            <x-label class="mb-2 whitespace-nowrap font-bold">
                                                Caducidad:
                                            </x-label>
                                            <div class="flex w-full">
                                                {{-- <x-input-solicitud type="date" value="{{ old('fecha_hora_entrega', $solicitud->solicitud_detail->fecha_hora_entrega->format('Y-m-d')) }}"
                                                    min="{{ \Carbon\Carbon::now()->format('Y-m-d') }}" name="fecha_hora_entrega"
                                                    class="" placeholder="" /> --}}
                                                <x-input-solicitud type="date"
                                                    value="{{ old('c_' . $input->input_id, renderCaducidadSection($input->input_id, $inputs_solicitud)) }}"
                                                    min="{{ \Carbon\Carbon::now()->format('Y-m-d') }}"
                                                    id="c_{{ $input->input_id }}" name="c_{{ $input->input_id }}"
                                                    class="" placeholder=""
                                                    value="{{ $input->medicine->caducidad ?? '' }}" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                    <div class="w-full">
                        <h3 class="font-bold">CARBOHIDRATOS:</h3>
                        @foreach ($inputs as $input)
                            @if ($input->category_id == 2)
                                @php
                                    $inputValue = old(
                                        'i_' . $input->input_id,
                                        renderInputSection($input->input_id, $inputs_solicitud),
                                    );
                                    $hasData = $inputValue;
                                @endphp
                                <div>
                                    <div
                                        class="mb-4 flex items-baseline gap-2 w-full {{ $hasData ? 'bg-yellow-200' : '' }}">
                                        <div class="flex w-[40%]">
                                            <x-label class="mb-2 whitespace-nowrap font-bold">
                                                {{ $input->description }}:
                                            </x-label>
                                            <div class="flex w-full">
                                                <x-input-solicitud type="number" class="w-full"
                                                    value="{{ old('i_' . $input->input_id, renderInputSection($input->input_id, $inputs_solicitud)) }}"
                                                    name="i_{{ $input->input_id }}" id="i_{{ $input->input_id }}"
                                                    step="0.0001" placeholder="" />
                                                <span data-original-unidad="{{ $input->unidad }}"
                                                    class="unidad-span">{{ $input->unidad }}</span>
                                                {{-- @error('i_' . $input->input_id . '_' . $input->unidad)
                                                <div class="text-red-500">{{ $message }}</div>
                                            @enderror --}}
                                            </div>
                                        </div>
                                        <div class="flex w-[10%] justify-center items-stretch">
                                            <x-label class="mb-2 whitespace-nowrap font-bold">
                                                ML:
                                            </x-label>
                                            <p
                                                class="flex border-t-0 border-r-0 border-l-0 border-b-2 border-dotted h-5 w-full pl-2 border-[#6b7280]">
                                                {{ renderInputMLSection($input->input_id, $inputs_solicitud) }}
                                            </p>
                                        </div>
                                        <div class="flex w-[20%] justify-center items-stretch">
                                            <x-label class="mb-2 whitespace-nowrap font-bold">
                                                Sobrellenado:
                                            </x-label>
                                            <p
                                                class="flex border-t-0 border-r-0 border-l-0 border-b-2 border-dotted h-5 w-full pl-2 border-[#6b7280]">
                                                {{ renderInputMLSobrellenadoSection($input->input_id, $inputs_solicitud) }}
                                            </p>
                                        </div>
                                        <div class="flex w-[20%]">
                                            <x-label class="mb-2 whitespace-nowrap font-bold">
                                                Lote:
                                            </x-label>
                                            <div class="flex w-full">
                                                <x-input-solicitud class="w-full"
                                                    value="{{ old('l_' . $input->input_id, renderLoteSection($input->input_id, $inputs_solicitud)) }}"
                                                    name="l_{{ $input->input_id }}" id="l_{{ $input->input_id }}"
                                                    step="0.0001" placeholder=""
                                                    value="{{ $input->medicine->lote ?? '' }}" />
                                            </div>
                                        </div>
                                        <div class="flex w-[25%]">
                                            <x-label class="mb-2 whitespace-nowrap font-bold">
                                                Caducidad:
                                            </x-label>
                                            <div class="flex w-full">
                                                {{-- <x-input-solicitud type="date" value="{{ old('fecha_hora_entrega', $solicitud->solicitud_detail->fecha_hora_entrega->format('Y-m-d')) }}"
                                                    min="{{ \Carbon\Carbon::now()->format('Y-m-d') }}" name="fecha_hora_entrega"
                                                    class="" placeholder="" /> --}}
                                                <x-input-solicitud type="date"
                                                    value="{{ old('c_' . $input->input_id, renderCaducidadSection($input->input_id, $inputs_solicitud)) }}"
                                                    min="{{ \Carbon\Carbon::now()->format('Y-m-d') }}"
                                                    id="c_{{ $input->input_id }}" name="c_{{ $input->input_id }}"
                                                    class="" placeholder=""
                                                    value="{{ $input->medicine->caducidad ?? '' }}" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                        <H3 class="font-bold">LÍPIDOS:</H3>
                        <div class="w-full">
                            @foreach ($inputs as $input)
                                @if ($input->category_id == 3)
                                    @php
                                        $inputValue = old(
                                            'i_' . $input->input_id,
                                            renderInputSection($input->input_id, $inputs_solicitud),
                                        );
                                        $hasData = $inputValue;
                                    @endphp
                                    <div class="w-full">
                                        <div
                                            class="mb-4 flex flex-wrap items-baseline gap-2 w-full {{ $hasData ? 'bg-yellow-200' : '' }}">
                                            <div class="flex w-[40%]">
                                                <x-label class="mb-2 whitespace-nowrap font-bold">
                                                    {{ $input->description }}:
                                                </x-label>
                                                <div class="flex w-full">
                                                    <x-input-solicitud type="number" class="w-full"
                                                        value="{{ old('i_' . $input->input_id, renderInputSection($input->input_id, $inputs_solicitud)) }}"
                                                        name="i_{{ $input->input_id }}"
                                                        id="i_{{ $input->input_id }}" step="0.0001" class="w-full"
                                                        placeholder="" />
                                                    <span data-original-unidad="{{ $input->unidad }}"
                                                        class="unidad-span">{{ $input->unidad }}</span>
                                                    {{-- @error('i_' . $input->input_id . '_' . $input->unidad)
                                                    <div class="text-red-500">{{ $message }}</div>
                                                @enderror --}}
                                                </div>
                                            </div>
                                            <div class="flex w-[10%] justify-center items-stretch">
                                                <x-label class="mb-2 whitespace-nowrap font-bold">
                                                    ML:
                                                </x-label>
                                                <p
                                                    class="flex border-t-0 border-r-0 border-l-0 border-b-2 border-dotted h-5 w-full pl-2 border-[#6b7280]">
                                                    {{ renderInputMLSection($input->input_id, $inputs_solicitud) }}
                                                </p>
                                            </div>
                                            <div class="flex w-[20%] justify-center items-stretch">
                                                <x-label class="mb-2 whitespace-nowrap font-bold">
                                                    Sobrellenado:
                                                </x-label>
                                                <p
                                                    class="flex border-t-0 border-r-0 border-l-0 border-b-2 border-dotted h-5 w-full pl-2 border-[#6b7280]">
                                                    {{ renderInputMLSobrellenadoSection($input->input_id, $inputs_solicitud) }}
                                                </p>
                                            </div>
                                            <div class="flex w-[20%]">
                                                <x-label class="mb-2 whitespace-nowrap font-bold">
                                                    Lote:
                                                </x-label>
                                                <div class="flex w-full">
                                                    <x-input-solicitud class="w-full"
                                                        value="{{ old('l_' . $input->input_id, renderLoteSection($input->input_id, $inputs_solicitud)) }}"
                                                        name="l_{{ $input->input_id }}"
                                                        id="l_{{ $input->input_id }}" step="0.0001" placeholder=""
                                                        value="{{ $input->medicine->lote ?? '' }}" />
                                                </div>
                                            </div>
                                            <div class="flex w-[25%]">
                                                <x-label class="mb-2 whitespace-nowrap font-bold">
                                                    Caducidad:
                                                </x-label>
                                                <div class="flex w-full">
                                                    {{-- <x-input-solicitud type="date" value="{{ old('fecha_hora_entrega', $solicitud->solicitud_detail->fecha_hora_entrega->format('Y-m-d')) }}"
                                                        min="{{ \Carbon\Carbon::now()->format('Y-m-d') }}" name="fecha_hora_entrega"
                                                        class="" placeholder="" /> --}}
                                                    <x-input-solicitud type="date"
                                                        value="{{ old('c_' . $input->input_id, renderCaducidadSection($input->input_id, $inputs_solicitud)) }}"
                                                        min="{{ \Carbon\Carbon::now()->format('Y-m-d') }}"
                                                        id="c_{{ $input->input_id }}"
                                                        name="c_{{ $input->input_id }}" class=""
                                                        placeholder=""
                                                        value="{{ $input->medicine->caducidad ?? '' }}" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
                <h2 class="mb-4 font-bold">ELECTROLITOS</h2>
                <hr>
                <div class="gap-4 items-start mt-4">
                    {{-- agregar grid grid grid-rows-4 --}}
                    <div class=" grid-flow-col gap-4">
                        @foreach ($inputs as $input)
                            @if ($input->category_id == 4)
                                @php
                                    $inputValue = old(
                                        'i_' . $input->input_id,
                                        renderInputSection($input->input_id, $inputs_solicitud),
                                    );
                                    $hasData = $inputValue;
                                @endphp
                                <div>
                                    <div
                                        class="mb-4 flex items-baseline gap-2 w-full {{ $hasData ? 'bg-yellow-200' : '' }}">
                                        <div class="flex w-[40%]">
                                            <x-label class="mb-2 whitespace-nowrap font-bold">
                                                {{ $input->description }}:
                                            </x-label>
                                            <div class="flex w-full">
                                                <x-input-solicitud type="number" class="w-full"
                                                    value="{{ old('i_' . $input->input_id, renderInputSection($input->input_id, $inputs_solicitud)) }}"
                                                    name="i_{{ $input->input_id }}" id="i_{{ $input->input_id }}"
                                                    step="0.0001" placeholder="" />
                                                <span data-original-unidad="{{ $input->unidad }}"
                                                    class="unidad-span-electrolitos">{{ $input->unidad }}</span>
                                                {{-- @error('i_' . $input->input_id . '_' . $input->unidad)
                                                <div class="text-red-500">{{ $message }}</div>
                                            @enderror --}}
                                            </div>
                                        </div>
                                        <div class="flex w-[10%] justify-center items-stretch">
                                            <x-label class="mb-2 whitespace-nowrap font-bold">
                                                ML:
                                            </x-label>
                                            <p
                                                class="flex border-t-0 border-r-0 border-l-0 border-b-2 border-dotted h-5 w-full pl-2 border-[#6b7280]">
                                                {{ renderInputMLSection($input->input_id, $inputs_solicitud) }}
                                            </p>
                                        </div>
                                        <div class="flex w-[20%] justify-center items-stretch">
                                            <x-label class="mb-2 whitespace-nowrap font-bold">
                                                Sobrellenado:
                                            </x-label>
                                            <p
                                                class="flex border-t-0 border-r-0 border-l-0 border-b-2 border-dotted h-5 w-full pl-2 border-[#6b7280]">
                                                {{ renderInputMLSobrellenadoSection($input->input_id, $inputs_solicitud) }}
                                            </p>
                                        </div>
                                        <div class="flex w-[20%]">
                                            <x-label class="mb-2 whitespace-nowrap font-bold">
                                                Lote:
                                            </x-label>
                                            <div class="flex w-full">
                                                <x-input-solicitud class="w-full"
                                                    value="{{ old('l_' . $input->input_id, renderLoteSection($input->input_id, $inputs_solicitud)) }}"
                                                    name="l_{{ $input->input_id }}" id="l_{{ $input->input_id }}"
                                                    step="0.0001" placeholder=""
                                                    value="{{ $input->medicine->lote ?? '' }}" />
                                            </div>
                                        </div>
                                        <div class="flex w-[25%]">
                                            <x-label class="mb-2 whitespace-nowrap font-bold">
                                                Caducidad:
                                            </x-label>
                                            <div class="flex w-full">
                                                {{-- <x-input-solicitud type="date" value="{{ old('fecha_hora_entrega', $solicitud->solicitud_detail->fecha_hora_entrega->format('Y-m-d')) }}"
                                                    min="{{ \Carbon\Carbon::now()->format('Y-m-d') }}" name="fecha_hora_entrega"
                                                    class="" placeholder="" /> --}}
                                                <x-input-solicitud type="date"
                                                    value="{{ old('c_' . $input->input_id, renderCaducidadSection($input->input_id, $inputs_solicitud)) }}"
                                                    min="{{ \Carbon\Carbon::now()->format('Y-m-d') }}"
                                                    id="c_{{ $input->input_id }}" name="c_{{ $input->input_id }}"
                                                    class="" placeholder=""
                                                    value="{{ $input->medicine->caducidad ?? '' }}" />
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
                <h2 class="mb-4 font-bold">ADITIVOS:</h2>
                <hr>
                <div class=" gap-4 items-start mt-4">
                    {{-- agregar grid grid-rows-9 grid-flow-col --}}
                    <div class="gap-4 w-full">

                        @foreach ($inputs as $input)
                            @if ($input->category_id == 5)
                                @php
                                    $inputValue = old(
                                        'i_' . $input->input_id,
                                        renderInputSection($input->input_id, $inputs_solicitud),
                                    );
                                    $hasData = $inputValue;
                                @endphp
                                <div>
                                    <div
                                        class="mb-4 flex items-baseline gap-2 w-full {{ $hasData ? 'bg-yellow-200' : '' }}">
                                        <div class="flex w-[40%]">
                                            <x-label class="mb-2 whitespace-nowrap font-bold">
                                                {{ $input->description }}:
                                            </x-label>
                                            <div class="flex w-full">
                                                <x-input-solicitud type="number" class="w-full"
                                                    value="{{ old('i_' . $input->input_id, renderInputSection($input->input_id, $inputs_solicitud)) }}"
                                                    name="i_{{ $input->input_id }}" id="i_{{ $input->input_id }}"
                                                    step="0.0001"
                                                    placeholder="" /><span>{{ $input->unidad }}</span>

                                                {{-- @error('i_' . $input->input_id . '_' . $input->unidad)
                                                <div class="text-red-500">{{ $message }}</div>
                                            @enderror --}}
                                            </div>
                                        </div>
                                        <div class="flex w-[10%] justify-center items-stretch">
                                            <x-label class="mb-2 whitespace-nowrap font-bold">
                                                ML:
                                            </x-label>
                                            <p
                                                class="flex border-t-0 border-r-0 border-l-0 border-b-2 border-dotted h-5 w-full pl-2 border-[#6b7280]">
                                                {{ renderInputMLSection($input->input_id, $inputs_solicitud) }}
                                            </p>
                                        </div>
                                        <div class="flex w-[20%] justify-center items-stretch">
                                            <x-label class="mb-2 whitespace-nowrap font-bold">
                                                Sobrellenado:
                                            </x-label>
                                            <p
                                                class="flex border-t-0 border-r-0 border-l-0 border-b-2 border-dotted h-5 w-full pl-2 border-[#6b7280]">
                                                {{ renderInputMLSobrellenadoSection($input->input_id, $inputs_solicitud) }}
                                            </p>
                                        </div>
                                        <div class="flex w-[20%]">
                                            <x-label class="mb-2 whitespace-nowrap font-bold">
                                                Lote:
                                            </x-label>
                                            <div class="flex w-full">
                                                <x-input-solicitud class="w-full"
                                                    value="{{ old('l_' . $input->input_id, renderLoteSection($input->input_id, $inputs_solicitud)) }}"
                                                    name="l_{{ $input->input_id }}" id="l_{{ $input->input_id }}"
                                                    step="0.0001" placeholder=""
                                                    value="{{ $input->medicine->lote ?? '' }}" />
                                            </div>
                                        </div>
                                        <div class="flex w-[25%]">
                                            <x-label class="mb-2 whitespace-nowrap font-bold">
                                                Caducidad:
                                            </x-label>
                                            <div class="flex w-full">
                                                {{-- <x-input-solicitud type="date" value="{{ old('fecha_hora_entrega', $solicitud->solicitud_detail->fecha_hora_entrega->format('Y-m-d')) }}"
                                                    min="{{ \Carbon\Carbon::now()->format('Y-m-d') }}" name="fecha_hora_entrega"
                                                    class="" placeholder="" /> --}}
                                                <x-input-solicitud type="date"
                                                    value="{{ old('c_' . $input->input_id, renderCaducidadSection($input->input_id, $inputs_solicitud)) }}"
                                                    min="{{ \Carbon\Carbon::now()->format('Y-m-d') }}"
                                                    id="c_{{ $input->input_id }}" name="c_{{ $input->input_id }}"
                                                    class="" placeholder=""
                                                    value="{{ $input->medicine->caducidad ?? '' }}" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>

                    @foreach ($inputs as $input)
                        @if ($input->category_id == 10)
                            <div>
                                <div class="mb-4 flex items-baseline gap-2 w-full">
                                    <x-label class="mb-2 whitespace-nowrap font-bold">
                                        {{ $input->description }}:
                                    </x-label>
                                    <div class="flex w-full">
                                        <x-select class="w-full" name="i_{{ $input->input_id }}"
                                            id="i_{{ $input->input_id }}_{{ $input->unidad }}">
                                            <option value="0" @if (old('i_' . $input->input_id, renderInputSection($input->input_id, $inputs_solicitud)) == '0') selected @endif>
                                                No
                                            </option>
                                            <option value="1" @if (old('i_' . $input->input_id, renderInputSection($input->input_id, $inputs_solicitud)) == '1') selected @endif>
                                                Si
                                            </option>
                                        </x-select>
                                    </div>
                                    <x-label class="mb-2 whitespace-nowrap font-bold">
                                        Lote:
                                    </x-label>
                                    <div class="flex w-full">
                                        <x-input-solicitud class="w-full"
                                            value="{{ old('l_' . $input->input_id, renderLoteSection($input->input_id, $inputs_solicitud)) }}"
                                            name="l_{{ $input->input_id }}" id="l_{{ $input->input_id }}"
                                            step="0.0001" placeholder=""
                                            value="{{ $input->medicine->lote ?? '' }}" />
                                    </div>

                                    <x-label class="mb-2 whitespace-nowrap font-bold">
                                        Caducidad:
                                    </x-label>
                                    <div class="flex w-full">
                                        {{-- <x-input-solicitud type="date" value="{{ old('fecha_hora_entrega', $solicitud->solicitud_detail->fecha_hora_entrega->format('Y-m-d')) }}"
                                            min="{{ \Carbon\Carbon::now()->format('Y-m-d') }}" name="fecha_hora_entrega"
                                            class="" placeholder="" /> --}}
                                        <x-input-solicitud type="date"
                                            value="{{ old('c_' . $input->input_id, renderCaducidadSection($input->input_id, $inputs_solicitud)) }}"
                                            min="{{ \Carbon\Carbon::now()->format('Y-m-d') }}"
                                            id="c_{{ $input->input_id }}" name="c_{{ $input->input_id }}"
                                            class="" placeholder=""
                                            value="{{ $input->medicine->caducidad ?? '' }}" />
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach

                    <div class="flex">
                        <div class="flex items-center w-6/12">

                            <x-label class="mb-2 whitespace-nowrap">
                                Bolsa Eva:
                            </x-label>
                            <div class="flex w-full">
                                <x-select class="w-full" name="bolsa_eva" id="bolsa_eva">
                                    <option value="" disabled selected>Seleccionar Bolsa Eva</option>
                                    @foreach ($inputs as $input)
                                        @if ($input->category_id == 6)
                                            <option value="{{ $input->input_id }}"
                                                @if (old('bolsa_eva', $bolsaSeleccionadaId) == $input->input_id) selected @endif>
                                                {{ $input->description }}
                                            </option>
                                        @endif
                                    @endforeach
                                </x-select>
                            </div>
                            <!-- Mensaje de error -->
                            @error('bolsa_eva')
                                <div class="text-red-500 text-sm">{{ $message }}</div>
                            @enderror

                        </div>
                        <div class="flex items-center w-3/12">
                            <x-label class="mb-2 whitespace-nowrap">
                                Lote:
                            </x-label>
                            <div class="flex w-full">
                                <x-input-solicitud class="w-full" value="{{ old('lote_bolsa_eva', $loteBolsaEva) }}"
                                    name="lote_bolsa_eva" id="lote_bolsa_eva" step="0.0001" placeholder="" />
                            </div>
                            @error('lote_bolsa_eva')
                                <div class="text-red-500 text-sm">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="flex items-center w-3/12">
                            <x-label class="mb-2 whitespace-nowrap">
                                Caducidad:
                            </x-label>
                            <div class="flex w-full">
                                <x-input-solicitud type="date"
                                    value="{{ old('caducidad_bolsa_eva', $caducidadBolsaEva) }}"
                                    min="{{ \Carbon\Carbon::now()->format('Y-m-d') }}" name="caducidad_bolsa_eva"
                                    id="caducidad_bolsa_eva" class="" placeholder="" />
                            </div>
                            @error('caducidad_bolsa_eva')
                                <div class="text-red-500 text-sm">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="mb-4">
                    <x-label class="mb-2 font-bold">
                        OBSERVACIONES
                    </x-label>
                    <textarea class="border-2 border-solid w-full resize-x overflow-auto h-20" name="observaciones">{{ old('observaciones', $solicitud->solicitud_detail->observaciones) }}</textarea>
                    <!-- Mensaje de error -->
                    @error('observaciones')
                        <div class="text-red-500 text-sm">{{ $message }}</div>
                    @enderror
                </div>
                <div class="flex flex-row gap-4 items-start w-full">
                    <div class="w-full">
                        <div class="w-full">
                            <div class="mb-4 flex items-baseline gap-2 w-full">
                                <x-label class="mb-2 font-bold">
                                    Fecha y hora de entrega:
                                </x-label>
                                <div class="flex flex-col w-full">
                                    <x-input-solicitud type="datetime-local"
                                        value="{{ old('fecha_hora_entrega', $solicitud->solicitud_detail->fecha_hora_entrega) }}"
                                        min="{{ \Carbon\Carbon::now()->format('Y-m-d\TH:i') }}"
                                        name="fecha_hora_entrega" class="" placeholder="" />
                                    <!-- Mensaje de error -->
                                    @error('fecha_hora_entrega')
                                        <div class="text-red-500 text-sm">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="w-full">
                            <div class="w-full">
                                <div class="mb-4 flex items-baseline gap-2 w-full">
                                    <x-label class="mb-2 font-bold">
                                        Fecha y hora de preparación:
                                    </x-label>
                                    <div class="flex flex-col w-full">
                                        <x-input-solicitud type="datetime-local"
                                            value="{{ old('fecha_hora_preparacion') }}"
                                            min="{{ \Carbon\Carbon::now()->format('Y-m-d\TH:i') }}"
                                            name="fecha_hora_preparacion" class="" placeholder="" />
                                        <!-- Mensaje de error -->
                                        @error('fecha_hora_preparacion')
                                            <div class="text-red-500 text-sm">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="w-full">
                            <div class="mb-4 flex items-baseline gap-2 w-full">
                                <x-label class="mb-2 font-bold">
                                    Hospital destino:
                                </x-label>
                                <div class="flex flex-col w-full">
                                    <x-input-solicitud
                                        value="{{ old('hospital_destino', $solicitud->solicitud_detail->hospital_destino) }}"
                                        name="hospital_destino" class="w-full" placeholder="" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="w-full">
                        <div class="w-full">
                            <div class="mb-4 flex items-baseline gap-2 w-full">
                                <x-label class="mb-2 font-bold">
                                    Nombre del médico:
                                </x-label>
                                <div class="flex flex-col w-full">
                                    <x-input-solicitud
                                        value="{{ old('nombre_medico', $solicitud->solicitud_detail->nombre_medico) }}"
                                        name="nombre_medico" class="w-full" placeholder="" />
                                    <!-- Mensaje de error -->
                                    @error('nombre_medico')
                                        <div class="text-red-500 text-sm">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="w-full">
                            <div class="mb-4 flex items-baseline gap-2 w-full">
                                <x-label class="mb-2 font-bold">
                                    Cédula profesional:
                                </x-label>
                                <div class="flex flex-col w-full">
                                    <x-input-solicitud
                                        value="{{ old('cedula', $solicitud->solicitud_detail->cedula) }}"
                                        name="cedula" class="w-full" placeholder="" />
                                    <!-- Mensaje de error -->
                                    @error('cedula')
                                        <div class="text-red-500 text-sm">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <input type="hidden" name="is_aprobada" id="is_aprobada_input" value='Pendiente'>
                <div class="flex justify-end gap-5 mb-3">
                    {{-- <input type="checkbox" name="terminos" id="terminos"> Enviar de todas formas --}}
                    <x-button>
                        ACTUALIZAR
                    </x-button>
                </div>
                <div class="flex justify-end gap-5 mb-3">
                    {{-- <input type="checkbox" name="terminos" id="terminos"> Enviar de todas formas --}}
                    <x-button type="button" onclick="updateIsAprobada('Aprobada')">
                        APROBAR
                    </x-button>
                </div>

                <div class="flex justify-end gap-5">
                    {{-- <input type="checkbox" name="terminos" id="terminos"> Enviar de todas formas --}}
                    <x-button type="button" onclick="updateIsAprobada('No Aprobada')">
                        RECHAZAR
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


            // document.addEventListener('DOMContentLoaded', function() {
            //     var inputHoraEntrega = document.getElementById('hora_entrega');

            //     // Escuchar el evento 'change' del campo de entrada
            //     inputHoraEntrega.addEventListener('change', function() {
            //         // Obtener el valor del campo de entrada
            //         var hora = this.value;

            //         // Convertir la hora al formato de 24 horas
            //         var hora24h = convertirHoraA24(hora);

            //         // Establecer el valor convertido en el campo de entrada
            //         this.value = hora24h;
            //     });

            //     // Función para convertir la hora al formato de 24 horas
            //     function convertirHoraA24(hora12h) {
            //         var partes = hora12h.split(':'); // Dividir la hora en horas y minutos
            //         var horas = parseInt(partes[0]); // Convertir las horas a un número entero

            //         // Si el sufijo es 'p.m.' y las horas no son 12, sumar 12 para convertir a formato de 24 horas
            //         if (hora12h.includes('p.m.') && horas !== 12) {
            //             horas += 12;
            //         }
            //         // Si el sufijo es 'a.m.' y las horas son 12, establecer las horas a 0 para convertir a formato de 24 horas
            //         else if (hora12h.includes('a.m.') && horas === 12) {
            //             horas = 0;
            //         }

            //         // Formatear la hora como 'HH:mm' (formato de 24 horas)
            //         var hora24h = horas.toString().padStart(2, '0') + ':' + partes[1];

            //         return hora24h;
            //     }
            // });


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

            function updateIsAprobada(value) {
                if (value == 'Aprobada') {
                    Swal.fire({
                        title: "¿Seguro que deseas aprobar esta solicitud?",
                        showCancelButton: true,
                        confirmButtonText: "Confirmar",
                        cancelButtonText: `Cancelar`,
                        customClass: {
                            confirmButton: 'swal-button-confirm',
                            cancelButton: 'swal-button-cancel'
                        }
                    }).then((result) => {
                        /* Read more about isConfirmed, isDenied below */
                        if (result.isConfirmed) {
                            //document.getElementById('is_aprobada_input').value = 'Aprobada';
                            //Swal.fire("Saved!", "", "success");
                            // Obtener el formulario por su ID
                            document.getElementById('is_aprobada_input').value = 'Aprobada';
                            let form = document.getElementById('solicitudForm');
                            // Enviar el formulario
                            form.submit();
                        }
                    });
                } else if (value == 'No Aprobada') {
                    Swal.fire({
                        title: "¿Seguro que deseas rechazar esta solicitud?",
                        showCancelButton: true,
                        confirmButtonText: "Confirmar",
                        cancelButtonText: `Cancelar`,
                        customClass: {
                            confirmButton: 'swal-button-confirm',
                            cancelButton: 'swal-button-cancel'
                        }
                    }).then((result) => {
                        /* Read more about isConfirmed, isDenied below */
                        if (result.isConfirmed) {
                            //document.getElementById('is_aprobada_input').value = 'Aprobada';
                            //Swal.fire("Saved!", "", "success");
                            // Obtener el formulario por su ID
                            document.getElementById('is_aprobada_input').value = 'No Aprobada';
                            let form = document.getElementById('solicitudForm');
                            // Enviar el formulario
                            form.submit();
                        }
                    });

                }

            }

            document.addEventListener("DOMContentLoaded", function() {
                const inputTiempo = document.querySelector('input[name="tiempo_infusion_min"]');
                const inputVelocidad = document.querySelector('input[name="velocidad_infusion"]');

                function toggleInputState() {
                    if (inputTiempo.value) {
                        inputVelocidad.disabled = true;
                    } else if (inputVelocidad.value) {
                        inputTiempo.disabled = true;
                    } else {
                        inputVelocidad.disabled = false;
                        inputTiempo.disabled = false;
                    }
                }

                // Llamar a la función toggleInputState cuando la página se carga
                toggleInputState();

                // Agregar escuchadores de eventos para cambiar el estado de los campos dinámicamente
                inputTiempo.addEventListener('input', toggleInputState);
                inputVelocidad.addEventListener('input', toggleInputState);
            });

            document.addEventListener('DOMContentLoaded', function() {
                const selectNPT = document.getElementById('npt-select');
                const unidades = document.querySelectorAll('.unidad-span');

                function actualizarUnidades(valorSeleccionado) {

                    unidades.forEach((unidad) => {
                        if (valorSeleccionado === 'ADULT') {
                            unidad.textContent = 'g/día';

                        } else if (valorSeleccionado === 'INF') {
                            unidad.textContent = unidad.getAttribute('data-original-unidad');

                        }
                    });
                }

                // Inicializa las unidades según el valor cargado
                actualizarUnidades(selectNPT.value);

                // Escucha cambios en el select
                selectNPT.addEventListener('change', function() {
                    actualizarUnidades(selectNPT.value);

                });
            });

            document.addEventListener('DOMContentLoaded', function() {
                const selectNPT = document.getElementById('npt-select');
                const unidades = document.querySelectorAll('.unidad-span-electrolitos');

                function actualizarUnidades(valorSeleccionado) {

                    unidades.forEach((unidad) => {
                        if (valorSeleccionado === 'ADULT') {
                            unidad.textContent = 'mEq/día';

                        } else if (valorSeleccionado === 'INF') {
                            unidad.textContent = unidad.getAttribute('data-original-unidad');

                        }
                    });
                }

                // Inicializa las unidades según el valor cargado
                actualizarUnidades(selectNPT.value);

                // Escucha cambios en el select
                selectNPT.addEventListener('change', function() {
                    actualizarUnidades(selectNPT.value);

                });
            });
        </script>
    @endpush

</x-admin-layout>
