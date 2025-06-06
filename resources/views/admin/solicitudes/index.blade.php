<x-admin-layout>

    <div class="mt-2">
        <h1 class="text-2xl font-medium text-gray-800">Lista de Solicitudes</h1>
    </div>

    <div class="flex flex-wrap justify-end mt-4">
        <div class="mb-4">
            <a class="text-white bg-azul-prodifem hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-sm px-5 py-2.5 text-center me-2 mb-2 dark:bg-blue-600 dark:hover:bg-azul-prodifem dark:focus:ring-blue-800"
                href="{{ route('admin.solicitudes.create') }}"><i class="fa-solid fa-plus pr-1"></i> Agregar</a>
        </div>
        <div class="mb-4">
            <a href="{{ route('admin.solicitudes.exportar') }}"
                class="text-white bg-green-600 hover:bg-green-800 focus:outline-none focus:ring-4 focus:ring-green-300 font-medium rounded-full text-sm px-5 py-2.5 text-center me-2 mb-2">
                <i class="fa-solid fa-file-excel pr-1"></i> Exportar a Excel
            </a>
        </div>
    </div>


    <div class="relative overflow-x-auto">
        <table id="solicitudesTable" class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    @hasanyrole('Admin|Super Admin')
                        <th scope="col" class="px-6 py-3 ">
                            ID
                        </th>
                    @endhasanyrole
                    <th scope="col" class="px-6 py-3">
                        Hospital
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Paciente
                    </th>
                    @hasanyrole('Admin|Super Admin')
                        <th scope="col" class="px-6 py-3">
                            Modificar
                        </th>
                    @endhasanyrole
                    <th scope="col" class="px-6 py-3">
                        Fecha y hora solicitud
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Fecha y hora entrega
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Estado
                    </th>

                    <th scope="col" class="px-6 py-3">
                        Detalles
                    </th>

                    @hasanyrole('Admin|Super Admin')
                        <th scope="col" class="px-6 py-3 text-center">
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
                    <tr
                        class="
                        {{-- @if ($solicitud->read_at) bg-white
                        @else
                            @auth
                                @if (auth()->user()->roles->contains('name', 'Admin'))
                                    bg-[#fde047ab]
                                @else
                                    bg-white @endif
    @else
    bg-white
                            @endauth
                        @endif --}}
                        border-b dark:bg-gray-800 dark:border-gray-700
                    ">
                        @hasanyrole('Admin|Super Admin')
                            <td scope="row"
                                class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white text-center">
                                {{ $solicitud->id }}
                            </td>
                        @endhasanyrole
                        <td class="px-6 py-4">
                            {{ $solicitud->user->hospital->name }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $solicitud->solicitud_patient->nombre_paciente ?? 'N/A' }}
                            {{ $solicitud->solicitud_patient->apellidos_paciente ?? 'N/A' }}
                        </td>
                        @hasanyrole('Admin|Super Admin')
                            <td class="px-6 py-4">
                                @if ($solicitud->is_aprobada == 'Pendiente')
                                    <div class="flex items-center">
                                        <a class="text-white bg-azul-prodifem hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-sm px-5 py-2.5 text-center me-2 mb-2 dark:bg-blue-600 dark:hover:bg-azul-prodifem dark:focus:ring-blue-800"
                                            href="{{ route('admin.solicitudes.edit', $solicitud) }}" {{-- wire:click.prevent="readSolicitudEdit('{{ $solicitud->id }}')" --}}>
                                            <i class="fa-solid fa-pen pr-1"></i> Aprobar
                                        </a>
                                    </div>
                                @else
                                    <div class="flex items-center"></div>
                                @endif
                            </td>
                        @endhasanyrole
                        <td class="px-6 py-4">
                            {{ $solicitud->created_at }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $solicitud->solicitud_detail->fecha_hora_entrega }}
                        </td>
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
                                <a href="{{ route('admin.solicitudes.show', $solicitud) }}">
                                    <button
                                        class="text-white bg-azul-prodifem hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-sm px-5 py-2.5 text-center me-2 mb-2 dark:bg-blue-600 dark:hover:bg-azul-prodifem dark:focus:ring-blue-800"
                                        {{-- wire:click.prevent="readSolicitud('{{ $solicitud->id }}')" --}}>
                                        <i class="fa-solid fa-eye pr-1"></i> Ver
                                    </button>
                                </a>
                            </div>
                        </td>
                        @hasanyrole('Admin|Super Admin')
                            <td class="px-6 py-4" style="text-align: center">
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
        <div class="mt-4">
            {{ $solicitudes->links() }}
        </div>
    </div>



    @push('js')
        <script>
            document.addEventListener('DOMContentLoaded', function() {

                const initDataTable = () => {
                    const table = document.querySelector('#solicitudesTable');
                    if (table.classList.contains('dataTable-initialized')) {
                        table.DataTable().destroy();
                    }

                    const dataTable = new DataTable(table, {
                        paging: false, // Desactivar paginación de DataTables
                        searching: true,
                        info: false,
                        order: [
                            [0, 'desc']
                        ], // Ordenar la primera columna (ID) de manera descendente
                        language: {
                            lengthMenu: "Mostrar _MENU_ registros por página",
                            zeroRecords: "Nada encontrado - lo siento",
                            info: "Mostrando página _PAGE_ de _PAGES_",
                            infoEmpty: "No hay registros disponibles",
                            infoFiltered: "(filtrado de _MAX_ registros totales)",
                            search: "Buscar:"
                        },
                    });

                    table.classList.add('dataTable-initialized');
                };

                initDataTable();


            });
        </script>
    @endpush




</x-admin-layout>
