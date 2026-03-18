<x-admin-layout>
    <div class="mt-2 mb-4">
        <h1 class="text-2xl font-medium text-gray-800">Editar Cliente</h1>
    </div>

    <form action="{{ route('admin.clientes.update', $cliente) }}" method="POST" class="bg-white rounded-lg p-6 shadow-lg">
        @csrf
        @method('PUT')

        <x-validation-errors class="mb-4" />

        <div class="mb-4">
            <x-label class="mb-2">
                Nombre
            </x-label>
            <x-input value="{{ old('nombre', $cliente->nombre) }}" name="nombre" class="w-full"
                placeholder="Escriba el nombre del cliente" />
        </div>

        <div class="mb-4">
            <x-label class="mb-2">
                Razón social
            </x-label>
            <x-input value="{{ old('razon_social', $cliente->razon_social) }}" name="razon_social" class="w-full"
                placeholder="Escriba la razón social del cliente" />
        </div>

        <div class="flex justify-end gap-2">
            <a href="{{ route('admin.clientes.create') }}"
                class="px-4 py-2 border rounded-md text-gray-700 hover:bg-gray-100">
                Cancelar
            </a>

            <x-button>
                Actualizar cliente
            </x-button>
        </div>
    </form>
</x-admin-layout>
