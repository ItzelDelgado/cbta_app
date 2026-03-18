<div>
    <form wire:submit.prevent="aplicarBusqueda" class="p-4">
        <div class="flex items-center gap-2">
            <input
                type="text"
                wire:model.defer="buscar"
                placeholder="Buscar diluyente..."
                class="border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 focus:ring-opacity-50 w-1/3 p-2"
            >
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-800">
                Buscar
            </button>
        </div>
    </form>

    <table class="min-w-full text-sm">
        <thead class="bg-gray-100 text-gray-600">
            <tr>
                <th class="px-4 py-3 text-left cursor-pointer" wire:click="sortBy('denominacion_generica')">
                    Denominación genérica
                    <span class="{{ $sortField === 'denominacion_generica' ? 'font-bold text-blue-700' : 'text-gray-400' }}">
                        {!! $sortField === 'denominacion_generica' ? ($sortDirection === 'asc' ? '▲' : '▼') : '↕' !!}
                    </span>
                </th>
                <th class="px-4 py-3 text-right">Acciones</th>
            </tr>
        </thead>

        <tbody class="divide-y">
            @forelse ($diluents as $d)
                <tr>
                    <td class="px-4 py-3">{{ $d->denominacion_generica }}</td>

                    <td class="px-4 py-3 text-right space-x-2">
                        <a href="{{ route('admin.oncologicos.diluent_presentations.index', $d) }}"
                            class="px-3 py-1 bg-indigo-600 text-white rounded hover:bg-indigo-700">
                            Presentaciones
                        </a>

                        <a href="{{ route('admin.oncologicos.diluents.edit', $d) }}"
                            class="px-3 py-1 bg-amber-500 text-white rounded hover:bg-amber-600">
                            Editar
                        </a>

                        <form action="{{ route('admin.oncologicos.diluents.destroy', $d) }}"
                            method="POST" class="inline-block form-eliminar-diluent">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700">
                                Eliminar
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td class="px-4 py-6 text-center text-gray-500" colspan="2">
                        No hay diluyentes.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-4 px-4 pb-4">
        {{ $diluents->links() }}
    </div>
</div>
