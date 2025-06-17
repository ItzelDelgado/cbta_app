<x-admin-layout>
    <div class="mt-4 mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Detalle de Solicitud #{{ $solicitud->id }}</h1>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8 bg-white shadow rounded-lg p-6">
        <div>
            <p class="text-gray-700"><span class="font-semibold text-gray-900">Hospital:</span> {{ $solicitud->user->hospital->name ?? 'N/A' }}</p>
            <p class="text-gray-700"><span class="font-semibold text-gray-900">Paciente:</span> {{ $solicitud->nombre_paciente }}</p>
        </div>
        <div>
            <p class="text-gray-700"><span class="font-semibold text-gray-900">Fecha de Solicitud:</span> {{ $solicitud->fecha_solicitud }} {{ $solicitud->created_at->format('H:i') }}</p>
            <p class="text-gray-700"><span class="font-semibold text-gray-900">Fecha de Entrega:</span> {{ \Carbon\Carbon::parse($solicitud->fecha_solicitud . ' ' . $solicitud->horario_entrega)->format('Y-m-d H:i') }}</p>
        </div>
    </div>

    <div class="relative overflow-x-auto rounded-lg shadow">
        <table class="w-full text-sm text-left text-gray-700 bg-white">
            <thead class="text-xs uppercase bg-gray-100 text-gray-700">
                <tr>
                    <th class="px-6 py-3">No. Mezcla</th>
                    <th class="px-6 py-3">Hospital</th>
                    <th class="px-6 py-3">Paciente</th>
                    <th class="px-6 py-3">Fecha y hora de solicitud</th>
                    <th class="px-6 py-3">Fecha y hora de entrega</th>
                    <th class="px-6 py-3">Estado</th>
                    <th class="px-6 py-3">Remisión</th>
                    <th class="px-6 py-3">Lote</th>
                    <th class="px-6 py-3">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($solicitud->mezclas as $mezcla)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-6 py-4">{{ $mezcla->id }}</td>
                        <td class="px-6 py-4">{{ $solicitud->user->hospital->name ?? 'N/A' }}</td>
                        <td class="px-6 py-4">{{ $solicitud->nombre_paciente }}</td>
                        <td class="px-6 py-4">{{ $solicitud->fecha_solicitud }} {{ $solicitud->created_at->format('H:i') }}</td>
                        <td class="px-6 py-4">{{ \Carbon\Carbon::parse($solicitud->fecha_solicitud . ' ' . $solicitud->horario_entrega)->format('Y-m-d H:i') }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full
                                {{ match($mezcla->estado) {
                                    'pendiente' => 'bg-yellow-100 text-yellow-800',
                                    'aprobada' => 'bg-green-100 text-green-800',
                                    'cancelada' => 'bg-red-100 text-red-800',
                                    default => 'bg-gray-100 text-gray-800'
                                } }}">
                                {{ ucfirst($mezcla->estado) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">{{ $solicitud->remision ?? '—' }}</td>
                        <td class="px-6 py-4">{{ $mezcla->lote ?? '—' }}</td>
                        <td class="px-6 py-4">
                            <a href="{{ route('admin.oncologicos.mezclas.show', $mezcla->id) }}" class="text-indigo-600 hover:text-indigo-900 font-medium text-sm">Ver</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="px-6 py-4 text-center text-gray-500">No hay mezclas registradas.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        <a href="{{ route('admin.oncologicos.solicitudes.index') }}"
           class="inline-block px-4 py-2 text-sm text-blue-600 hover:underline">
            &laquo; Volver a listado
        </a>
    </div>
</x-admin-layout>
