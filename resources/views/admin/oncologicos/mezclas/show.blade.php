<x-admin-layout>
    <div class="flex flex-col">
        <div class="mt-2 mb-4">
            <h1 class="text-2xl font-medium text-gray-800">Visualizar Mezcla #{{ $mezcla->id }} - Solicitud
                #{{ $mezcla->solicitud->id }}</h1>
        </div>

        <div class="bg-white rounded-lg p-6 shadow-lg space-y-4">

            {{-- Datos del paciente --}}
            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="text-sm font-semibold">Paciente Nombre(s)</label>
                    <p class="border border-gray-300 rounded-md px-2 py-1 bg-gray-100 text-gray-700">
                        {{ $mezcla->solicitud->nombre_paciente }}
                    </p>
                </div>
                <div>
                    <label class="text-sm font-semibold">Servicio</label>
                    <p class="border border-gray-300 rounded-md px-2 py-1 bg-gray-100 text-gray-700">
                        {{ $mezcla->solicitud->servicio }}
                    </p>
                </div>
                <div>
                    <label class="text-sm font-semibold">Registro</label>
                    <p class="border border-gray-300 rounded-md px-2 py-1 bg-gray-100 text-gray-700">
                        {{ $mezcla->solicitud->registro_paciente }}
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-4 gap-4">
                <div>
                    <label class="text-sm font-semibold">Sexo</label>
                    <p class="border border-gray-300 rounded-md px-2 py-1 bg-gray-100 text-gray-700">
                        {{ $mezcla->solicitud->sexo }}
                    </p>
                </div>
                <div>
                    <label class="text-sm font-semibold">Fecha de nacimiento</label>
                    <p class="border border-gray-300 rounded-md px-2 py-1 bg-gray-100 text-gray-700">
                        {{ $mezcla->solicitud->fecha_nacimiento }}
                    </p>
                </div>
                <div>
                    <label class="text-sm font-semibold">Peso</label>
                    <p class="border border-gray-300 rounded-md px-2 py-1 bg-gray-100 text-gray-700">
                        {{ $mezcla->solicitud->peso }} kg
                    </p>
                </div>
                <div>
                    <label class="text-sm font-semibold">Cama</label>
                    <p class="border border-gray-300 rounded-md px-2 py-1 bg-gray-100 text-gray-700">
                        {{ $mezcla->solicitud->cama }}
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-4 gap-4">
                <div>
                    <label class="text-sm font-semibold">Piso</label>
                    <p class="border border-gray-300 rounded-md px-2 py-1 bg-gray-100 text-gray-700">
                        {{ $mezcla->solicitud->piso }}
                    </p>
                </div>
                <div>
                    <label class="text-sm font-semibold">Fecha de entrega</label>
                    <p class="border border-gray-300 rounded-md px-2 py-1 bg-gray-100 text-gray-700">
                        {{ \Carbon\Carbon::parse($mezcla->solicitud->fecha_entrega)->format('Y-m-d\TH:i') }}


                    </p>
                </div>
                <div>
                    <label class="text-sm font-semibold">Médico</label>
                    <p class="border border-gray-300 rounded-md px-2 py-1 bg-gray-100 text-gray-700">
                        {{ $mezcla->solicitud->nombre_medico }}
                    </p>
                </div>
                <div>
                    <label class="text-sm font-semibold">Cédula</label>
                    <p class="border border-gray-300 rounded-md px-2 py-1 bg-gray-100 text-gray-700">
                        {{ $mezcla->solicitud->cedula_medico }}
                    </p>
                </div>
            </div>

            <div>
                <label class="text-sm font-semibold">Diagnóstico</label>
                <p class="border border-gray-300 rounded-md px-2 py-1 bg-gray-100 text-gray-700">
                    {{ $mezcla->solicitud->diagnostico }}
                </p>
            </div>

            <div>
                <label class="text-sm font-semibold">Observaciones</label>
                <p class="border border-gray-300 rounded-md px-2 py-1 bg-gray-100 text-gray-700">
                    {{ $mezcla->solicitud->observaciones }}
                </p>
            </div>

            {{-- Mezcla visualizada --}}
            <div class="mt-6 border border-gray-300 p-4 rounded-lg">
                <h2 class="text-lg font-semibold mb-2">Mezcla #1</h2>

                <table class="table-auto w-full text-sm border border-gray-200 mb-4">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="border px-4 py-2">Medicamento</th>
                            <th class="border px-4 py-2">Dosis</th>
                            <th class="border px-4 py-2">Diluyente</th>
                            <th class="border px-4 py-2">Vía de administración</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($mezcla->medicamentos as $med)
                            @php
                                $info = $infoAdicional[$med->medicamento_id] ?? null;

                                $diluyenteNombre = $info
                                    ? optional($info['diluyentes']->firstWhere('id', $med->diluyente_id))->name
                                    : '—';

                                $viaNombre = $info
                                    ? optional($info['vias']->firstWhere('id', $med->via_administracion_id))->name
                                    : '—';
                            @endphp
                            <tr>
                                <td class="border px-4 py-2">{{ $med->nombre_medicamento }}</td>
                                <td class="border px-4 py-2">{{ $med->dosis }}</td>
                                <td class="border px-4 py-2">{{ $diluyenteNombre ?? '—' }}</td>
                                <td class="border px-4 py-2">{{ $viaNombre ?? '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>

                </table>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-semibold">Volumen total de dilución (ml)</label>
                        <p class="border border-gray-300 rounded-md px-2 py-1 bg-gray-100 text-gray-700">
                            {{ $mezcla->volumen_dilucion }}
                        </p>
                    </div>
                    <div>
                        <label class="text-sm font-semibold">Tiempo de infusión (min)</label>
                        <p class="border border-gray-300 rounded-md px-2 py-1 bg-gray-100 text-gray-700">
                            {{ $mezcla->tiempo_infusion }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="mt-6">
                <a href="{{ route('admin.oncologicos.solicitudes.index') }}"
                    class="text-blue-600 hover:underline">&laquo; Volver</a>
            </div>
        </div>
    </div>
</x-admin-layout>
