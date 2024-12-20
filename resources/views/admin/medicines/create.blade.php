<x-admin-layout>
    <div class="mt-2 mb-4">
        <h1 class="text-2xl font-medium text-gray-800">Agregar nueva medicina</h1>
    </div>
    <form action="{{ route('admin.medicines.store') }}" method="POST" class="bg-white rounded-lg p-6 shadow-lg lg:w-[40rem] mx-auto">
        @csrf

        <x-validation-errors class="mb-4" />

        <div class="mb-4">
            <x-label class="mb-2">
                Denominación genérica
            </x-label>
            <x-input value="{{ old('denominacion_generica') }}" name="denominacion_generica" class="w-full"
                placeholder="Escriba la denominacion genérica del medicamento" />
        </div>
        <div class="mb-4">
            <x-label class="mb-2">
                Denominación comercial
            </x-label>
            <x-input value="{{ old('denominacion_comercial') }}" name="denominacion_comercial" class="w-full"
                placeholder="Escriba la denominacion comercial del medicamento" />
        </div>

        <div class="mb-4">
            <x-label class="mb-2">
                Descripción
            </x-label>
            <x-select class="w-full" name="input_id">
                @foreach ($inputs as $input)
                    <option @selected(old('input_id') == $input->id) value="{{ $input->id }}">{{ $input->description }}</option>
                @endforeach
            </x-select>
        </div>

        <div class="mb-4">
            <x-label class="mb-2">
                Presentación
            </x-label>
            <x-input value="{{ old('presentacion') }}" name="presentacion" class="w-full"
                placeholder="Escriba la presentación del medicamento" />
        </div>


        <div class="mb-4">
            <x-label class="mb-2">
                ML por presentación
            </x-label>
            <x-input value="{{ old('presentacion_ml') }}" name="presentacion_ml" class="w-full"
                placeholder="Escriba la presentación del medicamento" />
        </div>

        <div class="mb-4">
            <x-label class="mb-2">
                Osmolaridad
            </x-label>
            <x-input type="number" step="0.001" min="0" value="{{ old('osmolaridad') }}" name="osmolaridad"
                class="w-full" placeholder="Escriba la osmolaridad" />
        </div>

        <div class="mb-4">
            <x-label class="mb-2">
                Precio por ml
            </x-label>
            <x-input type="number" step="0.001" min="0" value="{{ old('precio_ml') }}" name="precio_ml"
                class="w-full" placeholder="Escriba el precio por ml del medicamento" />
        </div>
        <div class="mb-4">
            <x-label class="mb-2">
                Categoría
            </x-label>
            <x-select class="w-full" name="category_id">
                @foreach ($categories as $category)
                    <option @selected(old('category_id') == $category->id) value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </x-select>
        </div>
        <div class="mb-4">
            <x-label class="mb-2">
                Lote
            </x-label>
            <x-input value="{{ old('lote') }}" name="lote" class="w-full"
                placeholder="Escriba el lote del medicamento" />
        </div>
        <!-- Campo Caducidad -->
        <div class="mb-4">
            <x-label class="mb-2">
                Caducidad
            </x-label>
            <x-input type="date" value="{{ old('caducidad') }}" name="caducidad" class="w-full" />
        </div>
        <div class="flex justify-end">
            <x-button>
                Crear medicamento
            </x-button>
        </div>
    </form>
</x-admin-layout>
