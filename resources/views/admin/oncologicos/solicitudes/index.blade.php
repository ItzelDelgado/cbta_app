<x-admin-layout>

    <div class="mt-2">
        <h1 class="text-2xl font-medium text-gray-800">Lista de Solicitudes</h1>
    </div>

    <div class="flex flex-wrap justify-end mt-4">
        <div class="mb-4">
            <a class="text-white bg-azul-prodifem hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-sm px-5 py-2.5 text-center me-2 mb-2 dark:bg-blue-600 dark:hover:bg-azul-prodifem dark:focus:ring-blue-800"
                href="{{ route('admin.oncologicos.solicitudes.create') }}"><i class="fa-solid fa-plus pr-1"></i>
                Agregar</a>
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
                @foreach ($solicitudes as $solicitud)
                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50">

                        <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                            {{ $solicitud->id }}
                        </td>

                        <td class="px-6 py-4">
                            {{ $solicitud->user->hospital->name ?? 'N/A' }}
                        </td>

                        <td class="px-6 py-4">
                            {{ $solicitud->nombre_paciente }}
                        </td>

                        <td class="px-6 py-4">
                            {{ $solicitud->fecha_solicitud }} {{ $solicitud->created_at?->format('H:i') ?? '' }}
                        </td>

                        <td class="px-6 py-4">
                            {{ \Carbon\Carbon::parse($solicitud->fecha_solicitud . ' ' . $solicitud->horario_entrega)->format('Y-m-d H:i') }}
                        </td>

                        <td class="px-6 py-4">
                            <span
                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                {{ ucfirst($solicitud->estado) }}
                            </span>
                        </td>

                        <td class="px-6 py-4">
                            <div class="flex space-x-2">
                                <a href="#" class="text-blue-600 hover:text-blue-900"><i
                                        class="fa-solid fa-eye"></i></a>
                                <a href="#" class="text-yellow-600 hover:text-yellow-900"><i
                                        class="fa-solid fa-pen-to-square"></i></a>
                                <form method="POST" action="#">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900"><i
                                            class="fa-solid fa-trash"></i></button>
                                </form>
                            </div>
                        </td>

                        <td class="px-6 py-4">
                            {{ $solicitud->remision ?? '—' }}
                        </td>
                    </tr>
                @endforeach
            </tbody>

        </table>
    </div>





</x-admin-layout>
