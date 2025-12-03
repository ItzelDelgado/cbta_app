<x-admin-layout>
    <div class="max-w-5xl mx-auto p-6 bg-white rounded-xl shadow-md">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">Crear Nueva Lista de Medicamentos</h1>

        @if ($errors->any())
            <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
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

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nombre de la lista:</label>
                <input type="text" name="name" value="{{ old('name') }}" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring focus:ring-blue-200">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Descripción:</label>
                <textarea name="description" rows="3"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring focus:ring-blue-200">{{ old('description') }}</textarea>
            </div>

            {{-- 🔹 Activar marcas (igual que en edit) --}}
            <div class="flex items-center gap-6">
                <div class="flex items-center">
                    <input type="hidden" name="active_brands" value="0">
                    <label class="inline-flex items-center cursor-pointer">
                        <input type="checkbox"
                               name="active_brands"
                               value="1"
                               class="sr-only peer"
                               {{ old('active_brands', false) ? 'checked' : '' }}>
                        <div
                            class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:bg-blue-600 relative
                            after:content-[''] after:absolute after:top-[2px] after:left-[2px]
                            after:bg-white after:border-gray-300 after:border after:rounded-full
                            after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-full">
                        </div>
                        <span class="ml-3 text-sm font-medium text-gray-700">Activar marcas</span>
                    </label>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Medicamentos:</label>

                {{-- charge_by global --}}
                <input type="hidden" name="charge_by" id="charge_by" value="{{ old('charge_by', 'mg') }}">

                <table id="tbl" class="w-full text-sm border border-gray-200 rounded">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="p-2 text-left">Medicamento (tipo)</th>
                            <th class="p-2">Cobro</th>
                            <th class="p-2">Precio (mg o frasco)</th>
                            <th class="p-2 text-center">Acción</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>

                <button type="button" id="addRow" class="mt-4 px-4 py-2 bg-green-600 text-white rounded">
                    + Agregar
                </button>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white font-semibold rounded">
                    Guardar Lista
                </button>
            </div>
        </form>
    </div>

    <template id="row-tpl">
        <tr class="border-t">
            <td class="p-2 w-1/2">
                <select name="medicamentos[__i__][id]" class="w-full border rounded p-2" required>
                    <option value="">Seleccione…</option>
                    @foreach ($catalogo as $item)
                        <option value="{{ $item->id }}">
                            {{ $item->denominacion }} ({{ $item->denominacion_comercial }})
                        </option>
                    @endforeach
                </select>
            </td>
            <!-- Cobro (solo visual, no se envía) -->
            <td class="p-2 w-28">
                <select class="charge w-full border rounded p-2">
                    <option value="mg">mg</option>
                    <option value="frasco">frasco</option>
                </select>
            </td>

            <!-- Precio que REALMENTE se envía al backend -->
            <td class="p-2">
                <input class="price w-full border rounded p-2" type="number" step="0.01" min="0"
                    name="medicamentos[__i__][precio]" required
                    placeholder="Si mg → precio mg; si frasco → precio frasco">
            </td>

            <td class="p-2 text-center">
                <button type="button" class="rm px-3 py-1 bg-red-500 text-white rounded">Eliminar</button>
            </td>
        </tr>
    </template>

    <script>
        const tbl = document.querySelector('#tbl tbody');
        const tpl = document.querySelector('#row-tpl').innerHTML;
        const add = document.querySelector('#addRow');
        const chargeByInput = document.getElementById('charge_by');
        let rowCounter = 0;

        // IDs de medicamentos ya usados
        function getUsedIds() {
            return Array.from(tbl.querySelectorAll('select[name^="medicamentos"]'))
                .map(sel => sel.value)
                .filter(v => v !== '');
        }

        // Deshabilita en cada <select> las opciones que ya estén usadas en otra fila
        function updateOptions() {
            const used = getUsedIds();

            tbl.querySelectorAll('select[name^="medicamentos"]').forEach(select => {
                const current = select.value;

                select.querySelectorAll('option').forEach(opt => {
                    if (!opt.value) return; // opción "Seleccione…"
                    // Deshabilita si ya está usada en otra fila distinta
                    opt.disabled = used.includes(opt.value) && opt.value !== current;
                });
            });
        }

        // Sincroniza TODOS los selects .charge y el hidden charge_by
        function syncChargeBy(value) {
            tbl.querySelectorAll('select.charge').forEach(sel => {
                sel.value = value;
            });
            if (chargeByInput) {
                chargeByInput.value = value;
            }
        }

        // Agregar una fila nueva
        function addRow() {
            tbl.insertAdjacentHTML('beforeend', tpl.replaceAll('__i__', rowCounter++));

            // aplicar el charge_by actual a la fila recién agregada
            const lastRow = tbl.lastElementChild;
            const chargeSelect = lastRow.querySelector('select.charge');
            if (chargeSelect && chargeByInput) {
                chargeSelect.value = chargeByInput.value || 'mg';
            }

            updateOptions();
        }

        // Evento: botón "Agregar"
        add.addEventListener('click', addRow);

        // Evento: eliminar fila
        tbl.addEventListener('click', e => {
            if (e.target.classList.contains('rm')) {
                e.target.closest('tr').remove();
                updateOptions();
            }
        });

        // Evento: cambios en selects de medicamento o tipo de cobro
        tbl.addEventListener('change', e => {
            // Cambio de medicamento → recalcular opciones disponibles
            if (e.target.matches('select[name^="medicamentos"]')) {
                updateOptions();
            }

            // Cambio en select de cobro → aplicar a todos y actualizar hidden
            if (e.target.classList.contains('charge')) {
                syncChargeBy(e.target.value);
            }
        });

        // Fila inicial y estado inicial de cobro
        addRow();
        syncChargeBy(chargeByInput.value || 'mg');
    </script>
</x-admin-layout>
