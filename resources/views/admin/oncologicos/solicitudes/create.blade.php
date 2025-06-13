<x-admin-layout>
    <div class="flex flex-col ">
        <div class="mt-2 mb-4">
            <h1 class="text-2xl font-medium text-gray-800">Crear Nueva Solicitud</h1>

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

        <form id="formularioSolicitud" action="{{ route('admin.oncologicos.solicitudes.store') }}" method="POST"
            class="bg-white rounded-lg p-6 shadow-lg">
            @csrf
            <div class="flex justify-between mb-4 gap-4">
                <div class="w-1/4">
                    <label for="paciente_nombre">Paciente Nombre(s)</label>
                    <input type="text" name="paciente_nombre" id="paciente_nombre"
                        value="{{ old('paciente_nombre') }}"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                        placeholder="Nombre(s) del Paciente">
                </div>
                <div class="w-1/4">
                    <label for="paciente_apellido">Paciente Apellido(s)</label>
                    <input type="text" name="paciente_apellido" id="paciente_apellido"
                        value="{{ old('paciente_apellido') }}"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                        placeholder="Apellido(s) del Paciente">
                </div>
                <div class="w-1/4">
                    <label for="servicio">Servicio*</label>
                    <input type="text" name="servicio" id="servicio" value="{{ old('servicio') }}"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                        placeholder="Servicio">
                </div>
                <div class="w-1/4">
                    <label for="registro">Registro*</label>
                    <input type="text" name="registro" id="registro" value="{{ old('registro') }}"
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
                        <option value="M" @selected(old('sexo') === 'M')>Masculino</option>
                        <option value="F" @selected(old('sexo') === 'F')>Femenino</option>
                    </select>
                </div>
                <div class="w-1/5">
                    <label for="fecha_nacimiento">Fecha de Nacimiento</label>
                    <input type="date" name="fecha_nacimiento" id="fecha_nacimiento"
                        value="{{ old('fecha_nacimiento') }}"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                </div>
                <div class="w-1/5">
                    <label for="peso">Peso*</label>
                    <input type="number" name="peso" id="peso" value="{{ old('peso') }}"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                        placeholder="Peso">
                </div>
                <div class="w-1/5">
                    <label for="piso">Piso*</label>
                    <input type="text" name="piso" id="piso" value="{{ old('piso') }}"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                        placeholder="Piso">
                </div>
                <div class="w-1/5">
                    <label for="cama">Cama*</label>
                    <input type="text" name="cama" id="cama" value="{{ old('cama') }}"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                        placeholder="Cama">
                </div>
            </div>

            <div class="flex justify-between mb-4 gap-4">
                <div class="w-1/4">
                    <label for="diagnostico">Diagnóstico</label>
                    <input type="text" name="diagnostico" id="diagnostico" value="{{ old('diagnostico') }}"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                        placeholder="Diagnóstico">
                </div>
                <div class="w-1/4">
                    <label for="medico_nombre">Nombre del Médico</label>
                    <input type="text" name="medico_nombre" id="medico_nombre" value="{{ old('medico_nombre') }}"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                        placeholder="Nombre del Médico">
                </div>
                <div class="w-1/4">
                    <label for="medico_cedula">Cédula del Médico</label>
                    <input type="text" name="medico_cedula" id="medico_cedula"
                        value="{{ old('medico_cedula') }}"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                        placeholder="Cédula del Médico">
                </div>
                <div class="w-1/4">
                    <label for="fecha_entrega">Fecha de entrega*</label>
                    <input type="datetime-local" name="fecha_entrega" id="fecha_entrega"
                        value="{{ old('fecha_entrega') }}"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                </div>
            </div>

            <div class="mb-4">
                <label for="observaciones">Observaciones</label>
                <input type="text" name="observaciones" id="observaciones" value="{{ old('observaciones') }}"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                    placeholder="Observaciones">
            </div>

            <!-- Mezclas -->
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
            e.preventDefault(); // prevenir envío inmediato

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

            // SweetAlert de confirmación
            Swal.fire({
                title: '¿Estás seguro?',
                text: "Esta solicitud será registrada.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#2563eb',
                cancelButtonColor: '#e11d48',
                confirmButtonText: 'Sí, guardar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    e.target.submit(); // enviar si confirma
                }
            });
        });

        document.addEventListener("DOMContentLoaded", () => agregarMezcla());
    </script>

    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: '¡Éxito!',
                text: '{{ session('success') }}',
                confirmButtonColor: '#3085d6'
            });
        </script>
    @endif

    @if ($errors->any())
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                html: `{!! implode('<br>', $errors->all()) !!}`,
                confirmButtonColor: '#e3342f'
            });
        </script>
    @endif
</x-admin-layout>
