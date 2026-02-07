<x-admin-layout>
    <div class="mt-2">
        <h1 class="text-2xl font-medium text-gray-800">
            Presentaciones de: {{ $catalog->marca }}
        </h1>
    </div>

    <div class="flex justify-end mb-4">
        <a class="text-white bg-azul-prodifem hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-sm px-5 py-2.5"
            href="{{ route('admin.oncologicos.medicines.catalog.presentations.create', ['catalog' => $catalog->id]) }}">
            <i class="fa-solid fa-plus pr-1"></i> Agregar presentación
        </a>
    </div>

    <div class="relative overflow-x-auto">
        <table class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr>
                    <th class="px-6 py-3">ID</th>
                    <th class="px-6 py-3">Marca</th>
                    <th class="px-6 py-3">Presentación</th>
                    <th class="px-6 py-3">Lote vigente</th>
                    <th class="px-6 py-3">Caducidad</th>
                    <th class="px-6 py-3">Acciones</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($presentations as $p)
                    @php
                        $vigente = $p->batches->first(); // por el with()->latest()
                    @endphp
                    <tr class="bg-white border-b">
                        <td class="px-6 py-4">{{ $p->id }}</td>
                        <td class="px-6 py-4">{{ $p->marca ?? '—' }}</td>

                        <td class="px-6 py-4">
                            {{ $p->presentacion }}
                            @if ($p->contenido_valor && $p->contenido_unidad)
                                — {{ rtrim(rtrim(number_format($p->contenido_valor, 2, '.', ''), '0'), '.') }}
                                {{ $p->contenido_unidad }}
                            @endif
                        </td>

                        <td class="px-6 py-4">{{ $vigente?->lote ?? '—' }}</td>
                        <td class="px-6 py-4">{{ $vigente?->caducidad?->format('Y-m-d') ?? '—' }}</td>

                        <td class="px-6 py-4">
                            <div class="flex space-x-2">
                                <a href="{{ route('admin.oncologicos.medicines.catalog.presentations.edit', [$catalog->id, $p->id]) }}"
                                    class="inline-block bg-yellow-400 hover:bg-yellow-500 text-white text-xs font-semibold px-3 py-1 rounded shadow">
                                    <i class="fas fa-edit mr-1"></i> Editar
                                </a>

                                <form
                                    action="{{ route('admin.oncologicos.medicines.catalog.presentations.destroy', [$catalog->id, $p->id]) }}"
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
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                            No hay presentaciones registradas.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-admin-layout>
