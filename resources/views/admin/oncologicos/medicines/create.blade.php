<x-admin-layout>
    <div class="mt-2">
        <h1 class="text-2xl font-medium text-gray-800">Nueva Lista de Medicamentos</h1>
    </div>

    <div class="relative overflow-x-auto">
        <table id="medicinesTable" class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-6 py-3">Denominación</th>
                    <th scope="col" class="px-6 py-3">Presentación</th>
                    <th scope="col" class="px-6 py-3">Precio</th>
                    <th scope="col" class="px-6 py-3">multiplica</th>
                    <th scope="col" class="px-6 py-3">divide</th>
                </tr>
            </thead>
            <tbody>

                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                    <td class="px-6 py-4">
                        <input type="text" name="denominacion" id="denominacion"
                            class="block w-full p-2 text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                            placeholder="Denominación" required>
                    </td>
                    <td class="px-6 py-4">
                        <input type="text" name="presentacion" id="presentacion"
                            class="block w-full p-2 text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                            placeholder="Presentación" required>
                    </td>
                    <td class="px-6 py-4">
                        <input type="text" name="precio" id="precio"
                            class="block w-full p-2 text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                            placeholder="Precio" required>
                    </td>
                    <td class="px-6 py-4">
                        <input type="text" name="multiplica" id="multiplica"
                            class="block w-full p-2 text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                            placeholder="Multiplica" required>
                    </td>
                    <td>
                        <input type="text" name="divide" id="divide"
                            class="block w-full p-2 text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                            placeholder="Divide" required>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    @push('js')
    @endpush
</x-admin-layout>
