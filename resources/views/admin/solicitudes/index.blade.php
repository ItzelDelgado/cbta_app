<x-admin-layout>

    <!-- CSS de DataTables -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.6/css/jquery.dataTables.css">

    <!-- jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <!-- JavaScript de DataTables -->
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.6/js/jquery.dataTables.js"></script>

    <div class="mt-2">
        <h1 class="text-2xl font-medium text-gray-800">Lista de Solicitudes</h1>
    </div>

    <div class="flex justify-end mb-4">
        <a class="text-white bg-azul-prodifem hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-sm px-5 py-2.5 text-center me-2 mb-2 dark:bg-blue-600 dark:hover:bg-azul-prodifem dark:focus:ring-blue-800"
            href="{{ route('admin.solicitudes.create') }}">Agregar</a>
    </div>


    <div class="relative overflow-x-auto">
        <table id="miTabla" class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-6 py-3">
                        ID
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Hospital
                    </th>
                    @hasanyrole('Admin|Super Admin')
                        <th scope="col" class="px-6 py-3">
                            Modificar
                        </th>
                    @endhasanyrole
                    <th scope="col" class="px-6 py-3">
                        Estado
                    </th>

                    <th scope="col" class="px-6 py-3">
                        Detalles
                    </th>

                    @hasanyrole('Admin|Super Admin')
                        <th scope="col" class="px-6 py-3">
                            Remisión
                        </th>
                    @endhasanyrole
                    @hasanyrole('Admin|Super Admin')
                        <th scope="col" class="px-6 py-3">
                            Lote
                        </th>
                    @endhasanyrole

                </tr>
            </thead>
            <tbody>
                @foreach ($solicitudes as $solicitud)
                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                        <th scope="row"
                            class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            {{ $solicitud->id }}
                        </th>
                        <td class="px-6 py-4">
                            {{ $solicitud->user->hospital->name }}
                        </td>

                        @hasanyrole('Admin|Super Admin')
                            <td class="px-6 py-4">
                                @if ($solicitud->is_aprobada == 'Pendiente')
                                    <div class="flex items-center">
                                        <a class="text-white bg-azul-prodifem hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-sm px-5 py-2.5 text-center me-2 mb-2 dark:bg-blue-600 dark:hover:bg-azul-prodifem dark:focus:ring-blue-800"
                                            href="{{ route('admin.solicitudes.edit', $solicitud) }}">Aprobar</a>
                                    </div>
                                @else
                                    <div class="flex items-center">

                                    </div>
                                @endif
                            </td>
                        @endhasanyrole

                        <td class="px-6 py-4">
                            @if ($solicitud->is_aprobada == 'Aprobada')
                                <div class="flex items-center">
                                    <div class="h-2.5 w-2.5 rounded-full bg-green-500 me-2"></div> Aprobada
                                </div>
                            @elseif ($solicitud->is_aprobada == 'No Aprobada')
                                <div class="flex items-center">
                                    <div class="h-2.5 w-2.5 rounded-full bg-red-500 me-2"></div> No Aprobada
                                </div>
                            @elseif ($solicitud->is_aprobada == 'Pendiente')
                                <div class="flex items-center">
                                    <div class="h-2.5 w-2.5 rounded-full bg-azul-prodifem me-2"></div> Pendiente
                                </div>
                            @endif
                        </td>

                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <a class="text-white bg-azul-prodifem hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-sm px-5 py-2.5 text-center me-2 mb-2 dark:bg-blue-600 dark:hover:bg-azul-prodifem dark:focus:ring-blue-800"
                                    href="{{ route('admin.solicitudes.show', $solicitud) }}">Ver</a>
                            </div>
                        </td>

                        @hasanyrole('Admin|Super Admin')
                            <td class="px-6 py-4">
                                @isset($solicitud->solicitud_aprobada)
                                    {{ $solicitud->solicitud_aprobada->id }}
                                @else
                                @endisset
                            </td>
                        @endhasanyrole
                        @hasanyrole('Admin|Super Admin')
                            <td class="px-6 py-4">
                                @isset($solicitud->solicitud_aprobada)
                                    {{ $solicitud->solicitud_aprobada->lote }}
                                @else
                                @endisset
                            </td>
                        @endhasanyrole

                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>



    <script>
        $(document).ready(function() {
            $('#miTabla').DataTable();
        });
    </script>

</x-admin-layout>
