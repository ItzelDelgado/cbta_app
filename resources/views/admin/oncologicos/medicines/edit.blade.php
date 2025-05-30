<x-admin-layout>
    <div class="max-w-5xl mx-auto p-6 bg-white rounded-xl shadow-md">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">Editar Lista de Medicamentos</h1>

        <!-- Mostrar errores -->
        @if ($errors->any())
            <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                <strong class="font-bold">¡Oops! Algo salió mal:</strong>
                <ul class="mt-2 list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form id="formActualizar" action="{{ route('admin.oncologicos.medicines.update', $lista->id) }}" method="POST"
            class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Nombre -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nombre de la lista:</label>
                <input type="text" name="name" value="{{ old('name', $lista->name) }}" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-blue-200 focus:outline-none">
            </div>

            <!-- Descripción -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Descripción:</label>
                <textarea name="description" rows="3"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-blue-200 focus:outline-none">{{ old('description', $lista->description) }}</textarea>
            </div>

            <!-- Tabla de medicamentos -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Medicamentos:</label>
                <table id="medicamentosTable"
                    class="w-full table-auto border border-gray-200 rounded-lg overflow-hidden text-sm">
                    <thead class="bg-gray-100 text-left text-gray-600 uppercase">
                        <tr>
                            <th class="px-4 py-2">Medicamento</th>
                            <th class="px-4 py-2">Precio personalizado</th>
                            <th class="px-4 py-2 text-center">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach (old(
        'medicamentos',
        $lista->medicines->map(
                fn($m) => [
                    'id' => $m->catalog_id,
                    'precio' => $m->pivot->precio,
                ],
            )->toArray(),
    ) as $index => $med)
                            <tr>
                                <td class="px-4 py-2">
                                    <select name="medicamentos[{{ $index }}][id]" required
                                        class="w-full px-3 py-1 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-blue-200">
                                        <option value="">Selecciona</option>
                                        @foreach ($catalogo as $item)
                                            <option value="{{ $item->id }}"
                                                {{ $item->id == $med['id'] ? 'selected' : '' }}>
                                                {{ $item->denominacion }} ({{ $item->presentacion }})
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="px-4 py-2">
                                    <input type="number" name="medicamentos[{{ $index }}][precio]"
                                        step="0.01" required value="{{ data_get($med, 'precio', '') }}"
                                        class="w-full px-3 py-1 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-blue-200">
                                </td>
                                <td class="px-4 py-2 text-center">
                                    <button type="button" onclick="this.closest('tr').remove()"
                                        class="px-3 py-1 bg-red-500 text-white rounded-md hover:bg-red-600 transition duration-200">
                                        Eliminar
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <button type="button" id="addRowBtn"
                    class="mt-4 px-4 py-2 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition duration-200"
                    data-index="{{ count(old('medicamentos', $lista->medicines)) }}">
                    + Agregar Medicamento
                </button>
            </div>

            <!-- Botón de guardar -->
            <div class="flex justify-end">
                <button type="button" id="confirmUpdateBtn"
                    class="px-6 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition duration-200">
                    Actualizar Lista
                </button>
            </div>
        </form>
    </div>

    @push('js')
        <script>
            const catalogo = @json($catalogo);
            const addRowBtn = document.getElementById('addRowBtn');
            const tbody = document.querySelector('#medicamentosTable tbody');

            let index = parseInt(addRowBtn.getAttribute('data-index')) || 0;

            function getSelectedIds() {
                return [...document.querySelectorAll('select[name^="medicamentos"]')]
                    .map(select => select.value)
                    .filter(val => val !== "");
            }

            function getOpcionesHTML(selectedId = null) {
                const usados = getSelectedIds().filter(id => id !== selectedId);
                const disponibles = catalogo.filter(m => !usados.includes(String(m.id)));

                return disponibles.map(m => {
                    return `<option value="${m.id}" ${m.id == selectedId ? 'selected' : ''}>
                        ${m.denominacion} (${m.presentacion})
                    </option>`;
                }).join('');
            }

            function crearFila() {
                const row = document.createElement('tr');

                row.innerHTML = `
            <td class="px-4 py-2">
                <select name="medicamentos[${index}][id]" required
                    class="medicamento-select w-full px-3 py-1 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-blue-200">
                    <option value="">Selecciona</option>
                    ${getOpcionesHTML()}
                </select>
            </td>
            <td class="px-4 py-2">
                <input type="number" name="medicamentos[${index}][precio]" step="0.01" required
                    class="w-full px-3 py-1 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-blue-200">
            </td>
            <td class="px-4 py-2 text-center">
                <button type="button"
                    class="eliminar-fila px-3 py-1 bg-red-500 text-white rounded-md hover:bg-red-600 transition duration-200">
                    Eliminar
                </button>
            </td>
        `;

                tbody.appendChild(row);
                index++;
                addRowBtn.setAttribute('data-index', index);
                actualizarOpciones();
            }

            function actualizarOpciones() {
                document.querySelectorAll('.medicamento-select').forEach(select => {
                    const valorActual = select.value;
                    select.innerHTML = `<option value="">Selecciona</option>` + getOpcionesHTML(valorActual);
                    select.value = valorActual;
                });
            }

            addRowBtn.addEventListener('click', crearFila);

            tbody.addEventListener('click', function(e) {
                if (e.target.classList.contains('eliminar-fila')) {
                    e.target.closest('tr').remove();
                    actualizarOpciones();
                }
            });

            tbody.addEventListener('change', function(e) {
                if (e.target.classList.contains('medicamento-select')) {
                    actualizarOpciones();
                }
            });

            window.addEventListener('DOMContentLoaded', actualizarOpciones);

            document.getElementById('confirmUpdateBtn').addEventListener('click', function() {
                Swal.fire({
                    title: '¿Estás seguro?',
                    text: "Se actualizará esta lista de medicamentos.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#2563eb',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, actualizar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('formActualizar').submit();
                    }
                });
            });
        </script>
    @endpush
</x-admin-layout>
