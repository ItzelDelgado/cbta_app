<x-admin-layout>
    <div class="max-w-5xl mx-auto p-6 bg-white rounded-xl shadow-md">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">
            Editar Lista de Medicamentos: {{ $lista->name }}
        </h1>

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

        <form action="{{ route('admin.oncologicos.medicines.update', $lista->id) }}"
              method="POST"
              class="space-y-6">
            @csrf
            @method('PUT')

            {{-- Datos generales --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nombre de la lista:</label>
                <input type="text"
                       name="name"
                       value="{{ old('name', $lista->name) }}"
                       required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring focus:ring-blue-200">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Descripción:</label>
                <textarea name="description"
                          rows="3"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring focus:ring-blue-200">{{ old('description', $lista->description) }}</textarea>
            </div>

            {{-- Switch: Activar marcas --}}
            <div class="flex items-center">
                <input type="hidden" name="active_brands" value="0">
                <label class="inline-flex items-center cursor-pointer">
                    <input type="checkbox"
                           name="active_brands"
                           value="1"
                           class="sr-only peer"
                           {{ old('active_brands', $lista->active_brands) ? 'checked' : '' }}>
                    <div
                        class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:bg-blue-600 relative
                               after:content-[''] after:absolute after:top-[2px] after:left-[2px]
                               after:bg-white after:border-gray-300 after:border after:rounded-full
                               after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-full">
                    </div>
                    <span class="ml-3 text-sm font-medium text-gray-700">Activar marcas</span>
                </label>
            </div>

            {{-- Switch: tipo de cobro global (mg / frasco) --}}
            <div class="flex items-center">
                <input type="hidden"
                       name="charge_by"
                       id="charge_by"
                       value="{{ old('charge_by', $lista->charge_by ?? 'mg') }}">

                <label class="inline-flex items-center cursor-pointer">
                    <input type="checkbox"
                           id="charge_by_switch"
                           class="sr-only peer"
                           {{ old('charge_by', $lista->charge_by ?? 'mg') === 'frasco' ? 'checked' : '' }}>
                    <div
                        class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:bg-blue-600 relative
                               after:content-[''] after:absolute after:top-[2px] after:left-[2px]
                               after:bg-white after:border-gray-300 after:border after:rounded-full
                               after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-full">
                    </div>
                    <span class="ml-3 text-sm font-medium text-gray-700">
                        Cobrar por
                        <span id="charge_by_label">
                            {{ old('charge_by', $lista->charge_by ?? 'mg') === 'frasco' ? 'frasco' : 'mg' }}
                        </span>
                    </span>
                </label>
            </div>

            {{-- ================= TABLA DE MEDICAMENTOS ================= --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Medicamentos:</label>

                <table class="w-full text-sm text-left text-gray-600 border">
                    <thead class="text-xs uppercase bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 w-1/12 text-center">#</th>
                        <th class="px-4 py-2 w-3/12">Medicamento (genérico)</th>
                        <th class="px-4 py-2 w-3/12">Presentación</th>
                        <th class="px-4 py-2 w-1/12 text-center">Cobro</th>
                        <th class="px-4 py-2 w-2/12 text-center">Precio (mg o frasco)</th>
                        <th class="px-4 py-2 w-2/12 text-center">Acción</th>
                    </tr>
                    </thead>
                    <tbody id="tbody-medicamentos">
                    {{-- se llenará por JS --}}
                    </tbody>
                </table>

                <button type="button"
                        id="btn-add-grupo"
                        class="mt-3 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold px-4 py-2 rounded">
                    + Agregar medicamento
                </button>

                {{-- ========= PLANTILLAS OCULTAS ========= --}}

                {{-- Encabezado de grupo (genérico) --}}
                <table class="hidden">
                    <tbody>
                    <tr id="tpl-grupo-header">
                        <td class="px-4 py-2 text-center font-bold bg-gray-50 border-t" colspan="6">
                            <div class="flex items-center gap-3">
                                <span class="text-xs uppercase text-gray-500 mr-2">
                                    Medicamento genérico:
                                </span>

                                <select class="select-generico border-gray-300 rounded text-sm"
                                        data-role="generico">
                                    <option value="">Seleccione un medicamento...</option>
                                    @foreach ($catalogos as $cat)
                                        <option value="{{ $cat->id }}">
                                            {{ $cat->denominacion }}
                                            @if ($cat->denominacion_comercial)
                                                ({{ $cat->denominacion_comercial }})
                                            @endif
                                        </option>
                                    @endforeach
                                </select>

                                <button type="button"
                                        class="btn-add-presentacion ml-auto bg-blue-600 hover:bg-blue-700 text-white text-xs px-3 py-1 rounded"
                                        data-role="add-presentacion">
                                    + Agregar presentación
                                </button>

                                <button type="button"
                                        class="btn-remove-grupo bg-red-600 hover:bg-red-700 text-white text-xs px-3 py-1 rounded"
                                        data-role="remove-grupo">
                                    Quitar medicamento
                                </button>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>

                {{-- Fila de presentación --}}
                <table class="hidden">
                    <tbody>
                    <tr id="tpl-presentacion-row"
                        class="fila-presentacion border-t"
                        data-group="">
                        <td class="px-4 py-2 text-center align-top" data-role="row-index">1</td>

                        {{-- nombre del genérico (solo texto) --}}
                        <td class="px-4 py-2 align-top text-gray-800 text-sm"
                            data-role="generico-label">
                            <!-- se llena por JS -->
                        </td>

                        <td class="px-4 py-2 align-top">
                            <select class="w-full border-gray-300 rounded text-sm select-presentacion"
                                    data-role="presentacion">
                                <option value="">Seleccione presentación...</option>
                            </select>

                            {{-- hidden para backend --}}
                            <input type="hidden" data-role="input-catalog-id">
                            <input type="hidden" data-role="input-index">
                        </td>

                        <td class="px-4 py-2 text-center align-top">
                            <select class="border-gray-300 rounded text-sm"
                                    data-role="charge-by">
                                <option value="mg">mg</option>
                                <option value="frasco">frasco</option>
                            </select>
                        </td>

                        <td class="px-4 py-2 text-center align-top">
                            <input type="number"
                                   step="0.0001"
                                   min="0"
                                   class="w-full border-gray-300 rounded text-sm text-right"
                                   placeholder="Precio"
                                   data-role="precio">
                        </td>

                        <td class="px-4 py-2 text-center align-top">
                            <button type="button"
                                    class="btn-remove-row bg-red-500 hover:bg-red-600 text-white text-xs px-3 py-1 rounded"
                                    data-role="remove-row">
                                Eliminar
                            </button>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>

            <div class="flex justify-end">
                <button type="submit"
                        class="px-6 py-2 bg-blue-600 text-white font-semibold rounded hover:bg-blue-700 transition">
                    Guardar cambios
                </button>
            </div>
        </form>
    </div>

    @php
        // Para el JS: presentaciones por catálogo => [{id, text}, ...]
        $presentacionesPorCatalogo = $catalogos->mapWithKeys(function ($c) {
            return [
                $c->id => $c->presentations->map(function ($p) {
                    return [
                        'id'   => $p->id,
                        'text' => $p->presentacion,
                    ];
                }),
            ];
        });
    @endphp

    @push('js')
        <script>
            // Datos globales para JS
            window.PRESENTACIONES_POR_CATALOGO = @json($presentacionesPorCatalogo);
            window.LISTA_ITEMS = @json($listaItems);

            document.addEventListener('DOMContentLoaded', function () {
                const tbody = document.getElementById('tbody-medicamentos');
                const tplGrupoHeader = document.getElementById('tpl-grupo-header');
                const tplPresentacionRow = document.getElementById('tpl-presentacion-row');

                let globalIndex = 0;
                let groupCounter = 0;

                const PRESENT = window.PRESENTACIONES_POR_CATALOGO || {};
                const PRESET_ITEMS = window.LISTA_ITEMS || [];

                // ================= Helpers =================

                function renumerarFilas() {
                    let n = 1;
                    tbody.querySelectorAll('tr.fila-presentacion').forEach(row => {
                        const cellIndex = row.querySelector('[data-role="row-index"]');
                        if (cellIndex) {
                            cellIndex.textContent = n++;
                        }
                    });
                }

                // regresa IDs de presentaciones ya usadas para un catálogo (excepto currentValue)
                function getUsedPresentationIdsPorCatalog(catalogId, currentValue = null) {
                    const ids = [];
                    tbody.querySelectorAll('tr.fila-presentacion').forEach(row => {
                        const inputCatalog = row.querySelector('[data-role="input-catalog-id"]');
                        const selPres = row.querySelector('[data-role="presentacion"]');
                        if (!inputCatalog || !selPres) return;

                        if (String(inputCatalog.value) === String(catalogId) && selPres.value) {
                            if (!currentValue || String(selPres.value) !== String(currentValue)) {
                                ids.push(String(selPres.value));
                            }
                        }
                    });
                    return ids;
                }

                function cargarPresentacionesEnSelect(selectEl, catalogId, currentValue = null) {
                    selectEl.innerHTML = '<option value="">Seleccione presentación...</option>';

                    if (!catalogId) {
                        selectEl.disabled = true;
                        return;
                    }

                    const lista = PRESENT[catalogId] || [];
                    const usados = getUsedPresentationIdsPorCatalog(catalogId, currentValue);

                    lista.forEach(p => {
                        const opt = document.createElement('option');
                        opt.value = p.id;
                        opt.textContent = p.text;

                        if (String(p.id) === String(currentValue)) {
                            opt.selected = true;
                        } else if (usados.includes(String(p.id))) {
                            opt.disabled = true;
                        }

                        selectEl.appendChild(opt);
                    });

                    selectEl.disabled = false;
                }

                // ================= Grupos y filas =================

                function addGrupo(initialCatalogId = null) {
                    const groupId = 'g' + (groupCounter++);

                    const header = tplGrupoHeader.cloneNode(true);
                    header.id = '';
                    header.dataset.group = groupId;
                    tbody.appendChild(header);

                    const selectGenerico = header.querySelector('[data-role="generico"]');
                    const btnAddPres = header.querySelector('[data-role="add-presentacion"]');
                    const btnRemoveGrupo = header.querySelector('[data-role="remove-grupo"]');

                    if (initialCatalogId) {
                        selectGenerico.value = String(initialCatalogId);
                    }

                    selectGenerico.addEventListener('change', function () {
                        const catalogId = this.value;

                        // actualizar todas las filas de este grupo
                        tbody.querySelectorAll('tr.fila-presentacion[data-group="' + groupId + '"]')
                            .forEach(row => {
                                const label = row.querySelector('[data-role="generico-label"]');
                                const inputCatalog = row.querySelector('[data-role="input-catalog-id"]');
                                const selPres = row.querySelector('[data-role="presentacion"]');

                                label.textContent = this.options[this.selectedIndex]?.text || '';
                                inputCatalog.value = catalogId;

                                cargarPresentacionesEnSelect(selPres, catalogId, selPres.value || null);
                            });
                    });

                    btnAddPres.addEventListener('click', function () {
                        if (!selectGenerico.value) {
                            alert('Selecciona primero el medicamento genérico.');
                            return;
                        }
                        addPresentacionRow(groupId, selectGenerico);
                    });

                    btnRemoveGrupo.addEventListener('click', function () {
                        tbody.querySelectorAll('tr.fila-presentacion[data-group="' + groupId + '"]')
                            .forEach(r => r.remove());
                        header.remove();
                        renumerarFilas();
                    });

                    return groupId;
                }

                function addPresentacionRow(groupId, selectGenerico, presetItem = null) {
                    const row = tplPresentacionRow.cloneNode(true);
                    row.id = '';
                    row.dataset.group = groupId;

                    const labelGenerico = row.querySelector('[data-role="generico-label"]');
                    const selectPresent = row.querySelector('[data-role="presentacion"]');
                    const inputCatalog = row.querySelector('[data-role="input-catalog-id"]');
                    const inputIndex = row.querySelector('[data-role="input-index"]');
                    const selectChargeBy = row.querySelector('[data-role="charge-by"]');
                    const inputPrecio = row.querySelector('[data-role="precio"]');
                    const btnRemoveRow = row.querySelector('[data-role="remove-row"]');

                    const catalogId = selectGenerico.value;
                    labelGenerico.textContent = selectGenerico.options[selectGenerico.selectedIndex]?.text || '';
                    inputCatalog.value = catalogId;

                    const idx = globalIndex++;
                    inputIndex.value = idx;

                    inputCatalog.name = `medicamentos[${idx}][catalog_id]`;
                    selectPresent.name = `medicamentos[${idx}][presentation_id]`;
                    selectChargeBy.name = `medicamentos[${idx}][charge_by]`;
                    inputPrecio.name = `medicamentos[${idx}][precio]`;

                    let presetPresentationId = null;
                    if (presetItem) {
                        presetPresentationId = presetItem.presentation_id;
                        selectChargeBy.value = presetItem.charge_by || 'mg';
                        inputPrecio.value = presetItem.precio;
                    }

                    cargarPresentacionesEnSelect(selectPresent, catalogId, presetPresentationId);

                    // insertar fila debajo del último renglón del grupo
                    let lastRowOfGroup = null;
                    tbody.querySelectorAll('tr.fila-presentacion').forEach(r => {
                        if (r.dataset.group === groupId) {
                            lastRowOfGroup = r;
                        }
                    });

                    if (lastRowOfGroup) {
                        lastRowOfGroup.insertAdjacentElement('afterend', row);
                    } else {
                        const header = tbody.querySelector('tr[data-group="' + groupId + '"]');
                        header.insertAdjacentElement('afterend', row);
                    }

                    btnRemoveRow.addEventListener('click', function () {
                        row.remove();
                        renumerarFilas();
                    });

                    // cuando cambie la presentación, re-aplicar regla de no duplicados
                    selectPresent.addEventListener('change', function () {
                        const currentVal = this.value || null;

                        // refrescar todas las filas de este grupo
                        tbody.querySelectorAll('tr.fila-presentacion[data-group="' + groupId + '"]')
                            .forEach(r => {
                                const sel = r.querySelector('[data-role="presentacion"]');
                                if (!sel) return;
                                const cur = sel === this ? currentVal : sel.value || null;
                                cargarPresentacionesEnSelect(sel, catalogId, cur);
                            });
                    });

                    renumerarFilas();
                }

                // ================= Switch de cobro global =================

                const chargeByHidden = document.getElementById('charge_by');
                const chargeSwitch = document.getElementById('charge_by_switch');
                const chargeLabel = document.getElementById('charge_by_label');

                function syncChargeVisual() {
                    if (!chargeByHidden || !chargeSwitch || !chargeLabel) return;

                    const value = chargeSwitch.checked ? 'frasco' : 'mg';
                    chargeByHidden.value = value;
                    chargeLabel.textContent = value;

                    tbody.querySelectorAll('[data-role="charge-by"]').forEach(sel => {
                        sel.value = value;
                    });
                }

                if (chargeSwitch && chargeByHidden && chargeLabel) {
                    chargeSwitch.addEventListener('change', syncChargeVisual);
                    syncChargeVisual();
                }

                // ================= Inicialización (edit vs create) =================

                const btnAddGrupo = document.getElementById('btn-add-grupo');

                btnAddGrupo.addEventListener('click', function () {
                    addGrupo();
                });

                if (PRESET_ITEMS.length === 0) {
                    // Lista sin registros → un grupo vacío
                    addGrupo();
                } else {
                    // Agrupar items existentes por catalog_id
                    const groupsByCatalog = {};
                    PRESET_ITEMS.forEach(item => {
                        const cid = String(item.catalog_id);
                        if (!groupsByCatalog[cid]) {
                            groupsByCatalog[cid] = [];
                        }
                        groupsByCatalog[cid].push(item);
                    });

                    Object.entries(groupsByCatalog).forEach(([catalogId, items]) => {
                        const groupId = addGrupo(catalogId);

                        const header = tbody.querySelector('tr[data-group="' + groupId + '"]');
                        const selectGenerico = header.querySelector('[data-role="generico"]');

                        selectGenerico.value = catalogId;

                        items.forEach(item => {
                            addPresentacionRow(groupId, selectGenerico, item);
                        });

                        // dispara el change para actualizar labels de filas si cambias el genérico luego
                        selectGenerico.dispatchEvent(new Event('change'));
                    });
                }
            });
        </script>
    @endpush
</x-admin-layout>