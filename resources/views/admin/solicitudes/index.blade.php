<x-admin-layout>
    <div class="mt-2">
        <h1 class="text-2xl font-medium text-gray-800">Lista de Solicitudes</h1>
    </div>

    <div class="flex justify-end mb-4">
        <a class="text-white bg-blue-700 hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-sm px-5 py-2.5 text-center me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
            href="{{ route('admin.solicitudes.create') }}">Agregar</a>
        <a class="text-white bg-blue-700 hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-sm px-5 py-2.5 text-center me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
            href="">pdf</a>
    </div>


    <div class="relative overflow-x-auto">
        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-6 py-3">
                        ID
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Hospital
                    </th>

                    <th scope="col" class="px-6 py-3">
                        Detalles
                    </th>

                    <th scope="col" class="px-6 py-3">
                        Estado
                    </th>

                    <th scope="col" class="px-6 py-3">
                        #Remision
                    </th>
                    <th scope="col" class="px-6 py-3">

                    </th>
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

                        <!-- Botón para abrir el modal -->
                        <td class="px-6 py-4">
                            <a class="text-white bg-blue-700 hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-sm px-5 py-2.5 text-center me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800" href="{{ route('admin.solicitudes.edit', $solicitud) }}">Aprobar</a>
                        </td>

                        <!-- Modal -->
                        {{-- <div id="modal{{ $solicitud->id }}" class="modal">
                            <div class="modal-content">
                                @livewire('solicitud-detalles', ['data' => $solicitud], key($solicitud->id))
                                <button onclick="closeModal({{ $solicitud->id }})">Cerrar</button>
                            </div>
                        </div> --}}

                        <td class="px-6 py-4">
                            @if ($solicitud->is_aprobada == 'Aprobada')
                                <div class="flex items-center">
                                    <div class="h-2.5 w-2.5 rounded-full bg-green-500 me-2"></div> Aprobada
                                </div>
                            @elseif ($solicitud->is_aprobada == 'No aprobada')
                                <div class="flex items-center">
                                    <div class="h-2.5 w-2.5 rounded-full bg-red-500 me-2"></div> No Aprobada
                                </div>
                            @elseif ($solicitud->is_aprobada == 'Pendiente')
                                <div class="flex items-center">
                                    <div class="h-2.5 w-2.5 rounded-full bg-slate-600 me-2"></div> Pendiente
                                </div>
                            @endif
                        </td>
                        {{-- <td class="px-6 py-4">
                            <a href="{{ route('admin.solicitudes.edit', $solicitud->id) }}">Editar</a>
                        </td> --}}
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>





</x-admin-layout>
