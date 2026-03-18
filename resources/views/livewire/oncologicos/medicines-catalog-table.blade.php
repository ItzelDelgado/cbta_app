<div>
    <form wire:submit.prevent="aplicarBusqueda">
        <div class="mb-4 flex items-center gap-2">
            <input
                type="text"
                wire:model.defer="buscar"
                placeholder="Buscar por ID o denominación..."
                class="border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 focus:ring-opacity-50 w-1/3 p-2"
            >
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-800">
                Buscar
            </button>
        </div>
    </form>

    <div class="relative overflow-x-auto">
        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th class="px-6 py-3 cursor-pointer" wire:click="sortBy('id')">
                        ID
                        <span class="{{ $sortField === 'id' ? 'font-bold text-blue-700' : 'text-gray-400' }}">
                            {!! $sortField === 'id' ? ($sortDirection === 'asc' ? '▲' : '▼') : '↕' !!}
                        </span>
                    </th>

                    <th class="px-6 py-3 cursor-pointer" wire:click="sortBy('denominacion')">
                        Denominación
                        <span class="{{ $sortField === 'denominacion' ? 'font-bold text-blue-700' : 'text-gray-400' }}">
                            {!! $sortField === 'denominacion' ? ($sortDirection === 'asc' ? '▲' : '▼') : '↕' !!}
                        </span>
                    </th>

                    <th class="px-6 py-3">
                        Acciones
                    </th>
                </tr>
            </thead>

            <tbody>
                @forelse ($medicamentos as $med)
                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                        <td class="px-6 py-4">{{ $med->id }}</td>
                        <td class="px-6 py-4">{{ $med->denominacion }}</td>

                        <td class="px-6 py-4">
                            <div class="flex flex-wrap gap-2">
                                <a href="{{ route('admin.oncologicos.medicines.catalog.presentations.index', ['catalog' => $med->id]) }}"
                                    class="inline-block bg-blue-400 hover:bg-blue-500 text-white text-xs font-semibold px-3 py-1 rounded shadow">
                                    <i class="fas fa-edit mr-1"></i> Presentaciones
                                </a>

                                <a href="{{ route('admin.oncologicos.medicines.catalog.edit', $med->id) }}"
                                    class="inline-block bg-yellow-400 hover:bg-yellow-500 text-white text-xs font-semibold px-3 py-1 rounded shadow">
                                    <i class="fas fa-edit mr-1"></i> Editar
                                </a>

                                <form action="{{ route('admin.oncologicos.medicines.catalog.destroy', $med->id) }}"
                                    method="POST" class="inline-block form-eliminar">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="bg-red-500 hover:bg-red-600 text-white text-xs font-semibold px-3 py-1 rounded shadow eliminar-btn">
                                        <i class="fas fa-trash-alt mr-1"></i> Deshabilitar
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-6 py-4 text-center text-gray-500">
                            No hay medicamentos registrados.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $medicamentos->links() }}
    </div>
</div>
