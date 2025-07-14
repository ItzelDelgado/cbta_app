<x-admin-layout>
    <div class="flex flex-col ">
        <div class="mt-2 mb-4">
            <h1 class="text-2xl font-medium text-gray-800">Editar Solicitud</h1>

            @if ($errors->any())
                <div class="bg-red-100 text-red-700 p-4 rounded mb-4">
                    <strong>Se encontraron los siguientes errores:</strong>
                    <ul class="list-disc pl-6">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('success'))
                <div class="bg-green-100 text-green-800 p-4 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif
        </div>

        <form id="formularioSolicitud" action="{{ route('admin.oncologicos.solicitudes.update', $solicitud->id) }}"
            method="POST" class="bg-white rounded-lg p-6 shadow-lg">
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
                    <input type="text" name="servicio" id="servicio"
                        value="{{ old('servicio', $solicitud->servicio) }}"
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

            <!-- Mezclas -->
            <div class="mt-4">
                <div id="contenedorMezclas"></div>
                <input type="hidden" name="mezclas" id="mezclas_json">
            </div>
            <div class="my-4">
                <button type="button" onclick="agregarMezcla()"
                    class="bg-green-500 text-white px-4 py-2 rounded text-sm">
                    <i class="fas fa-plus"></i> Agregar Mezcla
                </button>
            </div>

            <div class="flex justify-end gap-5 mt-4">
                <x-button>
                    ACTUALIZAR SOLICITUD
                </x-button>
            </div>
        </form>

    </div>

    <script>
        const medicamentos = @json($medicamentos);
        const infoAdicional = @json($infoAdicional);
        const mezclasCargadas = @json($solicitud->mezclas);

        let idInternoMezcla = 0;
        let contadorFilasGlobal = 0;

        function agregarMezcla() {
            idInternoMezcla++;
            const mezclaDiv = document.createElement('div');
            mezclaDiv.classList.add("border", "border-black", "p-4", "relative");
            mezclaDiv.dataset.idInterno = idInternoMezcla;

            mezclaDiv.innerHTML = `
        <div class="flex justify-between items-center mb-2">
            <h3 class="text-lg font-semibold mezcla-titulo">Mezcla</h3>
            <button type="button" onclick="eliminarMezcla(this)" class="bg-red-600 text-white px-3 py-1 rounded text-sm">
                <i class="fas fa-trash"></i> Eliminar Mezcla
            </button>
        </div>
        <table class="table-auto w-full border text-center">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border px-4 py-2 text-xs">MEDICAMENTO</th>
                    <th class="border px-4 py-2 text-xs">DOSIS</th>
                    <th class="border px-4 py-2 text-xs">DILUYENTE</th>
                    <th class="border px-4 py-2 text-xs">VÍA DE ADMINISTRACIÓN</th>
                </tr>
            </thead>
            <tbody id="medicamentos_mezcla_${idInternoMezcla}"></tbody>
        </table>
        <div class="grid grid-cols-2 gap-4 mt-4">
            <div>
                <label>Volumen total de dilución (ml)*</label>
                <input type="number" data-name="volumen_dilucion" class="w-full border rounded px-2 py-1 text-sm">
            </div>
            <div>
                <label>Tiempo de infusión (min)*</label>
                <input type="number" data-name="tiempo_infusion" class="w-full border rounded px-2 py-1 text-sm">
            </div>
        </div>
        <div class="mt-4">
            <button type="button" class="btn-agregar-medicamento bg-green-600 hover:bg-green-700 text-white text-xs font-medium py-1 px-2 rounded">
                + Agregar Medicamento
            </button>
        </div>
        `;

            document.getElementById('contenedorMezclas').appendChild(mezclaDiv);
            actualizarNumeracionMezclas();
        }

        function eliminarMezcla(btn) {
            const mezcla = btn.closest(".border");
            mezcla.remove();
            actualizarNumeracionMezclas();
        }

        function agregarMezclaCargada(m) {
            idInternoMezcla++;
            const mezclaDiv = document.createElement('div');
            mezclaDiv.classList.add("border", "border-black", "p-4", "relative", "mezcla-existente");
            mezclaDiv.dataset.idInterno = idInternoMezcla;

            mezclaDiv.innerHTML = `
    <div class="flex justify-between items-center mb-2">
        <h3 class="text-lg font-semibold mezcla-titulo">Mezcla</h3>
    </div>
    <table class="table-auto w-full border text-center">
        <thead class="bg-gray-100">
            <tr>
                <th class="border px-4 py-2 text-xs">MEDICAMENTO</th>
                <th class="border px-4 py-2 text-xs">DOSIS</th>
                <th class="border px-4 py-2 text-xs">DILUYENTE</th>
                <th class="border px-4 py-2 text-xs">VÍA DE ADMINISTRACIÓN</th>
            </tr>
        </thead>
        <tbody id="medicamentos_mezcla_${idInternoMezcla}"></tbody>
    </table>
    <div class="grid grid-cols-2 gap-4 mt-4">
        <div>
            <label>Volumen total de dilución (ml)*</label>
            <input type="number" data-name="volumen_dilucion" class="w-full border rounded px-2 py-1 text-sm" value="${m.volumen_dilucion}" disabled>
        </div>
        <div>
            <label>Tiempo de infusión (min)*</label>
            <input type="number" data-name="tiempo_infusion" class="w-full border rounded px-2 py-1 text-sm" value="${m.tiempo_infusion}" disabled>
        </div>
    </div>
    `;

            document.getElementById('contenedorMezclas').appendChild(mezclaDiv);

            const tbody = mezclaDiv.querySelector(`#medicamentos_mezcla_${idInternoMezcla}`);
            m.medicamentos.forEach(med => {
                contadorFilasGlobal++;
                const fila = document.createElement('tr');
                fila.id = `fila_m${idInternoMezcla}_f${contadorFilasGlobal}`;

                const data = infoAdicional[med.medicamento_id] || {
                    diluyentes: [],
                    vias: []
                };

                const diluyenteOptions = data.diluyentes.map(d =>
                    `<option value="${d.id}" ${d.id == med.diluyente_id ? 'selected' : ''}>${d.name}</option>`
                ).join('');

                const viaOptions = data.vias.map(v =>
                    `<option value="${v.id}" ${v.id == med.via_administracion_id ? 'selected' : ''}>${v.name}</option>`
                ).join('');

                fila.innerHTML = `
        <td class="border">
            <select class="medicamento-select w-full border px-2 py-1 text-sm" disabled>
                ${medicamentos.map(m =>
                    `<option value="${m.id}" ${m.id == med.medicamento_id ? 'selected' : ''}>${m.denominacion} (${m.presentacion})</option>`
                ).join('')}
            </select>
        </td>
        <td class="border">
            <input type="number" value="${med.dosis}" class="w-full border px-2 py-1 text-sm" disabled>
        </td>
        <td class="border">
            <select data-name="diluyente" class="w-full border rounded px-2 py-1 text-sm" disabled>
                <option value="">Diluyentes</option>
                ${diluyenteOptions}
            </select>
        </td>
        <td class="border">
            <select data-name="via_administracion" class="w-full border rounded px-2 py-1 text-sm" disabled>
                <option value="">Vía de admin</option>
                ${viaOptions}
            </select>
        </td>
        `;

                tbody.appendChild(fila);
            });

            actualizarNumeracionMezclas();
        }


        function actualizarNumeracionMezclas() {
            document.querySelectorAll('#contenedorMezclas > .border').forEach((mezcla, index) => {
                mezcla.querySelector('.mezcla-titulo').textContent = `Mezcla #${index + 1}`;
            });
        }

        function actualizarDiluentesYVias(selectElem, idMezcla, idFila) {
            const medicamentoId = selectElem.value;
            const fila = document.getElementById(`fila_m${idMezcla}_f${idFila}`);
            const data = infoAdicional[medicamentoId] || {
                diluyentes: [],
                vias: []
            };

            const selectDiluyente = fila.querySelector('[data-name="diluyente"]');
            const selectVia = fila.querySelector('[data-name="via_administracion"]');

            selectDiluyente.innerHTML = `<option value="">Diluyentes</option>` + data.diluyentes.map(d =>
                `<option value="${d.id}">${d.name}</option>`).join('');
            selectVia.innerHTML = `<option value="">Vía de admin</option>` + data.vias.map(v =>
                `<option value="${v.id}">${v.name}</option>`).join('');
        }

        document.addEventListener("DOMContentLoaded", () => {
            if (mezclasCargadas.length > 0) {
                mezclasCargadas.forEach(m => agregarMezclaCargada(m));
            }
        });

        document.addEventListener("click", function(e) {
            if (e.target.classList.contains("btn-agregar-medicamento")) {
                const mezclaDiv = e.target.closest(".border");
                const idMezcla = mezclaDiv.dataset.idInterno;
                const tbody = mezclaDiv.querySelector(`#medicamentos_mezcla_${idMezcla}`);

                contadorFilasGlobal++;
                const fila = document.createElement("tr");
                fila.id = `fila_m${idMezcla}_f${contadorFilasGlobal}`;

                fila.innerHTML = `
            <td class="border">
                <select class="medicamento-select w-full border px-2 py-1 text-sm" name="medicamento[]"
                    onchange="actualizarDiluentesYVias(this, ${idMezcla}, ${contadorFilasGlobal})">
                    <option value="">Seleccione</option>
                    ${medicamentos.map(med => `<option value="${med.id}">${med.denominacion} (${med.presentacion})</option>`).join('')}
                </select>
            </td>
            <td class="border">
                <input type="number" name="dosis[]" class="w-full border px-2 py-1 text-sm">
            </td>
            <td class="border">
                <select name="diluyente[]" data-name="diluyente" class="w-full border rounded px-2 py-1 text-sm">
                    <option value="">Diluyentes</option>
                </select>
            </td>
            <td class="border">
                <select name="via_administracion[]" data-name="via_administracion" class="w-full border rounded px-2 py-1 text-sm">
                    <option value="">Vía de admin</option>
                </select>
            </td>
            `;

                tbody.appendChild(fila);
            }
        });

        document.getElementById("formularioSolicitud").addEventListener("submit", function(e) {
            e.preventDefault();
            const mezclas = [];

            document.querySelectorAll('#contenedorMezclas > .border').forEach((mezclaDiv) => {
                const idInterno = mezclaDiv.dataset.idInterno;
                const volumen = mezclaDiv.querySelector(`[data-name="volumen_dilucion"]`)?.value;
                const tiempo = mezclaDiv.querySelector(`[data-name="tiempo_infusion"]`)?.value;

                const medicamentos = [];
                mezclaDiv.querySelectorAll(`#medicamentos_mezcla_${idInterno} tr`).forEach((fila) => {
                    const medicamentoSelect = fila.querySelector(`[name^="medicamento"]`);
                    const dosisInput = fila.querySelector(`[name^="dosis"]`);
                    const diluyenteSelect = fila.querySelector(`[data-name="diluyente"]`);
                    const viaSelect = fila.querySelector(`[data-name="via_administracion"]`);

                    if (medicamentoSelect && dosisInput && medicamentoSelect.value !== "") {
                        medicamentos.push({
                            medicamento_id: medicamentoSelect.value,
                            nombre: medicamentoSelect.options[medicamentoSelect
                                .selectedIndex].text,
                            dosis: dosisInput.value,
                            diluyente_id: diluyenteSelect?.value || null,
                            via_administracion_id: viaSelect?.value || null
                        });
                    }
                });

                if (medicamentos.length > 0) {
                    const mezclaObj = {
                        volumen_dilucion: volumen,
                        tiempo_infusion: tiempo,
                        medicamentos: medicamentos
                    };

                    if (mezclaDiv.classList.contains("mezcla-existente")) {
                        mezclaObj.existente = true;
                    }

                    mezclas.push(mezclaObj);
                }
            });

            document.getElementById("mezclas_json").value = JSON.stringify(mezclas);

            Swal.fire({
                title: '¿Actualizar solicitud?',
                text: 'Se guardarán los cambios realizados',
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
    </script>


    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: '¡Éxito!',
                text: '{{ session('success') }}',
                customClass: {
                    confirmButton: 'swal-button-confirm',
                    cancelButton: 'swal-button-cancel'
                }
            });
        </script>
    @endif

    @if ($errors->any())
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                html: `{!! implode('<br>', $errors->all()) !!}`,
                customClass: {
                    confirmButton: 'swal-button-confirm',
                    cancelButton: 'swal-button-cancel'
                }
            });
        </script>
    @endif
</x-admin-layout>
