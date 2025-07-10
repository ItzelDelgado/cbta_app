<x-admin-layout>
    <div class="mb-4">
        <h1 class="text-2xl font-bold">Editar Mezcla #{{ $mezcla->id }}</h1>

    </div>

    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <strong>Se encontraron errores:</strong>
            <ul class="mt-2 list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-800 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <form id="formularioMezcla" action="{{ route('admin.oncologicos.mezclas.update', $mezcla->id) }}" method="POST"
        class="bg-white rounded-lg p-6 shadow">
        @csrf
        @method('PUT')

        <div class="flex justify-between mb-4 gap-4">
            <div class="w-1/3">
                <label for="paciente_nombre">Paciente Nombre(s)</label>
                <input type="text" name="paciente_nombre" id="paciente_nombre"
                    value="{{ old('paciente_nombre', $solicitud->nombre_paciente) }}"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                    placeholder="Nombre(s) del Paciente">
            </div>
            <div class="w-1/3">
                <label for="servicio">Servicio*</label>
                <input type="text" name="servicio" id="servicio" value="{{ old('servicio', $solicitud->servicio) }}"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                    placeholder="Servicio">
            </div>
            <div class="w-1/3">
                <label for="registro">Registro*</label>
                <input type="text" name="registro" id="registro"
                    value="{{ old('registro', $solicitud->registro_paciente) }}"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                    placeholder="Registro">
            </div>
        </div>

        <div class="flex justify-between mb-4 gap-4">
            <div class="w-1/5">
                <label for="sexo">Sexo</label>
                <select name="sexo" id="sexo"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    <option value="">Seleccione</option>
                    <option value="M" @selected(old('sexo', $solicitud->sexo) === 'M')>Masculino</option>
                    <option value="F" @selected(old('sexo', $solicitud->sexo) === 'F')>Femenino</option>
                </select>
            </div>
            <div class="w-1/5">
                <label for="fecha_nacimiento">Fecha de Nacimiento</label>
                <input type="date" name="fecha_nacimiento" id="fecha_nacimiento"
                    value="{{ old('fecha_nacimiento', $solicitud->fecha_nacimiento ? \Carbon\Carbon::parse($solicitud->fecha_nacimiento)->format('Y-m-d') : '') }}"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
            </div>
            <div class="w-1/5">
                <label for="peso">Peso*</label>
                <input type="number" name="peso" id="peso" value="{{ old('peso', $solicitud->peso) }}"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                    placeholder="Peso">
            </div>
            <div class="w-1/5">
                <label for="piso">Piso*</label>
                <input type="text" name="piso" id="piso" value="{{ old('piso', $solicitud->piso) }}"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                    placeholder="Piso">
            </div>
            <div class="w-1/5">
                <label for="cama">Cama*</label>
                <input type="text" name="cama" id="cama" value="{{ old('cama', $solicitud->cama) }}"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                    placeholder="Cama">
            </div>
        </div>

        <div class="flex justify-between mb-4 gap-4">
            <div class="w-1/4">
                <label for="diagnostico">Diagnóstico</label>
                <input type="text" name="diagnostico" id="diagnostico"
                    value="{{ old('diagnostico', $solicitud->diagnostico) }}"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                    placeholder="Diagnóstico">
            </div>
            <div class="w-1/4">
                <label for="medico_nombre">Nombre del Médico</label>
                <input type="text" name="medico_nombre" id="medico_nombre"
                    value="{{ old('medico_nombre', $solicitud->nombre_medico) }}"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                    placeholder="Nombre del Médico">
            </div>
            <div class="w-1/4">
                <label for="medico_cedula">Cédula del Médico</label>
                <input type="text" name="medico_cedula" id="medico_cedula"
                    value="{{ old('medico_cedula', $solicitud->cedula_medico) }}"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                    placeholder="Cédula del Médico">
            </div>
            <div class="w-1/4">
                <label for="fecha_entrega">Fecha de entrega*</label>
                <input type="datetime-local" name="fecha_entrega" id="fecha_entrega"
                    value="{{ old('fecha_entrega', $solicitud->fecha_entrega ? \Carbon\Carbon::parse($solicitud->fecha_entrega)->format('Y-m-d\TH:i') : '') }}"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
            </div>
        </div>

        <div class="mb-4">
            <label for="observaciones">Observaciones</label>
            <input type="text" name="observaciones" id="observaciones"
                value="{{ old('observaciones', $solicitud->observaciones) }}"
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                placeholder="Observaciones">
        </div>



        <div id="contenedorMezcla"></div>
        <input type="hidden" name="mezcla_json" id="mezcla_json">

        <div class="flex justify-end mt-4 gap-4">
            <input type="hidden" name="accion" id="accion" value="actualizar">

            <x-button onclick="document.getElementById('accion').value='actualizar'">ACTUALIZAR MEZCLA</x-button>

            @if ($mezcla->estado === 'pendiente')
                <x-button type="submit" class="bg-green-600 hover:bg-green-700"
                    onclick="document.getElementById('accion').value='aprobar'">APROBAR MEZCLA</x-button>
            @endif
        </div>
    </form>

    <script>
        const medicamentos = @json($medicamentos);
        const infoAdicional = @json($infoAdicional);
        const mezcla = @json($mezcla);

        let contadorFilas = 0;

        function renderMezcla(mezclaData) {
            const mezclaDiv = document.createElement('div');
            mezclaDiv.classList.add("border", "border-black", "p-4", "relative");

            mezclaDiv.innerHTML = `
            <h3 class="text-lg font-semibold mb-4">Mezcla</h3>
            <table class="table-auto w-full border text-center mb-4">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border px-2 py-1 text-xs">MEDICAMENTO</th>
                        <th class="border px-2 py-1 text-xs">DOSIS</th>
                        <th class="border px-2 py-1 text-xs">DILUYENTE</th>
                        <th class="border px-2 py-1 text-xs">VÍA DE ADMINISTRACIÓN</th>
                    </tr>
                </thead>
                <tbody id="medicamentos_mezcla"></tbody>
            </table>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label>Volumen de dilución (ml)*</label>
                    <input type="number" data-name="volumen_dilucion" class="w-full border rounded px-2 py-1 text-sm" value="${mezclaData.volumen_dilucion}">
                </div>
                <div>
                    <label>Tiempo de infusión (min)*</label>
                    <input type="number" data-name="tiempo_infusion" class="w-full border rounded px-2 py-1 text-sm" value="${mezclaData.tiempo_infusion}">
                </div>
            </div>

            <button type="button" class="btn-agregar-medicamento bg-green-600 hover:bg-green-700 text-white text-xs font-medium py-1 px-2 rounded">
                + Agregar Medicamento
            </button>
        `;

            document.getElementById('contenedorMezcla').appendChild(mezclaDiv);

            const tbody = mezclaDiv.querySelector(`#medicamentos_mezcla`);
            mezclaData.medicamentos.forEach(med => {
                contadorFilas++;
                const fila = document.createElement('tr');
                fila.id = `fila_${contadorFilas}`;

                const medicamentoId = med.medicamento_id;
                const data = infoAdicional[medicamentoId] || {
                    diluyentes: [],
                    vias: []
                };

                const diluyenteOptions = data.diluyentes.map(d =>
                        `<option value="${d.id}" ${d.id == med.diluyente_id ? 'selected' : ''}>${d.name}</option>`)
                    .join('');

                const viaOptions = data.vias.map(v =>
                    `<option value="${v.id}" ${v.id == med.via_administracion_id ? 'selected' : ''}>${v.name}</option>`
                ).join('');

                fila.innerHTML = `
                <td class="border">
                    <select class="medicamento-select w-full border px-2 py-1 text-sm" name="medicamento_existente[]"
                        onchange="actualizarDiluentesYVias(this, ${contadorFilas})">
                        ${medicamentos.map(m =>
                            `<option value="${m.id}" ${m.id == medicamentoId ? 'selected' : ''}>${m.denominacion} (${m.presentacion})</option>`
                        ).join('')}
                    </select>
                </td>
                <td class="border">
                    <input type="number" name="dosis_existente[]" value="${med.dosis}" class="w-full border px-2 py-1 text-sm">
                </td>
                <td class="border">
                    <select name="diluyente_existente[]" data-name="diluyente" class="w-full border px-2 py-1 text-sm">
                        <option value="">Diluyentes</option>
                        ${diluyenteOptions}
                    </select>
                </td>
                <td class="border">
                    <select name="via_existente[]" data-name="via_administracion" class="w-full border px-2 py-1 text-sm">
                        <option value="">Vía de admin</option>
                        ${viaOptions}
                    </select>
                </td>
            `;
                tbody.appendChild(fila);
            });
        }

        function actualizarDiluentesYVias(selectElem, filaId) {
            const medicamentoId = selectElem.value;
            const fila = document.getElementById(`fila_${filaId}`);
            const data = infoAdicional[medicamentoId] || {
                diluyentes: [],
                vias: []
            };

            const selectDiluyente = fila.querySelector('[data-name="diluyente"]');
            const selectVia = fila.querySelector('[data-name="via_administracion"]');

            selectDiluyente.innerHTML = `<option value="">Diluyentes</option>` +
                data.diluyentes.map(d => `<option value="${d.id}">${d.name}</option>`).join('');

            selectVia.innerHTML = `<option value="">Vía de admin</option>` +
                data.vias.map(v => `<option value="${v.id}">${v.name}</option>`).join('');
        }

        document.addEventListener("DOMContentLoaded", () => {
            renderMezcla(mezcla);
        });

        document.addEventListener("click", function(e) {
            if (e.target.classList.contains("btn-agregar-medicamento")) {
                const tbody = document.querySelector("#medicamentos_mezcla");
                contadorFilas++;
                const fila = document.createElement("tr");
                fila.id = `fila_${contadorFilas}`;

                fila.innerHTML = `
                <td class="border">
                    <select class="medicamento-select w-full border px-2 py-1 text-sm" name="nuevo_medicamento[]"
                        onchange="actualizarDiluentesYVias(this, ${contadorFilas})">
                        <option value="">Seleccione</option>
                        ${medicamentos.map(m => `<option value="${m.id}">${m.denominacion} (${m.presentacion})</option>`).join('')}
                    </select>
                </td>
                <td class="border">
                    <input type="number" name="nueva_dosis[]" class="w-full border px-2 py-1 text-sm">
                </td>
                <td class="border">
                    <select name="nuevo_diluyente[]" data-name="diluyente" class="w-full border px-2 py-1 text-sm">
                        <option value="">Diluyentes</option>
                    </select>
                </td>
                <td class="border">
                    <select name="nueva_via[]" data-name="via_administracion" class="w-full border px-2 py-1 text-sm">
                        <option value="">Vía de admin</option>
                    </select>
                </td>
            `;
                tbody.appendChild(fila);
            }
        });

        document.getElementById("formularioMezcla").addEventListener("submit", function(e) {
            e.preventDefault();

            const mezclaPayload = {
                volumen_dilucion: document.querySelector('[data-name="volumen_dilucion"]').value,
                tiempo_infusion: document.querySelector('[data-name="tiempo_infusion"]').value,
                medicamentos: []
            };

            // MEDICAMENTOS EXISTENTES
            document.querySelectorAll('[name="medicamento_existente[]"]').forEach((medicamentoSelect, i) => {
                const fila = medicamentoSelect.closest('tr');
                mezclaPayload.medicamentos.push({
                    medicamento_id: medicamentoSelect.value,
                    nombre: medicamentoSelect.options[medicamentoSelect.selectedIndex]
                    .text, // ← nombre
                    dosis: fila.querySelector('[name="dosis_existente[]"]').value,
                    diluyente_id: fila.querySelector('[name="diluyente_existente[]"]').value ||
                        null,
                    via_administracion_id: fila.querySelector('[name="via_existente[]"]').value ||
                        null
                });
            });

            // MEDICAMENTOS NUEVOS
            document.querySelectorAll('[name="nuevo_medicamento[]"]').forEach((medicamentoSelect, i) => {
                const fila = medicamentoSelect.closest('tr');
                if (medicamentoSelect.value) {
                    mezclaPayload.medicamentos.push({
                        medicamento_id: medicamentoSelect.value,
                        nombre: medicamentoSelect.options[medicamentoSelect.selectedIndex]
                        .text, // ← nombre
                        dosis: fila.querySelector('[name="nueva_dosis[]"]').value,
                        diluyente_id: fila.querySelector('[name="nuevo_diluyente[]"]').value ||
                            null,
                        via_administracion_id: fila.querySelector('[name="nueva_via[]"]').value ||
                            null
                    });
                }
            });

            // Guardar en el input hidden
            document.getElementById("mezcla_json").value = JSON.stringify(mezclaPayload);

            Swal.fire({
                title: '¿Actualizar mezcla?',
                text: 'Se guardarán los cambios realizados.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sí, actualizar',
                cancelButtonText: 'Cancelar',
                customClass: {
                    confirmButton: 'swal-button-confirm',
                    cancelButton: 'swal-button-cancel'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    e.target.submit();
                }
            });
        });


        @if ($errors->any())
            Swal.fire({
                icon: 'error',
                title: 'Error al actualizar',
                html: `{!! implode('<br>', $errors->all()) !!}`,
                customClass: {
                    confirmButton: 'swal-button-confirm',
                    cancelButton: 'swal-button-cancel'
                }
            });
        @endif

        @if (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: '{{ session('error') }}',
                customClass: {
                    confirmButton: 'swal-button-confirm',
                    cancelButton: 'swal-button-cancel'
                }
            });
        @endif

        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: '¡Éxito!',
                text: '{{ session('success') }}',
                customClass: {
                    confirmButton: 'swal-button-confirm',
                    cancelButton: 'swal-button-cancel'
                }
            });
        @endif
    </script>

</x-admin-layout>
