<x-admin-layout>
    <p>Editar Usario</p>
    <div class="mt-2 mb-4">
        <h1 class="text-2xl font-medium text-gray-800">Agregar nuevo Usuario</h1>
    </div>
    <form action="{{ route('admin.users.update', $user) }}" method="POST" class="bg-white rounded-lg p-6 shadow-lg">
        @csrf
        @method('PUT')
        <x-validation-errors class="mb-4" />

        <div class="mb-4">
            <x-label class="mb-2">
                Nombre
            </x-label>
            <x-input value="{{old('name',$user->name)}}" name="name" class="w-full" placeholder="Escriba el nombre del usuario" />
        </div>
        <div class="mb-4">
            <x-label class="mb-2">
                Apellidos
            </x-label>
            <x-input value="{{old('lastname',$user->lastname)}}" name="lastname" class="w-full" placeholder="Escriba los apellido del usuario" />
        </div>
        <div class="mb-4">
            <x-label class="mb-2">
                Nombre de usuario
            </x-label>
            <x-input value="{{old('username',$user->username)}}" name="username" class="w-full" placeholder="Escriba el nombre del usuario" />
        </div>
        <div class="mb-4">
            <x-label class="mb-2">
                Contraseña
            </x-label>
            <x-input type="password" value="{{old('password',$user->password)}}" name="password" class="w-full" placeholder="Escriba la contraseña del usuario" />
        </div>
        <div class="mb-4">
            <x-label class="mb-2">
                Hospital
            </x-label>
            <x-select class="w-full" name="hospital_id">
                @foreach ($hospitals as $hospital)
                    <option @selected(old('hospital_id', $user->hospital_id) == $hospital->id) value="{{ $hospital->id }}">{{ $hospital->name }}</option>
                @endforeach
            </x-select>
        </div>
        <div class="flex justify-end">
            <x-button>
                Editar usuario
            </x-button>
        </div>
    </form>
</x-admin-layout>
