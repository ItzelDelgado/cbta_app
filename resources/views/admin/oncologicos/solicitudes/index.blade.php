<x-admin-layout>

    <div class="mt-2">
        <h1 class="text-2xl font-medium text-gray-800">Lista de Solicitudes</h1>
    </div>

    <div class="flex flex-wrap justify-end mt-4">
        <div class="mb-4">
            <a class="text-white bg-azul-prodifem hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-sm px-5 py-2.5 text-center me-2 mb-2 dark:bg-blue-600 dark:hover:bg-azul-prodifem dark:focus:ring-blue-800"
                href="{{ route('admin.oncologicos.solicitudes.create')}}"><i class="fa-solid fa-plus pr-1"></i> Agregar</a>
        </div>
        <div class="mb-4">
            <a href=""
                class="text-white bg-green-600 hover:bg-green-800 focus:outline-none focus:ring-4 focus:ring-green-300 font-medium rounded-full text-sm px-5 py-2.5 text-center me-2 mb-2">
                <i class="fa-solid fa-file-excel pr-1"></i> Exportar a Excel
            </a>
        </div>
    </div>


    <div class="relative overflow-x-auto">
        <table id="solicitudesTable" class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>

                    <th scope="col" class="px-6 py-3 ">
                        No. Solicitud
                    </th>

                    <th scope="col" class="px-6 py-3">
                        Hospital
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Paciente
                    </th>

                    <th scope="col" class="px-6 py-3">
                        Fecha y Hora de Solicitud
                    </th>

                    <th scope="col" class="px-6 py-3">
                        Fecha y Hora de Entrega
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Estado
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Acciones
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Remisión
                    </th>
                </tr>
            </thead>
            <tbody>






            </tbody>
        </table>
    </div>





</x-admin-layout>
