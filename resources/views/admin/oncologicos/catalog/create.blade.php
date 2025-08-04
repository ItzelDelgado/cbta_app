<x-admin-layout>
    <div class="mt-2">
        <h1 class="text-2xl font-medium text-gray-800">Nuevo Medicamento</h1>
    </div>


    <form action="{{ route('admin.oncologicos.medicines.catalog.store') }}" method="POST" enctype="multipart/form-data"
        class="mb-4">
        @csrf

        <div class="flex gap-4">
            <div class="mb-4 w-1/4">
                <label for="denominacion" class="block mb-2 text-sm font-medium text-gray-700">Denominación</label>
                <input type="text" name="denominacion" id="denominacion" required
                    class="w-full p-2 text-sm text-gray-900 bg-gray-50 border border-gray-300 rounded-lg focus:ring focus:ring-blue-200 focus:border-blue-500">
            </div>

            <div class="mb-4 w-1/4">
                <label for="presentacion" class="block mb-2 text-sm font-medium text-gray-700">Presentación</label>
                <input type="text" name="presentacion" id="presentacion" required
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
                    Concentración mínima (mOsm/mL)
                </label>
                <input type="number" name="conc_min" id="conc_min"
                    value="{{ old('conc_min', $medicamento->conc_min ?? '') }}" step="0.01" min="0"
                    class="w-full p-2 text-sm text-gray-900 bg-gray-50 border border-gray-300 rounded-lg focus:ring focus:ring-blue-200 focus:border-blue-500">
            </div>

            <div class="mb-4 w-1/4">
                <label for="conc_max" class="block mb-2 text-sm font-medium text-gray-700">
                    Concentración máxima (mOsm/mL)
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

        <div class="flex gap-4">
            <!-- Checklist de Diluyentes -->
            <div class="mb-4">
                <label class="block mb-2 text-sm font-medium text-gray-700">Diluyentes</label>
                <div class="grid grid-cols-1 gap-2">
                    @foreach ($diluents as $diluent)
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="diluents[]" value="{{ $diluent->id }}"
                                class="form-checkbox text-blue-600">
                            <span class="ml-2">{{ $diluent->name }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <!-- Checklist de Vías de Administración -->
            <div class="mb-4">
                <label class="block mb-2 text-sm font-medium text-gray-700">Vías de Administración</label>
                <div class="grid grid-cols-1 gap-2">
                    @foreach ($routes as $route)
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="routes[]" value="{{ $route->id }}"
                                class="form-checkbox text-blue-600">
                            <span class="ml-2">{{ $route->name }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
        </div>




        <div class="mt-4 text-right">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded">
                Guardar lista de medicamentos
            </button>
        </div>

    </form>

    @push('js')
    @endpush
</x-admin-layout>
