<x-admin-layout>

    <div class="mt-2">
        <h1 class="text-2xl font-medium text-gray-800">Lista de Medicamentos</h1>
    </div>

    <div class="flex justify-end mb-4">
        <a class="text-white bg-azul-prodifem hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-sm px-5 py-2.5 text-center me-2 mb-2 dark:bg-blue-600 dark:hover:bg-azul-prodifem dark:focus:ring-blue-800"
            href="{{ route('admin.medicines.create') }}"><i class="fa-solid fa-plus pr-1"></i> Agregar</a>
    </div>

    <div class="relative overflow-x-auto">
        <table id="medicinesTable" class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-6 py-3">ID</th>
                    <th scope="col" class="px-6 py-3">Denominación Genérica</th>
                    <th scope="col" class="px-6 py-3">Denominación Comercial</th>
                    <th scope="col" class="px-6 py-3">Descripción</th>
                    <th scope="col" class="px-6 py-3">Presentación</th>
                    <th scope="col" class="px-6 py-3">Precio/ml</th>
                    <th scope="col" class="px-6 py-3">Osmolaridad</th>
                    <th scope="col" class="px-6 py-3">Categoría</th>
                    <th scope="col" class="px-6 py-3"></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($medicines as $medicine)
                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                        <th scope="row"
                            class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            {{ $medicine->id }}
                        </th>
                        <td class="px-6 py-4">{{ $medicine->denominacion_generica }}</td>
                        <td class="px-6 py-4">{{ $medicine->denominacion_comercial }}</td>
                        <td class="px-6 py-4">{{ $medicine->input['description'] }}</td>
                        <td class="px-6 py-4">{{ $medicine->presentacion_ml }}</td>
                        <td class="px-6 py-4">{{ $medicine->precio_ml }}</td>
                        <td class="px-6 py-4">{{ $medicine->osmolaridad }}</td>
                        <td class="px-6 py-4">{{ $medicine->category['name'] }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <a class="text-white bg-azul-prodifem hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-sm px-5 py-2.5 text-center me-2 mb-2 dark:bg-blue-600 dark:hover:bg-azul-prodifem dark:focus:ring-blue-800"
                                    href="{{ route('admin.medicines.edit', $medicine) }}"><i
                                        class="fa-solid fa-pen pr-1"></i> Editar</a>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @push('js')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                let table = new DataTable('#medicinesTable', {
                    language: {
                        lengthMenu: "Mostrar _MENU_ registros por página",
                        zeroRecords: "Nada encontrado - lo siento",
                        info: "Mostrando página _PAGE_ de _PAGES_",
                        infoEmpty: "No hay registros disponibles",
                        infoFiltered: "(filtrado de _MAX_ registros totales)",
                        search: "Buscar:",
                    }
                });
            });
        </script>
    @endpush
</x-admin-layout>
