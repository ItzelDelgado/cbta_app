<x-admin-layout>
    <div class="mt-2 mb-4">
        <h1 class="text-2xl font-medium text-gray-800">Agregar nuevo Cliente</h1>
    </div>

    <form action="{{ route('admin.clientes.store') }}" method="POST" class="bg-white rounded-lg p-6 shadow-lg">
        @csrf

        <x-validation-errors class="mb-4" />

        <div class="mb-4">
            <x-label class="mb-2">
                Nombre
            </x-label>
            <x-input value="{{ old('nombre') }}" name="nombre" class="w-full"
                placeholder="Escriba el nombre del cliente" />
        </div>

        <div class="mb-4">
            <x-label class="mb-2">
                Razón social
            </x-label>
            <x-input value="{{ old('razon_social') }}" name="razon_social" class="w-full"
                placeholder="Escriba la razón social del cliente" />
        </div>

        <div class="flex justify-end">
            <x-button>
                Crear cliente
            </x-button>
        </div>
    </form>
</x-admin-layout>
