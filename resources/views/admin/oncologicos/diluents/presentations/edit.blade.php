<x-admin-layout>
    <div class="mt-2 max-w-3xl">
        <h1 class="text-2xl font-semibold text-gray-800 mb-4">
            Editar presentación — {{ $diluent->denominacion_generica ?? '—' }}
        </h1>

        @if ($errors->any())
            <div class="mb-4 p-3 rounded bg-red-100 text-red-800">
                <ul class="list-disc ml-5">
                    @foreach ($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.oncologicos.diluent_presentations.update', [$diluent, $presentation]) }}"
            method="POST" class="bg-white p-4 rounded shadow space-y-4">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Presentación</label>
                    <input type="text" name="presentacion"
                        value="{{ old('presentacion', $presentation->presentacion) }}" required
                        class="w-full px-3 py-2 border rounded focus:ring focus:ring-blue-200 focus:outline-none">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Volumen (mL)</label>
                    <input type="number" step="0.01" min="0.01" name="volume_ml"
                        value="{{ old('volume_ml', $presentation->volume_ml) }}" required
                        class="w-full px-3 py-2 border rounded focus:ring focus:ring-blue-200 focus:outline-none">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Denominación comercial</label>
                    <input type="text" name="denominacion_comercial"
                        value="{{ old('denominacion_comercial', $presentation->denominacion_comercial) }}"
                        class="w-full px-3 py-2 border rounded focus:ring focus:ring-blue-200 focus:outline-none">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Lote</label>
                    <input type="text" name="lote" value="{{ old('lote', $presentation->lote) }}"
                        class="w-full px-3 py-2 border rounded focus:ring focus:ring-blue-200 focus:outline-none">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Caducidad</label>
                    <input type="date" name="caducidad"
                        value="{{ old('caducidad', optional($presentation->caducidad)->format('Y-m-d')) }}"
                        class="w-full px-3 py-2 border rounded focus:ring focus:ring-blue-200 focus:outline-none">
                </div>


                <div class="flex items-center gap-3 mt-6">
                    <input type="checkbox" id="is_active" name="is_active" value="1" class="h-4 w-4"
                        {{ old('is_active', $presentation->is_active) ? 'checked' : '' }}>
                    <label for="is_active" class="text-sm text-gray-700">Activo</label>
                </div>
            </div>

            <div class="text-right">
                <a href="{{ route('admin.oncologicos.diluent_presentations.index', $diluent) }}"
                    class="px-4 py-2 mr-2 border rounded text-gray-700 hover:bg-gray-50">Cancelar</a>
                <button type="submit"
                    class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Actualizar</button>
            </div>
        </form>
    </div>
</x-admin-layout>
