<div>
    <form wire:submit.prevent="aplicarBusqueda">
        <div class="mb-4 flex items-center gap-2">
            <input type="text" wire:model="buscar" placeholder="Buscar ..."
                class="border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 focus:ring-opacity-50 w-1/3 p-2">
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-800">
                Buscar
            </button>
        </div>
    </form>

    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 bg-gray-50 uppercase">
                <tr>
                    <th class="px-2 py-2 text-center cursor-pointer" wire:click="sortBy('id')">
                        ID
                        <span class="{{ $sortField === 'id' ? 'font-bold text-blue-700' : 'text-gray-400' }}">
                            {!! $sortField === 'id' ? ($sortDirection === 'asc' ? '▲' : '▼') : '↕' !!}
                        </span>
                    </th>
                    <th class="px-2 py-2 text-center cursor-pointer" wire:click="sortBy('user_id')">
                        Hospital
                        <span class="{{ $sortField === 'user_id' ? 'font-bold text-blue-700' : 'text-gray-400' }}">
                            {!! $sortField === 'user_id' ? ($sortDirection === 'asc' ? '▲' : '▼') : '↕' !!}
                        </span>
                    </th>
                    <th class="px-2 py-2 text-center">Paciente</th>
                    <th class="px-0 py-2 text-center">Modificar</th>
                    <th class="px-2 py-2 text-center cursor-pointer" wire:click="sortBy('created_at')">
                        Fecha y Hora
                        <span class="{{ $sortField === 'created_at' ? 'font-bold text-blue-700' : 'text-gray-400' }}">
                            {!! $sortField === 'created_at' ? ($sortDirection === 'asc' ? '▲' : '▼') : '↕' !!}
                        </span>
                    </th>
                    <th class="px-2 py-2 text-center cursor-pointer" wire:click="sortBy('is_aprobada')">
                        Estado
                        <span class="{{ $sortField === 'is_aprobada' ? 'font-bold text-blue-700' : 'text-gray-400' }}">
                            {!! $sortField === 'is_aprobada' ? ($sortDirection === 'asc' ? '▲' : '▼') : '↕' !!}
                        </span>
                    </th>
                    <th class="px-2 py-2 text-center">Detalles</th>
                    <th class="px-2 py-2 text-center cursor-pointer" wire:click="sortBy('solicitud_aprobadas.lote')">
                        Lote
                        <span class="{{ $sortField === 'solicitud_aprobada.lote' ? 'font-bold text-blue-700' : 'text-gray-400' }}">
                            {!! $sortField === 'solicitud_aprobada.lote' ? ($sortDirection === 'asc' ? '▲' : '▼') : '↕' !!}
                        </span>
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($solicitudes as $solicitud)
                    <tr class="border-b">
                        <td class="px-2 py-2 text-center">{{ $solicitud->id }}</td>
                        <td class="px-2 py-2 text-center">{{ $solicitud->user->hospital->name ?? 'N/A' }}</td>
                        <td class="px-2 py-2 text-center">
                            {{ $solicitud->solicitud_patient->nombre_paciente ?? '' }}
                            {{ $solicitud->solicitud_patient->apellidos_paciente ?? '' }}
                        </td>
                        @hasanyrole('Admin|Super Admin')
                            <td class="px-0 py-2 text-center">
                                <a href="{{ route('admin.nutricionales.solicitudes.edit', $solicitud) }}"
                                    class="text-white bg-blue-600 hover:bg-blue-800 rounded-full text-sm px-2 py-2">
                                    <i class="fa-solid fa-pen pr-1"></i> Aprobar
                                </a>
                            </td>
                        @endhasanyrole
                        <td class="px-2 py-2 text-center">{{ $solicitud->created_at->format('Y-m-d H:i') }}</td>
                        <td class="px-2 py-2 text-center">
                            @if ($solicitud->is_aprobada === 'Aprobada')
                                <span class="text-green-600 font-semibold">Aprobada</span>
                            @elseif ($solicitud->is_aprobada === 'No Aprobada')
                                <span class="text-red-600 font-semibold">No Aprobada</span>
                            @else
                                <span class="text-yellow-600 font-semibold">Pendiente</span>
                            @endif
                        </td>
                        <td class="px-2 py-2 text-center">
                            <a href="{{ route('admin.nutricionales.solicitudes.show', $solicitud) }}"
                                class="text-white bg-indigo-600 hover:bg-indigo-800 rounded-full text-sm px-4 py-2">
                                <i class="fa-solid fa-eye pr-1"></i> Ver
                            </a>
                        </td>
                        <td class="px-2 py-2 text-center">
                            {{ $solicitud->solicitud_aprobada->lote ?? '' }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-4">
            {{ $solicitudes->links() }}
        </div>
    </div>
</div>
