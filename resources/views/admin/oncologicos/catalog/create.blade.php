<x-admin-layout>
    <div class="mt-2">
        <h1 class="text-2xl font-medium text-gray-800">Nuevo Medicamento</h1>
    </div>


    <form action="{{ route('admin.oncologicos.medicines.catalog.store') }}" method="POST" enctype="multipart/form-data"
        class="mb-4">
        @csrf

        <div class="flex gap-4">
            <div class="mb-4 w-1/2">
                <label for="denominacion" class="block mb-2 text-sm font-medium text-gray-700">Denominación</label>
                <input type="text" name="denominacion" id="denominacion" required
                    class="w-full p-2 text-sm text-gray-900 bg-gray-50 border border-gray-300 rounded-lg focus:ring focus:ring-blue-200 focus:border-blue-500">
            </div>

            <div class="mb-4 w-1/2">
                <label for="presentacion" class="block mb-2 text-sm font-medium text-gray-700">Presentación</label>
                <input type="text" name="presentacion" id="presentacion" required
                    class="w-full p-2 text-sm text-gray-900 bg-gray-50 border border-gray-300 rounded-lg focus:ring focus:ring-blue-200 focus:border-blue-500">
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
