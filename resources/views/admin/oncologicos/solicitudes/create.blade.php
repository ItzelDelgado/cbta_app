<x-admin-layout>
    <div class="flex flex-col ">
        <div class="mt-2 mb-4">
            <h1 class="text-2xl font-medium text-gray-800">Crear Nueva Solicitud</h1>
        </div>
        <form id="formularioSolicitud" action="{{ route('admin.oncologicos.solicitudes.store') }}" method="POST"
            class="bg-white rounded-lg p-6 shadow-lg">
            @csrf
            <div class="flex justify-between mb-4 gap-4">
                <div class="w-1/4">
                    <label for="">Paciente Nombre(s)</label>
                    <input type="text" name="paciente_nombre" id="paciente_nombre"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                        placeholder="Nombre(s) del Paciente">
                </div>
                <div class="w-1/4">
                    <label for="">Paciente Apellido(s)</label>
                    <input type="text" name="paciente_apellido" id="paciente_apellido"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                        placeholder="Apellido(s) del Paciente">
                </div>
                <div class="w-1/4">
                    <label for="">Servicio*</label>
                    <input type="text" name="servicio" id="servicio"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                        placeholder="Servicio">
                </div>
                <div class="w-1/4">
                    <label for="">Registro*</label>
                    <input type="text" name="registro" id="registro"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                        placeholder="Registro">
                </div>
            </div>

            <div class="flex justify-between mb-4 gap-4">
                <div class="w-1/5">
                    <label for="">Sexo</label>
                    <select name="sexo" id="sexo"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <option value="">Seleccione</option>
                        <option value="M">Masculino</option>
                        <option value="F">Femenino</option>
                    </select>
                </div>
                <div class="w-1/5">
                    <label for="">Fecha de Nacimiento</label>
                    <input type="date" name="fecha_nacimiento" id="fecha_nacimiento"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                </div>
                <div class="w-1/5">
                    <label for="">Peso*</label>
                    <input type="number" name="peso" id="peso"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                        placeholder="Peso">
                </div>
                <div class="w-1/5">
                    <label for="">Piso*</label>
                    <input type="text" name="piso" id="piso"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                        placeholder="Piso">
                </div>
                <div class="w-1/5">
                    <label for="">Cama*</label>
                    <input type="text" name="cama" id="cama"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                        placeholder="Cama">
                </div>
            </div>

            <div class="flex justify-between mb-4 gap-4">
                <div class="w-1/4">
                    <label for="">Diagnóstico</label>
                    <input type="text" name="diagnostico" id="diagnostico"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                        placeholder="Diagnóstico">
                </div>
                <div class="w-1/4">
                    <label for="">Nombre del Médico</label>
                    <input type="text" name="medico_nombre" id="medico_nombre"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                        placeholder="Nombre del Médico">
                </div>
                <div class="w-1/4">
                    <label for="">Cédula del Médico</label>
                    <input type="text" name="medico_cedula" id="medico_cedula"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                        placeholder="Cédula del Médico">
                </div>
                <div class="w-1/4">
                    <label for="">Fecha de entrega*</label>
                    <input type="datetime-local" name="fecha_entrega" id="fecha_entrega"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                </div>
            </div>

            <div>
                <div>
                    <label for="">Observaciones</label>
                    <input type="text" name="observaciones" id="observaciones"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                        placeholder="Observaciones">
                </div>
            </div>
            <div class="mt-4">
                <div id="contenedorMezclas"></div>
                <input type="hidden" name="mezclas" id="mezclas_json">

                <div class="my-4">
                    <button type="button" onclick="agregarMezcla()"
                        class="bg-green-500 text-white px-4 py-2 rounded text-sm">
                        <i class="fas fa-plus"></i> Agregar Mezcla
                    </button>
                </div>
            </div>

            <div class="flex justify-end gap-5 mt-4">
                <x-button>
                    GUARDAR SOLICITUD
                </x-button>
            </div>
        </form>
    </div>

    <script>
        const medicamentos = @json($medicamentos);
        const infoAdicional = @json($infoAdicional);

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
                            <th class="border px-4 py-2 text-xs">ACCIÓN</th>
                        </tr>
                    </thead>
                    <tbody id="medicamentos_mezcla_${idInternoMezcla}">
                    </tbody>
                </table>
                <div class="my-2">
                    <button type="button" onclick="agregarFilaMedicamento(${idInternoMezcla})" class="bg-green-500 text-white px-4 py-2 rounded text-sm">
                        <i class="fas fa-plus"></i> Agregar Medicamento
                    </button>
                </div>
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
            `;

            document.getElementById('contenedorMezclas').appendChild(mezclaDiv);
            agregarFilaMedicamento(idInternoMezcla);
            actualizarNumeracionMezclas();
        }

        function agregarFilaMedicamento(idMezcla) {
            contadorFilasGlobal++;
            const tbody = document.getElementById(`medicamentos_mezcla_${idMezcla}`);
            const fila = document.createElement('tr');
            fila.id = `fila_m${idMezcla}_f${contadorFilasGlobal}`;

            fila.innerHTML = `
                <td class="border">
                    <select data-name="medicamento" class="w-full border rounded px-2 py-1 text-sm"
                        onchange="actualizarDiluentesYVias(this, ${idMezcla}, ${contadorFilasGlobal})">
                        <option value="">Seleccione el medicamento</option>
                        ${medicamentos.map(m => `<option value="${m.id}">${m.denominacion} (${m.presentacion})</option>`).join('')}
                    </select>
                </td>
                <td class="border">
                    <input type="number" data-name="dosis" class="w-full border-none px-2 py-1 text-sm">
                </td>
                <td class="border">
                    <select data-name="diluyente" class="w-full border rounded px-2 py-1 text-sm">
                        <option value="">Diluyentes</option>
                        <option value="NaCl">NaCl 0.9%</option>
                        <option value="Glucosa">Glucosa 5%</option>
                    </select>
                </td>
                <td class="border">
                    <select data-name="via_administracion" class="w-full border rounded px-2 py-1 text-sm">
                        <option value="">Vía de admin</option>
                        <option value="IV">Intravenosa</option>
                        <option value="IM">Intramuscular</option>
                    </select>
                </td>
                <td class="border">
                    <button type="button" onclick="eliminarFila('${fila.id}')" class="bg-red-500 text-white px-2 py-1 rounded">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            `;

            tbody.appendChild(fila);
        }

        function eliminarMezcla(btn) {
            btn.closest(".border").remove();
            actualizarNumeracionMezclas();
        }

        function eliminarFila(idFila) {
            document.getElementById(idFila)?.remove();
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

            const selectDiluyente = fila.querySelector(`[data-name="diluyente"]`);
            selectDiluyente.innerHTML = `<option value="">Diluyentes</option>` + data.diluyentes.map(d =>
                `<option value="${d.id}">${d.name}</option>`).join('');

            const selectVia = fila.querySelector(`[data-name="via_administracion"]`);
            selectVia.innerHTML = `<option value="">Vía de admin</option>` + data.vias.map(v =>
                `<option value="${v.id}">${v.name}</option>`).join('');
        }

        document.getElementById("formularioSolicitud").addEventListener("submit", function(e) {
            const mezclas = [];

            document.querySelectorAll('#contenedorMezclas > .border').forEach((mezclaDiv) => {
                const idInterno = mezclaDiv.dataset.idInterno;
                const volumen = mezclaDiv.querySelector(`[data-name="volumen_dilucion"]`)?.value;
                const tiempo = mezclaDiv.querySelector(`[data-name="tiempo_infusion"]`)?.value;


                const medicamentos = [];
                mezclaDiv.querySelectorAll(`#medicamentos_mezcla_${idInterno} tr`).forEach((fila) => {
                    const medicamentoSelect = fila.querySelector(`[data-name="medicamento"]`);
                    const dosisInput = fila.querySelector(`[data-name="dosis"]`);
                    const diluyenteSelect = fila.querySelector(`[data-name="diluyente"]`);
                    const viaSelect = fila.querySelector(`[data-name="via_administracion"]`);

                    if (medicamentoSelect && dosisInput && medicamentoSelect.value !== "") {
                        medicamentos.push({
                            medicamento_id: medicamentoSelect.value,
                            nombre: medicamentoSelect.options[medicamentoSelect
                                .selectedIndex].text,
                            dosis: dosisInput.value,
                            diluyente: diluyenteSelect?.value || "",
                            via_administracion: viaSelect?.value || ""
                        });
                    }
                });

                if (medicamentos.length > 0) {
                    mezclas.push({
                        volumen_dilucion: volumen,
                        tiempo_infusion: tiempo,
                        medicamentos: medicamentos
                    });
                }
            });

            document.getElementById("mezclas_json").value = JSON.stringify(mezclas);
        });

        document.addEventListener("DOMContentLoaded", () => agregarMezcla());
    </script>
</x-admin-layout>
