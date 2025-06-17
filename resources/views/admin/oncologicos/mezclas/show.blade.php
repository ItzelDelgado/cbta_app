<x-admin-layout>
    <div class="mb-4">
        <h1 class="text-2xl font-bold">Solicitud #{{ $mezcla->solicitud->id }} - Mezcla #{{ $mezcla->id }}</h1>
    </div>

    <div class="grid grid-cols-2 gap-4 mb-6 bg-white p-6 rounded shadow">
        <div><strong>Paciente:</strong> {{ $mezcla->solicitud->nombre_paciente }}</div>
        <div><strong>Servicio:</strong> {{ $mezcla->solicitud->servicio }}</div>
        <div><strong>Registro:</strong> {{ $mezcla->solicitud->registro_paciente }}</div>
        <div><strong>Sexo:</strong> {{ $mezcla->solicitud->sexo }}</div>
        <div><strong>Fecha de nacimiento:</strong> {{ $mezcla->solicitud->fecha_nacimiento }}</div>
        <div><strong>Peso:</strong> {{ $mezcla->solicitud->peso }} kg</div>
        <div><strong>Diagnóstico:</strong> {{ $mezcla->solicitud->diagnostico }}</div>
        <div><strong>Fecha de entrega:</strong> {{ $mezcla->solicitud->fecha_solicitud }} {{ $mezcla->solicitud->horario_entrega }}</div>
        <div><strong>Nombre del Médico:</strong> {{ $mezcla->solicitud->nombre_medico }}</div>
        <div><strong>Cédula:</strong> {{ $mezcla->solicitud->cedula_medico }}</div>
        <div><strong>Observaciones:</strong> {{ $mezcla->solicitud->observaciones }}</div>
    </div>

    <h2 class="text-lg font-bold mb-2">Medicamentos</h2>
    <table class="w-full text-sm text-left text-gray-600 border border-gray-200">
        <thead class="bg-gray-100 text-xs uppercase">
            <tr>
                <th class="p-3">Medicamento</th>
                <th class="p-3">Dosis</th>
            </tr>
        </thead>
        <tbody>
            @foreach($mezcla->medicamentos as $med)
            <tr class="border-t">
                <td class="p-3">{{ $med->nombre_medicamento }}</td>
                <td class="p-3">{{ $med->dosis }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="mt-6">
        <a href="{{ route('admin.oncologicos.solicitudes.index') }}"
           class="text-blue-600 hover:underline">&laquo; Volver</a>
    </div>
</x-admin-layout>
