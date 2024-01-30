<x-admin-layout>
    <div class="mt-2 mb-4">
        <h1 class="text-2xl font-medium text-gray-800">Agregar nuevo hospital</h1>
    </div>
    <form action="{{ route('admin.hospitals.store') }}" method="POST" class="bg-white rounded-lg p-6 shadow-lg">
        @csrf

        <x-validation-errors class="mb-4" />

        <div class="mb-4">
            <x-label class="mb-2">
                Nombre
            </x-label>
            <x-input value="{{old('name')}}" name="name" class="w-full" placeholder="Escriba el nombre del hospital" />
        </div>
        <div class="mb-4">
            <x-label class="mb-2">
                Dirección
            </x-label>
            <x-input value="{{old('adress')}}" name="adress" class="w-full" placeholder="Tlacotalpan 59, Col. Roma Sur , Cuauhtemoc, CDMX, 06760" />
        </div>
        <div class="flex justify-end">
            <x-button>
                Crear hospital
            </x-button>
        </div>
    </form>
</x-admin-layout>
