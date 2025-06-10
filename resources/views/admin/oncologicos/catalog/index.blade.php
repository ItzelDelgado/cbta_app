<x-admin-layout>
    <div class="mt-2">
        <h1 class="text-2xl font-medium text-gray-800">Medicamentos</h1>
    </div>

    <div class="flex justify-end mb-4">
        <a class="text-white bg-azul-prodifem hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-sm px-5 py-2.5 text-center me-2 mb-2 dark:bg-blue-600 dark:hover:bg-azul-prodifem dark:focus:ring-blue-800"
            href="{{ route('admin.oncologicos.medicines.catalog.create') }}"><i class="fa-solid fa-plus pr-1"></i>Agregar
            nuevo Medicamento</a>
    </div>

    <div class="relative overflow-x-auto">
        <table id="medicinesTable" class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-6 py-3">ID</th>
                    <th scope="col" class="px-6 py-3">Denominación</th>
                    <th scope="col" class="px-6 py-3">Presentación</th>
                    <th scope="col" class="px-6 py-3">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($medicamentos as $med)
                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                        <td class="px-6 py-4">{{ $med->id }}</td>
                        <td class="px-6 py-4">{{ $med->denominacion }}</td>
                        <td class="px-6 py-4">{{ $med->presentacion }}</td>
                        <td class="px-6 py-4">
                            <div class="flex space-x-2">
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
                        <td colspan="4" class="px-6 py-4 text-center text-gray-500">No hay medicamentos registrados.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>


    </div>

    @push('js')
        @if (session('success'))
            <script>
                Swal.fire({
                    icon: 'success',
                    title: '¡Éxito!',
                    text: '{{ session('success') }}',
                    confirmButtonColor: '#2563eb'
                });
            </script>
        @endif

        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const forms = document.querySelectorAll('.form-eliminar');

                forms.forEach(form => {
                    form.addEventListener('submit', function(e) {
                        e.preventDefault(); // evita el submit inmediato

                        Swal.fire({
                            title: '¿Estás seguro?',
                            text: "Este medicamento será deshabilitado.",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Sí, deshabilitar',
                            cancelButtonText: 'Cancelar'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                form.submit();
                            }
                        });
                    });
                });
            });
        </script>
    @endpush
</x-admin-layout>
