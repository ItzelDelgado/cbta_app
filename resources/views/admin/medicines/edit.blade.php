<x-admin-layout>
    <div class="mt-2 mb-4">
        <h1 class="text-2xl font-medium text-gray-800">Editar Medicamento</h1>
    </div>
    <form action="{{ route('admin.medicines.update', $medicine) }}" method="POST"
        class="bg-white rounded-lg p-6 shadow-lg lg:w-[40rem] mx-auto">
        @csrf
        @method('PUT')
        <x-validation-errors class="mb-4" />

        <div class="mb-4">
            <x-label class="mb-2">
                Denominación genérica
            </x-label>
            <x-input value="{{ old('denominacion_generica', $medicine->denominacion_generica) }}"
                name="denominacion_generica" class="w-full"
                placeholder="Escriba la denominacion genérica del medicamento" />
        </div>
        <div class="mb-4">
            <x-label class="mb-2">
                Denominación comercial
            </x-label>
            <x-input value="{{ old('denominacion_comercial', $medicine->denominacion_comercial) }}"
                name="denominacion_comercial" class="w-full"
                placeholder="Escriba la denominacion comercial del medicamento" />
        </div>

        <div class="mb-4">
            <x-label class="mb-2">
                Descripción
            </x-label>
            <x-select class="w-full" name="input_id">

                <option @selected(old('input_id') == $medicine->input['id']) value="{{ $medicine->input['id'] }}">
                    {{ $medicine->input['description'] }}</option>
            </x-select>
        </div>

        <div class="mb-4">
            <x-label class="mb-2">
                Presentación
            </x-label>
            <x-input value="{{ old('presentacion', $medicine->presentacion) }}" name="presentacion"
                class="w-full" placeholder="Escriba la presentación del medicamento" />
        </div>

        <div class="mb-4">
            <x-label class="mb-2">
                ML por presentación
            </x-label>
            <x-input value="{{ old('presentacion_ml', $medicine->presentacion_ml) }}" name="presentacion_ml" class="w-full"
                placeholder="Escriba la presentación del medicamento" />
        </div>



        <div class="mb-4">
            <x-label class="mb-2">
                Osmolaridad
            </x-label>
            <x-input type="number" value="{{ old('osmolaridad', $medicine->osmolaridad) }}" name="osmolaridad"
                class="w-full" placeholder="Escriba la osmolaridad" />
        </div>

        <div class="mb-4">
            <x-label class="mb-2">
                Precio por ml
            </x-label>
            <x-input type="number" value="{{ old('precio_ml', $medicine->precio_ml) }}" step="0.001" name="precio_ml"
                class="w-full" placeholder="Escriba el precio por ml del medicamento" />
        </div>
        <div class="mb-4">
            <x-label class="mb-2">
                Categoría
            </x-label>
            <x-select class="w-full" name="category_id">
                @foreach ($categories as $category)
                    <option @selected(old('category_id', $medicine->category_id) == $category->id) value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </x-select>
        </div>
        <!-- Campo Lote -->
        <div class="mb-4">
            <x-label class="mb-2">
                Lote
            </x-label>
            <x-input type="text" value="{{ old('lote', $medicine->lote) }}" name="lote" class="w-full"
                placeholder="Escriba el lote del medicamento" />
        </div>

        <!-- Campo Caducidad -->
        <div class="mb-4">
            <x-label class="mb-2">
                Caducidad
            </x-label>
            <x-input type="date" value="{{ old('caducidad', $medicine->caducidad) }}" name="caducidad"
                class="w-full" />
        </div>
        <div class="flex justify-end">
            <x-button>
                Actualizar medicamento
            </x-button>
        </div>
    </form>
</x-admin-layout>
