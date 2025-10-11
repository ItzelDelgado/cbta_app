<x-admin-layout>
    <div class="mt-2">
        <h1 class="text-2xl font-medium text-gray-800">Editar Medicamento</h1>
    </div>

    <form action="{{ route('admin.oncologicos.medicines.catalog.update', $medicamento->id) }}" method="POST"
        class="mb-4">
        @csrf
        @method('PUT')

        {{-- Errores --}}
        @if ($errors->any())
            <div class="mb-4 p-3 rounded bg-red-100 text-red-800">
                <div class="font-semibold mb-1">Revisa los campos:</div>
                <ul class="list-disc list-inside text-sm">
                    @foreach ($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="flex gap-4">
            <div class="mb-4 w-1/4">
                <label for="denominacion" class="block mb-2 text-sm font-medium text-gray-700">Denominación</label>
                <input type="text" name="denominacion" id="denominacion"
                    value="{{ old('denominacion', $medicamento->denominacion) }}" required
                    class="w-full p-2 text-sm text-gray-900 bg-gray-50 border border-gray-300 rounded-lg focus:ring focus:ring-blue-200 focus:border-blue-500">
            </div>

            <div class="mb-4 w-1/4">
                <label for="denominacion_comercial" class="block mb-2 text-sm font-medium text-gray-700">Denominación
                    comercial</label>
                <input type="text" name="denominacion_comercial" id="denominacion_comercial"
                    value="{{ old('denominacion_comercial', $medicamento->denominacion_comercial) }}" required
                    class="w-full p-2 text-sm text-gray-900 bg-gray-50 border border-gray-300 rounded-lg focus:ring focus:ring-blue-200 focus:border-blue-500">
            </div>

            <div class="mb-4 w-1/4">
                <label for="presentacion" class="block mb-2 text-sm font-medium text-gray-700">Presentación</label>
                <input type="text" name="presentacion" id="presentacion"
                    value="{{ old('presentacion', $medicamento->presentacion) }}" required
                    class="w-full p-2 text-sm text-gray-900 bg-gray-50 border border-gray-300 rounded-lg focus:ring focus:ring-blue-200 focus:border-blue-500">
            </div>

            <div class="mb-4 w-1/4">
                <label for="cantidad_medicamento" class="block mb-2 text-sm font-medium text-gray-700">
                    Cantidad de medicamento (mg)
                </label>
                <input type="number" name="cantidad_medicamento" id="cantidad_medicamento"
                    value="{{ old('cantidad_medicamento', $medicamento->cantidad_medicamento ?? '') }}" step="0.01"
                    min="0"
                    class="w-full p-2 text-sm text-gray-900 bg-gray-50 border border-gray-300 rounded-lg focus:ring focus:ring-blue-200 focus:border-blue-500">
            </div>

            <div class="mb-4 w-1/4">
                <label for="volumen_diluyente" class="block mb-2 text-sm font-medium text-gray-700">
                    Volumen del diluyente (mL)
                </label>
                <input type="number" name="volumen_diluyente" id="volumen_diluyente"
                    value="{{ old('volumen_diluyente', $medicamento->volumen_diluyente ?? '') }}" step="0.01"
                    min="0"
                    class="w-full p-2 text-sm text-gray-900 bg-gray-50 border border-gray-300 rounded-lg focus:ring focus:ring-blue-200 focus:border-blue-500">
            </div>
        </div>

        <div class="flex gap-4">
            <div class="mb-4 w-1/4">
                <label for="conc_min" class="block mb-2 text-sm font-medium text-gray-700">
                    Concentración mínima (mg/mL)
                </label>
                <input type="number" name="conc_min" id="conc_min"
                    value="{{ old('conc_min', $medicamento->conc_min ?? '') }}" step="0.01" min="0"
                    class="w-full p-2 text-sm text-gray-900 bg-gray-50 border border-gray-300 rounded-lg focus:ring focus:ring-blue-200 focus:border-blue-500">
            </div>

            <div class="mb-4 w-1/4">
                <label for="conc_max" class="block mb-2 text-sm font-medium text-gray-700">
                    Concentración máxima (mg/mL)
                </label>
                <input type="number" name="conc_max" id="conc_max"
                    value="{{ old('conc_max', $medicamento->conc_max ?? '') }}" step="0.01" min="0"
                    class="w-full p-2 text-sm text-gray-900 bg-gray-50 border border-gray-300 rounded-lg focus:ring focus:ring-blue-200 focus:border-blue-500">
            </div>

            {{-- Requiere infusor --}}
            <div class="mb-4 w-1/4">
                <label class="block mb-2 text-sm font-medium text-gray-700">
                    ¿Requiere infusor?
                </label>

                {{-- Importante: este hidden envía 0 cuando el checkbox no se marca --}}
                <input type="hidden" name="requires_infusor" value="0">

                <label class="inline-flex items-center">
                    <input type="checkbox" name="requires_infusor" value="1" class="form-checkbox text-blue-600"
                        {{ old('requires_infusor', (int) ($medicamento->requires_infusor ?? 0)) ? 'checked' : '' }}>
                    <span class="ml-2 text-sm text-gray-700">Sí, este medicamento requiere infusor</span>
                </label>

                @error('requires_infusor')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>


            <div class="mb-4 w-full">
                <label for="legend" class="block mb-2 text-sm font-medium text-gray-700">
                    Leyenda de protección
                </label>
                <textarea name="legend" id="legend" rows="2"
                    placeholder="Ej. No agitar. Conservar a temperatura ambiente o refrigerar de 2-8°C"
                    class="w-full p-2 text-sm text-gray-900 bg-gray-50 border border-gray-300 rounded-lg focus:ring focus:ring-blue-200 focus:border-blue-500">{{ old('legend', $medicamento->legend ?? '') }}</textarea>
            </div>
        </div>

        <div class="flex gap-8">
            <div class="flex gap-4">
                {{-- Diluyentes: SOLO genéricos --}}
                <div class="mb-4">
                    <label class="block mb-2 text-sm font-medium text-gray-700">Diluyentes</label>
                    <div class="grid grid-cols-1 gap-2">
                        @php $oldDils = old('diluents', $selectedDiluents); @endphp
                        @foreach ($diluents as $diluent)
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="diluents[]" value="{{ $diluent->id }}"
                                    class="form-checkbox text-blue-600"
                                    {{ in_array($diluent->id, $oldDils) ? 'checked' : '' }}>
                                <span class="ml-2">{{ $diluent->denominacion_generica }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- Vías de administración --}}
                <div class="mb-4">
                    <label class="block mb-2 text-sm font-medium text-gray-700">Vías de administración</label>
                    <div class="grid grid-cols-1 gap-2">
                        @php $oldRoutes = old('routes', $selectedRoutes); @endphp
                        @foreach ($routes as $route)
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="routes[]" value="{{ $route->id }}"
                                    class="form-checkbox text-blue-600"
                                    {{ in_array($route->id, $oldRoutes) ? 'checked' : '' }}>
                                <span class="ml-2">{{ $route->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="flex gap-8 w-[30rem]">
                <div class="mb-4 w-full">
                    <label for="lote" class="block mb-2 text-sm font-medium text-gray-700">Lote</label>
                    <input type="text" name="lote" id="lote"
                        value="{{ old('lote', $medicamento->lote ?? '') }}"
                        class="w-full p-2 text-sm text-gray-900 bg-gray-50 border border-gray-300 rounded-lg
                                  focus:ring focus:ring-blue-200 focus:border-blue-500">
                    @error('lote')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4 w-full">
                    <label for="caducidad" class="block mb-2 text-sm font-medium text-gray-700">Fecha de
                        caducidad</label>
                    <input type="date" name="caducidad" id="caducidad"
                        value="{{ old('caducidad', isset($medicamento->caducidad) ? \Carbon\Carbon::parse($medicamento->caducidad)->format('Y-m-d') : '') }}"
                        class="w-full p-2 text-sm text-gray-900 bg-gray-50 border border-gray-300 rounded-lg
                                  focus:ring focus:ring-blue-200 focus:border-blue-500">
                    @error('caducidad')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <div class="mt-4 text-right">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded">
                Actualizar medicamento
            </button>
        </div>
    </form>

    @push('js')
        @if (session('success'))
            <script>
                Swal.fire({
                    icon: 'success',
                    title: '¡Éxito!',
                    text: "{{ session('success') }}",
                });
            </script>
        @endif
    @endpush
</x-admin-layout>
