<x-admin-layout>

    <div class="flex flex-col ">
        <div class="mt-2 mb-4">
            <h1 class="text-2xl font-medium text-gray-800">Crear Nueva Solicitud</h1>
        </div>
        <form action="{{ route('admin.oncologicos.solicitudes.store') }}" method="POST" class="bg-white rounded-lg p-6 shadow-lg">
            @csrf
            <div class="flex justify-between mb-4 gap-4">
                <div class="w-1/4">
                    <label for="">Paciente Nombre(s)</label>
                    <input type="text" name="paciente_nombre" id="paciente_nombre"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                        placeholder="Nombre(s) del Paciente" >
                </div>
                <div class="w-1/4">
                    <label for="">Paciente Apellido(s)</label>
                    <input type="text" name="paciente_apellido" id="paciente_apellido"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                        placeholder="Apellido(s) del Paciente" >
                </div>
                <div class="w-1/4">
                    <label for="">Servicio*</label>
                    <input type="text" name="servicio" id="servicio"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                        placeholder="Servicio" >
                </div>
                <div class="w-1/4">
                    <label for="">Registro*</label>
                    <input type="text" name="registro" id="registro"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                        placeholder="Registro" >
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
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                        >
                </div>
                <div class="w-1/5">
                    <label for="">Peso*</label>
                    <input type="number" name="peso" id="peso"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                        placeholder="Peso" >
                </div>

                <div class="w-1/5">
                    <label for="">Piso*</label>
                    <input type="text" name="piso" id="piso"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                        placeholder="Piso" >
                </div>
                <div class="w-1/5">
                    <label for="">Cama*</label>
                    <input type="text" name="cama" id="cama"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                        placeholder="Cama" >
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
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                        >
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



                <!-- Contenedor de mezclas -->
                <div id="contenedorMezclas"></div>

                <!-- Botón para agregar bolo (mezcla) -->
                <div class="my-4">
                    <button type="button" onclick="agregarMezcla()"
                        class="bg-green-500 text-white px-4 py-2 rounded">
                        <i class="fas fa-plus"></i> Agregar Bolo
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
        let idInternoMezcla = 0; // ID único interno
        let contadorFilasGlobal = 0;

        function agregarMezcla() {
            idInternoMezcla++;

            const mezclaDiv = document.createElement('div');
            mezclaDiv.classList.add("border", "p-4", "mb-6", "relative");
            mezclaDiv.dataset.idInterno = idInternoMezcla;

            mezclaDiv.innerHTML = `
        <div class="flex justify-between items-center mb-2">
            <h3 class="text-lg font-semibold mezcla-titulo"></h3>
            <button type="button" onclick="eliminarMezcla(this)" class="bg-red-600 text-white px-3 py-1 rounded">
                <i class="fas fa-trash"></i> Eliminar Bolo
            </button>
        </div>

        <table class="table-auto w-full border text-center">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border px-4 py-2">MEDICAMENTO</th>
                    <th class="border px-4 py-2">DOSIS</th>
                    <th class="border px-4 py-2">DILUYENTE</th>
                    <th class="border px-4 py-2">VOLUMEN DE DILUCIÓN TOTAL ml*</th>
                    <th class="border px-4 py-2">TIEMPO DE INFUSIÓN (min)*</th>
                    <th class="border px-4 py-2">VÍA DE ADMINISTRACIÓN</th>
                    <th class="border px-4 py-2">ACCIÓN</th>
                </tr>
            </thead>
            <tbody id="medicamentos_mezcla_${idInternoMezcla}">
            </tbody>

        </table>
        <div class="my-2">
            <button type="button" onclick="agregarFilaMedicamento(${idInternoMezcla})" class="bg-green-500 text-white px-4 py-2 rounded">
                <i class="fas fa-plus"></i> Agregar Medicamento
            </button>
        </div>
    `;

            document.getElementById('contenedorMezclas').appendChild(mezclaDiv);

            // Agrega primer medicamento
            agregarFilaMedicamento(idInternoMezcla);

            // Recalcula numeración
            actualizarNumeracionMezclas();
        }

        function eliminarMezcla(boton) {
            const mezclaDiv = boton.closest("div.border");
            mezclaDiv.remove();
            actualizarNumeracionMezclas();
        }

        function actualizarNumeracionMezclas() {
            const mezclas = document.querySelectorAll('#contenedorMezclas > .border');
            mezclas.forEach((mezcla, index) => {
                const h3 = mezcla.querySelector('.mezcla-titulo');
                h3.textContent = `Mezcla #${index + 1}`;
            });
        }

        function agregarFilaMedicamento(idMezcla) {
            contadorFilasGlobal++;
            const tbody = document.getElementById(`medicamentos_mezcla_${idMezcla}`);
            const fila = document.createElement('tr');
            fila.id = `fila_m${idMezcla}_f${contadorFilasGlobal}`;

            fila.innerHTML = `
        <td class="border px-4 py-2">
            <select name="medicamento_m${idMezcla}[]" class="w-full border rounded px-2 py-1">
                <option value="">Seleccione el medicamento</option>
                <option value="med1">Medicamento 1</option>
                <option value="med2">Medicamento 2</option>
            </select>
        </td>
        <td class="border px-4 py-2">
            <input type="number" name="dosis_m${idMezcla}[]" class="w-full border rounded px-2 py-1">
        </td>
        <td class="border px-4 py-2">
            <select name="diluyente_m${idMezcla}[]" class="w-full border rounded px-2 py-1">
                <option value="">Diluyentes</option>
                <option value="NaCl">NaCl 0.9%</option>
                <option value="Glucosa">Glucosa 5%</option>
            </select>
        </td>
        <td class="border px-4 py-2">
            <input type="number" name="volumen_dilucion_m${idMezcla}[]" class="w-full border rounded px-2 py-1">
        </td>
        <td class="border px-4 py-2">
            <input type="number" name="tiempo_infusion_m${idMezcla}[]" class="w-full border rounded px-2 py-1">
        </td>
        <td class="border px-4 py-2">
            <select name="via_administracion_m${idMezcla}[]" class="w-full border rounded px-2 py-1">
                <option value="">Vía de admin</option>
                <option value="IV">Intravenosa</option>
                <option value="IM">Intramuscular</option>
            </select>
        </td>
        <td class="border px-4 py-2">
            <button type="button" onclick="eliminarFila('${fila.id}')" class="bg-red-500 text-white px-2 py-1 rounded">
                <i class="fas fa-trash"></i>
            </button>
        </td>
    `;

            tbody.appendChild(fila);
        }

        function eliminarFila(idFila) {
            const fila = document.getElementById(idFila);
            if (fila) fila.remove();
        }

        document.addEventListener("DOMContentLoaded", function() {
            agregarMezcla();
        });
    </script>

</x-admin-layout>
