<x-admin-layout>
    <div class="mt-2 mb-4 flex items-center justify-between">
        <h1 class="text-2xl font-medium text-gray-800">Listas de Medicamentos</h1>

        <a href="{{ route('admin.oncologicos.medicines.create') }}"
            class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            + Nueva Lista
        </a>
    </div>

    <div class="relative overflow-x-auto bg-white rounded-lg shadow">
        <table class="w-full text-sm text-left text-gray-600">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr>
                    <th class="px-6 py-3">ID</th>
                    <th class="px-6 py-3">Nombre de la lista</th>
                    <th class="px-6 py-3">Presentaciones configuradas</th>
                    <th class="px-6 py-3">Tipo de cobro</th>
                    <th class="px-6 py-3">Marcas</th>
                    <th class="px-6 py-3 text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($listas as $lista)
                    <tr class="bg-white border-b last:border-b-0">
                        <td class="px-6 py-2 text-gray-500">
                            {{ $lista->id }}
                        </td>

                        <td class="px-6 py-2">
                            <div class="font-semibold text-gray-800">
                                {{ $lista->name }}
                            </div>
                            @if ($lista->description)
                                <div class="text-xs text-gray-500">
                                    {{ Str::limit($lista->description, 80) }}
                                </div>
                            @endif
                        </td>

                        <td class="px-6 py-2">
                            {{ $lista->presentations->count() }}
                        </td>

                        <td class="px-6 py-2">
                            @if ($lista->charge_by === 'mg')
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                    Cobro por mg
                                </span>
                            @elseif ($lista->charge_by === 'frasco')
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                    Cobro por frasco
                                </span>
                            @else
                                <span class="text-xs text-gray-400">—</span>
                            @endif
                        </td>

                        <td class="px-6 py-2">
                            @if ($lista->active_brands)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800">
                                    Marcas activas
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-500">
                                    Solo genéricos
                                </span>
                            @endif
                        </td>

                        <td class="px-6 py-2 text-center space-x-2">
                            <a href="{{ route('admin.oncologicos.medicines.edit', $lista->id) }}"
                                class="inline-block px-4 py-1 bg-yellow-400 text-white text-sm rounded hover:bg-yellow-500 transition">
                                Editar
                            </a>

                            <form id="delete-form-{{ $lista->id }}"
                                action="{{ route('admin.oncologicos.medicines.destroy', $lista->id) }}"
                                method="POST"
                                class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="button"
                                    class="btn-eliminar px-4 py-1 bg-red-600 text-white text-sm rounded hover:bg-red-700 transition"
                                    data-id="{{ $lista->id }}">
                                    Eliminar
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                            No hay listas registradas todavía.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @push('js')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const botonesEliminar = document.querySelectorAll('.btn-eliminar');

                botonesEliminar.forEach(btn => {
                    btn.addEventListener('click', function() {
                        const id = this.getAttribute('data-id');

                        Swal.fire({
                            title: '¿Eliminar lista?',
                            text: "Esta acción no se puede deshacer.",
                            icon: 'warning',
                            showCancelButton: true,
                            customClass: {
                                confirmButton: 'swal-button-confirm',
                                cancelButton: 'swal-button-cancel'
                            },
                            confirmButtonText: 'Sí, eliminar',
                            cancelButtonText: 'Cancelar'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                document.getElementById('delete-form-' + id).submit();
                            }
                        });
                    });
                });
            });
        </script>
    @endpush
</x-admin-layout>
