<x-admin-layout>
    <div class="mt-2 mb-4">
        <h1 class="text-2xl font-medium text-gray-800">Editar Mezcla #{{ $mezcla->id }} - Solicitud #{{ $solicitud->id }}</h1>
    </div>

    <form action="{{ route('admin.oncologicos.mezclas.update', $mezcla->id) }}" method="POST" class="bg-white rounded-lg p-6 shadow-lg space-y-6">
        @csrf
        @method('PUT')

        {{-- Información de la solicitud asociada --}}
        <div class="grid grid-cols-3 gap-4">
            <div>
                <label>Paciente Nombre(s)</label>
                <input type="text" value="{{ $solicitud->nombre_paciente }}" class="w-full border rounded px-2 py-1 bg-gray-100" readonly>
            </div>
            <div>
                <label>Servicio</label>
                <input type="text" value="{{ $solicitud->servicio }}" class="w-full border rounded px-2 py-1 bg-gray-100" readonly>
            </div>
            <div>
                <label>Registro</label>
                <input type="text" value="{{ $solicitud->registro_paciente }}" class="w-full border rounded px-2 py-1 bg-gray-100" readonly>
            </div>
        </div>

        <div class="grid grid-cols-4 gap-4">
            <div>
                <label>Sexo</label>
                <input type="text" value="{{ $solicitud->sexo }}" class="w-full border rounded px-2 py-1 bg-gray-100" readonly>
            </div>
            <div>
                <label>Fecha de nacimiento</label>
                <input type="text" value="{{ $solicitud->fecha_nacimiento }}" class="w-full border rounded px-2 py-1 bg-gray-100" readonly>
            </div>
            <div>
                <label>Peso (kg)</label>
                <input type="text" value="{{ $solicitud->peso }}" class="w-full border rounded px-2 py-1 bg-gray-100" readonly>
            </div>
            <div>
                <label>Cama</label>
                <input type="text" value="{{ $solicitud->cama }}" class="w-full border rounded px-2 py-1 bg-gray-100" readonly>
            </div>
        </div>

        <div class="grid grid-cols-4 gap-4">
            <div>
                <label>Piso</label>
                <input type="text" value="{{ $solicitud->piso }}" class="w-full border rounded px-2 py-1 bg-gray-100" readonly>
            </div>
            <div>
                <label>Fecha de entrega</label>
                <input type="text" value="{{ $solicitud->fecha_entrega }}" class="w-full border rounded px-2 py-1 bg-gray-100" readonly>
            </div>
            <div>
                <label>Médico</label>
                <input type="text" value="{{ $solicitud->nombre_medico }}" class="w-full border rounded px-2 py-1 bg-gray-100" readonly>
            </div>
            <div>
                <label>Cédula</label>
                <input type="text" value="{{ $solicitud->cedula_medico }}" class="w-full border rounded px-2 py-1 bg-gray-100" readonly>
            </div>
        </div>

        {{-- Datos editables de la mezcla --}}
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label>Volumen total de dilución (ml)</label>
                <input type="number" name="volumen_total_dilucion" value="{{ old('volumen_total_dilucion', $mezcla->volumen_total_dilucion) }}" class="w-full border rounded px-2 py-1">
            </div>
            <div>
                <label>Tiempo de infusión (min)</label>
                <input type="number" name="tiempo_infusion" value="{{ old('tiempo_infusion', $mezcla->tiempo_infusion) }}" class="w-full border rounded px-2 py-1">
            </div>
        </div>

        <div class="border border-gray-300 p-4 rounded">
            <h2 class="text-lg font-semibold mb-2">Medicamentos</h2>
            <table class="w-full text-sm border mb-4" id="tablaMedicamentos">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border px-4 py-2">Medicamento</th>
                        <th class="border px-4 py-2">Dosis</th>
                        <th class="border px-4 py-2">Diluyente</th>
                        <th class="border px-4 py-2">Vía</th>
                        <th class="border px-4 py-2">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($mezcla->medicamentos as $index => $med)
                        <tr>
                            <td class="border px-2 py-1">
                                <select name="medicamentos[{{ $index }}][medicamento_id]" class="w-full border rounded">
                                    @foreach ($medicamentos as $m)
                                        <option value="{{ $m->id }}" @selected($m->denominacion === $med->nombre_medicamento)>{{ $m->denominacion }} ({{ $m->presentacion }})</option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="border px-2 py-1"><input type="number" name="medicamentos[{{ $index }}][dosis]" value="{{ $med->dosis }}" class="w-full border rounded"></td>
                            <td class="border px-2 py-1"><input type="text" name="medicamentos[{{ $index }}][diluyente]" value="{{ $med->diluyente }}" class="w-full border rounded"></td>
                            <td class="border px-2 py-1"><input type="text" name="medicamentos[{{ $index }}][via_administracion]" value="{{ $med->via_administracion }}" class="w-full border rounded"></td>
                            <td class="border px-2 py-1 text-center">
                                <button type="button" class="text-red-600 font-bold" onclick="this.closest('tr').remove()">✕</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <button type="button" id="btnAgregarMedicamento" class="bg-green-600 text-white px-3 py-1 rounded text-sm hover:bg-green-700">+ Agregar Medicamento</button>
        </div>

        <div class="flex justify-end">
            <x-button>Actualizar Mezcla</x-button>
        </div>
    </form>

    <script>
        const medicamentos = @json($medicamentos);
        let index = {{ count($mezcla->medicamentos) }};

        document.getElementById('btnAgregarMedicamento').addEventListener('click', () => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td class="border px-2 py-1">
                    <select name="medicamentos[${index}][medicamento_id]" class="w-full border rounded">
                        ${medicamentos.map(m => `<option value="${m.id}">${m.denominacion} (${m.presentacion})</option>`).join('')}
                    </select>
                </td>
                <td class="border px-2 py-1"><input type="number" name="medicamentos[${index}][dosis]" class="w-full border rounded"></td>
                <td class="border px-2 py-1"><input type="text" name="medicamentos[${index}][diluyente]" class="w-full border rounded"></td>
                <td class="border px-2 py-1"><input type="text" name="medicamentos[${index}][via_administracion]" class="w-full border rounded"></td>
                <td class="border px-2 py-1 text-center">
                    <button type="button" class="text-red-600 font-bold" onclick="this.closest('tr').remove()">✕</button>
                </td>
            `;
            document.querySelector('#tablaMedicamentos tbody').appendChild(tr);
            index++;
        });
    </script>
</x-admin-layout>
