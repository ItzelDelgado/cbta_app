<x-admin-layout>
    <div class="max-w-5xl mx-auto p-6 bg-white rounded-xl shadow-md">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">Crear Nueva Lista de Medicamentos</h1>

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

        <form action="{{ route('admin.oncologicos.medicines.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Nombre -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nombre de la lista:</label>
                <input type="text" name="name" value="{{ old('name') }}" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-blue-200 focus:outline-none">
            </div>

            <!-- Descripción -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Descripción:</label>
                <textarea name="description" rows="3"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-blue-200 focus:outline-none">{{ old('description') }}</textarea>
            </div>
            <!-- Activa Marcas -->
            <div class="flex items-center gap-8">
                {{-- Switch: Activar marcas --}}
                <div class="flex items-center">
                    <input type="hidden" name="active_brands" value="0"> {{-- valor por defecto --}}
                    <label class="inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="active_brands" value="1" class="sr-only peer"
                            {{ old('active_brands') ? 'checked' : '' }}>
                        <div
                            class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:bg-blue-600 relative
                       after:content-[''] after:absolute after:top-[2px] after:left-[2px]
                       after:bg-white after:border-gray-300 after:border after:rounded-full
                       after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-full">
                        </div>
                        <span class="ml-3 text-sm font-medium text-gray-700">Activar marcas</span>
                    </label>
                </div>

                {{-- Switch: Cobro por mg / frasco --}}
                <div class="flex items-center">
                    {{-- default a "mg" si no hay old() --}}
                    <input type="hidden" name="charge_by" value="{{ old('charge_by', 'mg') }}">

                    <label class="inline-flex items-center cursor-pointer">
                        {{-- Toggle visual (no cambia por sí solo el hidden); usamos data-attr para saber estado inicial --}}
                        <button type="button" id="toggle-charge-by" class="w-16 h-6 bg-gray-200 rounded-full relative"
                            data-state="{{ old('charge_by', 'mg') }}">
                            <span id="knob-charge-by"
                                class="absolute top-[2px] left-[2px] h-5 w-7 bg-white border border-gray-300 rounded-full transition-all flex items-center justify-center text-[10px] font-semibold">
                                {{ strtoupper(old('charge_by', 'mg')) }}
                            </span>
                        </button>
                        <span class="ml-3 text-sm font-medium text-gray-700">Cobrar por mg / frasco</span>
                    </label>
                </div>
            </div>
            <!-- Tabla -->
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
                        {{-- Reinsertar valores viejos --}}
                        @if (old('medicamentos'))
                            @foreach (old('medicamentos') as $med)
                                <tr>
                                    <td class="px-4 py-2">
                                        <select name="medicamentos[][id]" required
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
                                        <input type="number" name="medicamentos[][precio]" step="0.01" required
                                            value="{{ $med['precio'] }}"
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
                        @endif
                    </tbody>
                </table>

                <button type="button" id="addRowBtn" data-index="0"
                    class="mt-4 px-4 py-2 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition duration-200">
                    + Agregar Medicamento
                </button>
            </div>


            <!-- Botón de Guardar -->
            <div class="flex justify-end">
                <button type="submit"
                    class="px-6 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition duration-200">
                    Guardar Lista
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

            (function() {
                const btn = document.getElementById('toggle-charge-by');
                const knob = document.getElementById('knob-charge-by');

                if (!btn || !knob) return;

                const hidden = document.querySelector('input[type=hidden][name=charge_by]');
                const applyUI = (state) => {
                    if (state === 'mg') {
                        knob.style.transform = 'translateX(0)';
                        knob.textContent = 'MG';
                        btn.classList.remove('bg-blue-600');
                        btn.classList.add('bg-gray-200');
                    } else {
                        knob.style.transform = 'translateX(36px)'; // ~ w-16 (64) - knob (28) - margins
                        knob.textContent = 'FR';
                        btn.classList.remove('bg-gray-200');
                        btn.classList.add('bg-blue-600');
                    }
                };

                let state = btn.dataset.state === 'frasco' ? 'frasco' : 'mg';
                applyUI(state);

                btn.addEventListener('click', function() {
                    state = (state === 'mg') ? 'frasco' : 'mg';
                    if (hidden) hidden.value = state;
                    applyUI(state);
                });
            })();


            window.addEventListener('DOMContentLoaded', actualizarOpciones);
        </script>
    @endpush
</x-admin-layout>
