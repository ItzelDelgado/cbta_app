<x-admin-layout>
    <div class="mt-2">
        <h1 class="text-2xl font-medium text-gray-800">Editar Medicamento</h1>
    </div>

    <form action="{{ route('admin.oncologicos.medicines.catalog.update', $medicamento->id) }}" method="POST"
        class="mb-4">
        @csrf
        @method('PUT')

        <div class="flex gap-4">
            <div class="mb-4 w-1/4">
                <label for="denominacion" class="block mb-2 text-sm font-medium text-gray-700">Denominación</label>
                <input type="text" name="denominacion" id="denominacion"
                    value="{{ old('denominacion', $medicamento->denominacion) }}" required
                    class="w-full p-2 text-sm text-gray-900 bg-gray-50 border border-gray-300 rounded-lg focus:ring focus:ring-blue-200 focus:border-blue-500">
            </div>
            <div class="mb-4 w-1/4">
                <label for="denominacion_comercial" class="block mb-2 text-sm font-medium text-gray-700">Denominación comercial</label>
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
                    Volumen del diluyente (ml)
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
                <div class="mb-4">
                    <label class="block mb-2 text-sm font-medium text-gray-700">Diluyentes</label>
                    <div class="grid grid-cols-1 gap-2">
                        @foreach ($diluents as $diluent)
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="diluents[]" value="{{ $diluent->id }}"
                                    @checked(in_array($diluent->id, old('diluents', $selectedDiluents)))>
                                <span class="ml-2">{{ $diluent->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block mb-2 text-sm font-medium text-gray-700">Vías de administración</label>
                    <div class="grid grid-cols-1 gap-2">
                        @foreach ($routes as $route)
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="routes[]" value="{{ $route->id }}"
                                    @checked(in_array($route->id, old('routes', $selectedRoutes)))>
                                <span class="ml-2">{{ $route->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="flex gap-8 w-[30rem]">
                <div class="mb-4 w-full">
                    <label for="lote" class="block mb-2 text-sm font-medium text-gray-700">
                        Lote
                    </label>
                    <input type="text" name="lote" id="lote"
                        value="{{ old('lote', $medicamento->lote ?? '') }}"
                        class="w-full p-2 text-sm text-gray-900 bg-gray-50 border border-gray-300 rounded-lg
                   focus:ring focus:ring-blue-200 focus:border-blue-500">
                    @error('lote')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4 w-full">
                    <label for="caducidad" class="block mb-2 text-sm font-medium text-gray-700">
                        Fecha de caducidad
                    </label>
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
                    text: '{{ session('success') }}',
                    customClass: {
                        confirmButton: 'swal-button-confirm',
                        cancelButton: 'swal-button-cancel'
                    }
                });
            </script>
        @endif
    @endpush
</x-admin-layout>
