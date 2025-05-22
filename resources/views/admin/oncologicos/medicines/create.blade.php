<x-admin-layout>
    <div class="mt-2">
        <h1 class="text-2xl font-medium text-gray-800">Nueva Lista de Medicamentos</h1>
    </div>

    <form action="" method="POST" enctype="multipart/form-data" class="mb-4">
        @csrf
        <div class="flex gap-4 items-center">
            <input type="file" name="archivo_excel" accept=".xlsx,.xls" required
                class="border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-blue-200 focus:ring-opacity-50">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Importar desde Excel
            </button>
        </div>
    </form>

    <div class="flex justify-end my-4">
        <button type="button" id="addRowBtn"
            class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
            + Nuevo medicamento
        </button>
    </div>

    <div class="relative overflow-x-auto">
        <table id="medicinesTable" class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr>
                    <th class="px-6 py-3">Denominación</th>
                    <th class="px-6 py-3">Presentación</th>
                    <th class="px-6 py-3">Precio</th>
                    <th class="px-6 py-3">Multiplica</th>
                    <th class="px-6 py-3">Divide</th>
                    <th class="px-6 py-3">Acción</th>
                </tr>
            </thead>
            <tbody id="medicinesBody">
                <!-- Aquí se agregarán las filas dinámicamente -->
            </tbody>
        </table>
    </div>

    @push('js')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const addRowBtn = document.getElementById('addRowBtn');
                const tableBody = document.getElementById('medicinesBody');

                addRowBtn.addEventListener('click', () => {
                    const row = document.createElement('tr');
                    row.classList.add('bg-white', 'border-b');

                    row.innerHTML = `
                <td class="px-1 py-1">
                    <input type="text" name="denominacion[]" required
                        class="block w-full p-2 text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring focus:ring-blue-200 focus:border-blue-500">
                </td>
                <td class="px-1 py-1">
                    <input type="text" name="presentacion[]" required
                        class="block w-full p-2 text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring focus:ring-blue-200 focus:border-blue-500">
                </td>
                <td class="px-1 py-1">
                    <input type="number" step="0.01" name="precio[]" required
                        class="block w-full p-2 text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring focus:ring-blue-200 focus:border-blue-500">
                </td>
                <td class="px-1 py-1">
                    <input type="number" step="0.01" name="multiplica[]" required
                        class="block w-full p-2 text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring focus:ring-blue-200 focus:border-blue-500">
                </td>
                <td class="px-1 py-1">
                    <input type="number" step="0.01" name="divide[]" required
                        class="block w-full p-2 text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring focus:ring-blue-200 focus:border-blue-500">
                </td>
                <td class="px-1 py-1 text-center">
                    <button type="button" class="delete-row text-red-600 hover:underline">Eliminar</button>
                </td>
            `;

                    tableBody.appendChild(row);
                });

                // Delegación para eliminar filas dinámicamente
                tableBody.addEventListener('click', function(e) {
                    if (e.target && e.target.classList.contains('delete-row')) {
                        const row = e.target.closest('tr');
                        row.remove();
                    }
                });
            });
        </script>
    @endpush
</x-admin-layout>
