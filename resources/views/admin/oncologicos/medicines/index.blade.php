<x-admin-layout>
    <div class="mt-2">
        <h1 class="text-2xl font-medium text-gray-800">Lista de Medicamentos</h1>
    </div>

    <div class="flex justify-end mb-4">
        <a class="text-white bg-azul-prodifem hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-sm px-5 py-2.5 text-center me-2 mb-2 dark:bg-blue-600 dark:hover:bg-azul-prodifem dark:focus:ring-blue-800"
            href="{{ route('admin.oncologicos.medicines.create') }}"><i class="fa-solid fa-plus pr-1"></i> Nueva Lista de Medicamentos</a>
    </div>

    <div class="relative overflow-x-auto">
        <table id="medicinesTable" class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-6 py-3">ID</th>
                    <th scope="col" class="px-6 py-3">Nombre</th>
                    <th scope="col" class="px-6 py-3">Descripción</th>
                    <th scope="col" class="px-6 py-3">Acciones</th>
                </tr>
            </thead>
            <tbody>

                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                    <td class="px-6 py-4"></td>
                    <td class="px-6 py-4"></td>
                    <td class="px-6 py-4"></td>
                    <td class="px-6 py-4"></td>
                </tr>

            </tbody>
        </table>
    </div>

    @push('js')
    @endpush
</x-admin-layout>
