<div class="relative overflow-x-auto">
    <table id="miTabla" class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
            <tr>
                @hasanyrole('Admin|Super Admin')
                    <th scope="col" class="px-6 py-3">
                        ID
                    </th>
                @endhasanyrole
                <th scope="col" class="px-6 py-3">
                    Hospital
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
                <tr class="
                    @if ($solicitud->read_at)
                        bg-white
                    @else
                        @auth
                            @if (auth()->user()->roles->contains('name', 'Admin'))
                                bg-[#fde047ab]
                            @else
                                bg-white
                            @endif
                        @else
                            bg-white
                        @endauth
                    @endif
                    border-b dark:bg-gray-800 dark:border-gray-700
                ">
                    @hasanyrole('Admin|Super Admin')
                        <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            {{ $solicitud->id }}
                        </th>
                    @endhasanyrole
                    <td class="px-6 py-4">
                        {{ $solicitud->user->hospital->name }}
                    </td>
                    @hasanyrole('Admin|Super Admin')
                        <td class="px-6 py-4">
                            @if ($solicitud->is_aprobada == 'Pendiente')
                                <div class="flex items-center">
                                    <a class="text-white bg-azul-prodifem hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-sm px-5 py-2.5 text-center me-2 mb-2 dark:bg-blue-600 dark:hover:bg-azul-prodifem dark:focus:ring-blue-800"
                                        href="{{ route('admin.solicitudes.edit', $solicitud) }}"
                                        wire:click.prevent="readSolicitudEdit('{{ $solicitud->id }}')">
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
                            <button class="text-white bg-azul-prodifem hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-sm px-5 py-2.5 text-center me-2 mb-2 dark:bg-blue-600 dark:hover:bg-azul-prodifem dark:focus:ring-blue-800"
                                wire:click.prevent="readSolicitud('{{ $solicitud->id }}')">
                                <i class="fa-solid fa-eye pr-1"></i> Ver
                            </button>
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
    <div class="mt-4">
        {{ $solicitudes->links() }}
    </div>
</div>
