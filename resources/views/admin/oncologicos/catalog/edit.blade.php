<x-admin-layout>
    <div class="mt-2">
        <h1 class="text-2xl font-medium text-gray-800">Editar Medicamento</h1>
    </div>

    <form action="{{ route('admin.oncologicos.medicines.catalog.update', $medicamento->id) }}" method="POST"
        class="mb-4">
        @csrf
        @method('PUT')

        <div class="flex gap-4">
            <div class="mb-4 w-1/2">
                <label for="denominacion" class="block mb-2 text-sm font-medium text-gray-700">Denominación</label>
                <input type="text" name="denominacion" id="denominacion"
                    value="{{ old('denominacion', $medicamento->denominacion) }}" required
                    class="w-full p-2 text-sm text-gray-900 bg-gray-50 border border-gray-300 rounded-lg focus:ring focus:ring-blue-200 focus:border-blue-500">
            </div>

            <div class="mb-4 w-1/2">
                <label for="presentacion" class="block mb-2 text-sm font-medium text-gray-700">Presentación</label>
                <input type="text" name="presentacion" id="presentacion"
                    value="{{ old('presentacion', $medicamento->presentacion) }}" required
                    class="w-full p-2 text-sm text-gray-900 bg-gray-50 border border-gray-300 rounded-lg focus:ring focus:ring-blue-200 focus:border-blue-500">
            </div>
        </div>

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
                    confirmButtonColor: '#2563eb'
                });
            </script>
        @endif
    @endpush
</x-admin-layout>
