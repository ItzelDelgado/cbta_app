<x-admin-layout>
    <div class="mt-2">
        <h1 class="text-2xl font-medium text-gray-800">Listas de Medicamentos</h1>
    </div>

    <div class="flex justify-end mb-4">
        <a href="{{ route('admin.oncologicos.medicines.create') }}"
            class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            + Nueva Lista
        </a>
    </div>

    <div class="relative overflow-x-auto">
        <table class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr>
                    <th class="px-6 py-3">ID</th>
                    <th class="px-6 py-3">Nombre de la Lista</th>
                    <th class="px-6 py-3">Cantidad de Medicamentos</th>
                    <th class="px-6 py-3 text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($listas as $lista)
                    <tr class="bg-white border-b">
                        <td class="px-6 py-2">{{ $lista->id }}</td>
                        <td class="px-6 py-2">{{ $lista->name }}</td>
                        <td class="px-6 py-2">{{ $lista->medicines->count() }}</td>
                        <td class="px-6 py-2 text-center space-x-2">
                            <a href="{{ route('admin.oncologicos.medicines.edit', $lista->id) }}"
                                class="inline-block px-4 py-1 bg-yellow-400 text-white text-sm rounded hover:bg-yellow-500 transition">
                                Editar
                            </a>

                            <form id="delete-form-{{ $lista->id }}"
                                action="{{ route('admin.oncologicos.medicines.destroy', $lista->id) }}" method="POST"
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
                @endforeach
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
                            confirmButtonColor: '#dc2626',
                            cancelButtonColor: '#6b7280',
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
