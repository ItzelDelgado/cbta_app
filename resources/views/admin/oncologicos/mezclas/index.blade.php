<x-admin-layout>
    <div class="mt-4 mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Detalle de Solicitud #{{ $solicitud->id }}</h1>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8 bg-white shadow rounded-lg p-6">
        <div>
            <p class="text-gray-700"><span class="font-semibold text-gray-900">Hospital:</span>
                {{ $solicitud->user->hospital->name ?? 'N/A' }}</p>
            <p class="text-gray-700"><span class="font-semibold text-gray-900">Paciente:</span>
                {{ $solicitud->nombre_paciente }}</p>
        </div>
        <div>
            <p class="text-gray-700"><span class="font-semibold text-gray-900">Fecha de Solicitud:</span>
                {{ $solicitud->fecha_solicitud }} {{ $solicitud->created_at->format('H:i') }}</p>
            <p class="text-gray-700"><span class="font-semibold text-gray-900">Fecha de Entrega:</span>
                {{ \Carbon\Carbon::parse($solicitud->fecha_solicitud . ' ' . $solicitud->horario_entrega)->format('Y-m-d H:i') }}
            </p>
        </div>
        <div>
            <a href="{{ route('admin.oncologicos.solicitudes.edit', $solicitud->id) }}"
                class="inline-block px-6 py-3 bg-yellow-500 text-white text-base font-semibold rounded-lg hover:bg-yellow-600 transition duration-200">
                Ver Solicitud Completa
            </a>

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
                    <th class="px-6 py-3">Acciones</th>
                    <th class="px-6 py-3">Remisión</th>
                    <th class="px-6 py-3">Lote</th>
                </tr>
            </thead>
            <tbody>
                @forelse($solicitud->mezclas as $mezcla)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-6 py-4">{{ $mezcla->id }}</td>
                        <td class="px-6 py-4">{{ $solicitud->user->hospital->name ?? 'N/A' }}</td>
                        <td class="px-6 py-4">{{ $solicitud->nombre_paciente }}</td>
                        <td class="px-6 py-4">{{ $solicitud->fecha_solicitud }}
                            {{ $solicitud->created_at->format('H:i') }}</td>
                        <td class="px-6 py-4">
                            {{ \Carbon\Carbon::parse($solicitud->fecha_solicitud . ' ' . $solicitud->horario_entrega)->format('Y-m-d H:i') }}
                        </td>
                        <td class="px-6 py-4">
                            <span
                                class="px-2 py-1 text-xs font-semibold rounded-full
                                {{ match ($mezcla->estado) {
                                    'pendiente' => 'bg-yellow-100 text-yellow-800',
                                    'aprobada' => 'bg-green-100 text-green-800',
                                    'cancelada' => 'bg-red-100 text-red-800',
                                    default => 'bg-gray-100 text-gray-800',
                                } }}">
                                {{ ucfirst($mezcla->estado) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <a href="{{ route('admin.oncologicos.mezclas.show', $mezcla->id) }}"
                                class="btn-ver px-4 py-1 bg-blue-600 text-white text-sm rounded hover:bg-blue-700 transition">Ver</a>
                            <a href="{{ route('admin.oncologicos.mezclas.edit', $mezcla->id) }}"
                                class="btn-editar px-4 py-1 bg-yellow-500 text-white text-sm rounded hover:bg-yellow-600 transition">
                                Editar
                            </a>
                            @if ($mezcla->estado === 'aprobada')
                                <form id="formPreparar-{{ $mezcla->id }}"
                                    action="{{ route('admin.oncologicos.mezclas.update', $mezcla->id) }}"
                                    method="POST" class="inline">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="accion" value="preparada">
                                    <button type="button" onclick="confirmarPreparada({{ $mezcla->id }})"
                                        class="btn-aprobar px-4 py-1 bg-green-600 text-white text-sm rounded hover:bg-green-700 transition">
                                        Preparada
                                    </button>
                                </form>
                            @endif
                        </td>
                        <td class="px-6 py-4">{{ $solicitud->remision ?? '—' }}</td>
                        <td class="px-6 py-4">{{ $mezcla->lote ?? '—' }}</td>

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

    @push('js')
        <script>
            function confirmarPreparada(id) {
                Swal.fire({
                    title: '¿Estás seguro?',
                    text: '¿Estás seguro de que esta mezcla ya fue preparada?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, marcar como preparada',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('formPreparar-' + id).submit();
                    }
                });
            }
        </script>
    @endpush
</x-admin-layout>
