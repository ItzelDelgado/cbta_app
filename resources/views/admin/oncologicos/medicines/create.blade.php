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

                @if ($errors->has('medicamentos'))
                    <p class="mt-2 text-sm font-semibold">{{ $errors->first('medicamentos') }}</p>
                @endif
            </div>
        @endif

        <form action="{{ route('admin.oncologicos.medicines.store') }}" method="POST" enctype="multipart/form-data"
            class="space-y-6">
            @csrf

            {{-- Nombre --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nombre de la lista:</label>
                <input type="text" name="name" value="{{ old('name') }}" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring focus:ring-blue-200">
            </div>

            {{-- Descripción --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Descripción:</label>
                <textarea name="description" rows="3"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring focus:ring-blue-200">{{ old('description') }}</textarea>
            </div>

            {{-- Switch: Activar marcas --}}
            <div class="flex items-center">
                <input type="hidden" name="active_brands" value="0">
                <label class="inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="active_brands" value="1" class="sr-only peer"
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

            {{-- Switch: tipo de cobro global (mg / frasco) --}}
            <div class="flex items-center">
                <input type="hidden" name="charge_by" id="charge_by" value="{{ old('charge_by', 'mg') }}">

                <label class="inline-flex items-center cursor-pointer">
                    <input type="checkbox" id="charge_by_switch" class="sr-only peer"
                        {{ old('charge_by', 'mg') === 'frasco' ? 'checked' : '' }}>
                    <div
                        class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:bg-blue-600 relative
                               after:content-[''] after:absolute after:top-[2px] after:left-[2px]
                               after:bg-white after:border-gray-300 after:border after:rounded-full
                               after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-full">
                    </div>
                    <span class="ml-3 text-sm font-medium text-gray-700">
                        Cobrar por <span id="charge_by_label">
                            {{ old('charge_by', 'mg') === 'frasco' ? 'frasco' : 'mg' }}
                        </span>
                    </span>
                </label>
            </div>

            {{-- ================= DISTRIBUTOR (opcional) ================= --}}
            <div class="border rounded-lg p-4 bg-gray-50">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-sm font-semibold text-gray-800">Distribuidor</h2>
                        <p class="text-xs text-gray-500">Opcional. Estos datos se usarán para la segunda hoja de
                            remisión.</p>
                    </div>

                    <button type="button" id="btn-toggle-distributor"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold px-4 py-2 rounded">
                        + Agregar distribuidor
                    </button>
                </div>

                <div id="distributor-form" class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4 hidden">

                    <div class="md:col-span-1">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nombre del distribuidor</label>
                        <input type="text" name="distributor_name" id="distributor_name"
                            value="{{ old('distributor_name') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring focus:ring-blue-200"
                            placeholder="Ej. Centro Sequoia">
                    </div>

                    <div class="md:col-span-1">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Dirección</label>
                        <input type="text" name="distributor_address" id="distributor_address"
                            value="{{ old('distributor_address') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring focus:ring-blue-200"
                            placeholder="Calle, número, colonia, ciudad...">
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Logo (opcional)</label>
                        <input type="file" name="distributor_logo" id="distributor_logo" accept="image/*"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-white">

                        <div id="distributor-logo-preview-wrap" class="mt-3 hidden">
                            <p class="text-xs text-gray-500 mb-2">Vista previa:</p>
                            <img id="distributor-logo-preview" class="h-20 w-auto rounded border bg-white"
                                alt="Logo preview">
                        </div>

                        <div class="mt-3 flex gap-2">
                            <button type="button" id="btn-clear-distributor"
                                class="bg-gray-200 hover:bg-gray-300 text-gray-800 text-xs font-semibold px-3 py-2 rounded">
                                Quitar distribuidor
                            </button>
                        </div>

                        <p class="mt-2 text-xs text-gray-500">
                            Nota: por seguridad del navegador, el input de archivo no puede “persistir” con old(); si
                            falla la validación tendrás que volver a elegir el logo.
                        </p>
                    </div>
                </div>
            </div>

            {{-- ================= TABLA MEDICAMENTOS ================= --}}
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
                        {{-- JS agregará aquí los grupos y filas --}}
                    </tbody>
                </table>

                <button type="button" id="btn-add-grupo"
                    class="mt-3 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold px-4 py-2 rounded">
                    + Agregar medicamento
                </button>

                {{-- ================= Plantillas ocultas ================= --}}

                {{-- Encabezado de grupo (genérico) --}}
                <table class="hidden">
                    <tbody>
                        <tr id="tpl-grupo-header">
                            <td class="px-4 py-2 text-center font-bold bg-gray-50 border-t" colspan="6">
                                <div class="flex items-center gap-3">
                                    <span class="text-xs uppercase text-gray-500 mr-2">Medicamento genérico:</span>
                                    <select class="select-generico border-gray-300 rounded text-sm"
                                        data-role="generico">
                                        <option value="">Seleccione un medicamento...</option>
                                        @foreach ($catalogos as $cat)
                                            <option value="{{ $cat->id }}">
                                                {{ $cat->denominacion }}
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
                        <tr id="tpl-presentacion-row" class="fila-presentacion border-t" data-group="">
                            <td class="px-4 py-2 text-center align-top" data-role="row-index">1</td>

                            <td class="px-4 py-2 align-top text-gray-800 text-sm" data-role="generico-label"></td>

                            <td class="px-4 py-2 align-top">
                                <select class="w-full border-gray-300 rounded text-sm select-presentacion"
                                    data-role="presentacion">
                                    <option value="">Seleccione presentación...</option>
                                </select>

                                <input type="hidden" data-role="input-catalog-id">
                            </td>

                            {{-- ✅ Cobro solo visual (NO editable) --}}
                            <td class="px-4 py-2 text-center align-top">
                                <select class="border-gray-300 rounded text-sm" data-role="charge-by" disabled>
                                    <option value="mg">mg</option>
                                    <option value="frasco">frasco</option>
                                </select>
                                {{-- ✅ este hidden es el que se envía --}}
                                <input type="hidden" data-role="charge-by-hidden">
                            </td>

                            <td class="px-4 py-2 text-center align-top">
                                <input type="number" step="0.0001" min="0"
                                    class="w-full border-gray-300 rounded text-sm text-right" placeholder="Precio"
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

                @php
                    $presentacionesPorCatalogo = $catalogos->mapWithKeys(
                        fn($c) => [
                            $c->id => $c->presentations
                                ->map(
                                    fn($p) => [
                                        'id' => $p->id,
                                        'text' => $p->presentacion,
                                    ],
                                )
                                ->values(),
                        ],
                    );
                @endphp

                <script>
                    window.PRESENTACIONES_POR_CATALOGO = @json($presentacionesPorCatalogo);
                    window.OLD_MEDICAMENTOS = @json(old('medicamentos', []));
                </script>
            </div>

            <div class="flex justify-end">
                <button type="submit"
                    class="px-6 py-2 bg-blue-600 text-white font-semibold rounded hover:bg-blue-700 transition">
                    Guardar Lista
                </button>
            </div>
        </form>
    </div>

    @push('js')
        <script>
            document.addEventListener('DOMContentLoaded', function() {

                const tbody = document.getElementById('tbody-medicamentos');
                const tplGrupoHeader = document.getElementById('tpl-grupo-header');
                const tplPresentacionRow = document.getElementById('tpl-presentacion-row');

                const chargeByHidden = document.getElementById('charge_by');
                const chargeSwitch = document.getElementById('charge_by_switch');
                const chargeLabel = document.getElementById('charge_by_label');

                let globalIndex = 0;
                let groupCounter = 0;

                const PRESENT = window.PRESENTACIONES_POR_CATALOGO || {};
                const OLD_MEDS = window.OLD_MEDICAMENTOS || {};

                function getGlobalChargeBy() {
                    return (chargeByHidden && chargeByHidden.value) ? chargeByHidden.value : 'mg';
                }

                // ======================
                // 1) SWITCH GLOBAL (manda a hidden y a todas las filas)
                // ======================
                function syncChargeByLabel() {
                    if (!chargeByHidden || !chargeSwitch || !chargeLabel) return;

                    const value = chargeSwitch.checked ? 'frasco' : 'mg';
                    chargeByHidden.value = value;
                    chargeLabel.textContent = value;

                    tbody.querySelectorAll('tr.fila-presentacion').forEach(row => {
                        const sel = row.querySelector('[data-role="charge-by"]');
                        const hid = row.querySelector('[data-role="charge-by-hidden"]');
                        if (sel) sel.value = value;
                        if (hid) hid.value = value;
                    });
                }

                if (chargeSwitch && chargeByHidden && chargeLabel) {
                    chargeSwitch.addEventListener('change', () => {
                        syncChargeByLabel();
                    });
                    syncChargeByLabel();
                }

                // ======================
                // 2) GENÉRICOS: ocultar opciones ya usadas
                // ======================
                function getSelectedCatalogIds(exceptSelect = null) {
                    const ids = [];
                    tbody.querySelectorAll('select[data-role="generico"]').forEach(sel => {
                        if (exceptSelect && sel === exceptSelect) return;
                        if (sel.value) ids.push(String(sel.value));
                    });
                    return ids;
                }

                function refreshGenericosOptions() {
                    const allSelected = getSelectedCatalogIds(null);

                    tbody.querySelectorAll('select[data-role="generico"]').forEach(sel => {
                        const current = sel.value ? String(sel.value) : '';
                        const usedByOthers = allSelected.filter(v => v !== current);

                        // Recorremos options y ocultamos las usadas
                        Array.from(sel.options).forEach(opt => {
                            const val = String(opt.value || '');
                            if (!val) {
                                opt.hidden = false; // opción "Seleccione..."
                                return;
                            }

                            // si este option está usado en otro grupo y no es el actual => ocultar
                            opt.hidden = usedByOthers.includes(val);
                        });
                    });
                }

                // ======================
                // 3) PRESENTACIONES: ocultar opciones ya usadas dentro del MISMO grupo
                // ======================
                function getUsedPresentationIds(groupId, exceptSelect = null) {
                    const ids = [];
                    tbody.querySelectorAll(`tr.fila-presentacion[data-group="${groupId}"] [data-role="presentacion"]`)
                        .forEach(sel => {
                            if (exceptSelect && sel === exceptSelect) return;
                            if (sel.value) ids.push(String(sel.value));
                        });
                    return ids;
                }

                function cargarPresentacionesEnSelect(selectEl, catalogId, groupId) {
                    const currentValue = selectEl.value ? String(selectEl.value) : '';
                    const used = getUsedPresentationIds(groupId, selectEl); // usadas por otras filas del grupo

                    // reconstruir options
                    selectEl.innerHTML = '<option value="">Seleccione presentación...</option>';

                    if (!catalogId) {
                        selectEl.disabled = true;
                        return;
                    }

                    const lista = PRESENT[catalogId] || [];
                    lista.forEach(p => {
                        const val = String(p.id);
                        // si ya está usada y no es la actual => NO LA AGREGAMOS (no aparece)
                        if (used.includes(val) && val !== currentValue) return;

                        const opt = document.createElement('option');
                        opt.value = p.id;
                        opt.textContent = p.text;
                        if (val === currentValue) opt.selected = true;
                        selectEl.appendChild(opt);
                    });

                    selectEl.disabled = false;
                }

                function refreshPresentacionesGroup(groupId, catalogId) {
                    tbody.querySelectorAll(`tr.fila-presentacion[data-group="${groupId}"] [data-role="presentacion"]`)
                        .forEach(sel => {
                            cargarPresentacionesEnSelect(sel, catalogId, groupId);
                        });
                }

                function renumerarFilas() {
                    let n = 1;
                    tbody.querySelectorAll('tr.fila-presentacion').forEach(row => {
                        const cellIndex = row.querySelector('[data-role="row-index"]');
                        if (cellIndex) cellIndex.textContent = n++;
                    });
                }

                // ======================
                // 4) Crear fila
                // ======================
                function addPresentacionRow(groupId, selectGenerico) {
                    const row = tplPresentacionRow.cloneNode(true);
                    row.id = '';
                    row.dataset.group = groupId;

                    const labelGenerico = row.querySelector('[data-role="generico-label"]');
                    const selectPresent = row.querySelector('[data-role="presentacion"]');
                    const inputCatalog = row.querySelector('[data-role="input-catalog-id"]');

                    const selectChargeBy = row.querySelector('[data-role="charge-by"]');
                    const inputChargeHidden = row.querySelector('[data-role="charge-by-hidden"]');

                    const inputPrecio = row.querySelector('[data-role="precio"]');
                    const btnRemoveRow = row.querySelector('[data-role="remove-row"]');

                    // insertar al final del grupo
                    let insertAfter = tbody.querySelector(`tr.fila-presentacion[data-group="${groupId}"]:last-of-type`);
                    if (!insertAfter) insertAfter = tbody.querySelector(`tr[data-group="${groupId}"]`);
                    if (insertAfter) insertAfter.insertAdjacentElement('afterend', row);
                    else tbody.appendChild(row);

                    const catalogId = selectGenerico.value;

                    labelGenerico.textContent = selectGenerico.options[selectGenerico.selectedIndex]?.text || '';
                    inputCatalog.name = `medicamentos[${globalIndex}][catalog_id]`;
                    inputCatalog.value = catalogId;

                    selectPresent.name = `medicamentos[${globalIndex}][presentation_id]`;

                    // select cobro solo visual
                    selectChargeBy.name = '';
                    const gCharge = getGlobalChargeBy();
                    selectChargeBy.value = gCharge;

                    inputChargeHidden.name = `medicamentos[${globalIndex}][charge_by]`;
                    inputChargeHidden.value = gCharge;

                    inputPrecio.name = `medicamentos[${globalIndex}][precio]`;
                    globalIndex++;

                    // cargar presentaciones ocultando usadas
                    cargarPresentacionesEnSelect(selectPresent, catalogId, groupId);

                    selectPresent.addEventListener('change', function() {
                        // al cambiar una presentación, refrescar todo el grupo para que no aparezcan repetidas
                        refreshPresentacionesGroup(groupId, catalogId);
                    });

                    btnRemoveRow.addEventListener('click', function() {
                        row.remove();
                        renumerarFilas();
                        // al borrar, refrescar presentaciones del grupo
                        refreshPresentacionesGroup(groupId, catalogId);
                    });

                    renumerarFilas();
                    return row;
                }

                // ======================
                // 5) Crear grupo
                // ======================
                function addGrupo() {
                    const groupId = 'g' + (groupCounter++);

                    const header = tplGrupoHeader.cloneNode(true);
                    header.id = '';
                    header.dataset.group = groupId;

                    tbody.appendChild(header);

                    const selectGenerico = header.querySelector('[data-role="generico"]');
                    const btnAddPres = header.querySelector('[data-role="add-presentacion"]');
                    const btnRemoveGrupo = header.querySelector('[data-role="remove-grupo"]');

                    // refrescar options de genéricos (ocultar usados)
                    refreshGenericosOptions();

                    selectGenerico.addEventListener('change', function() {
                        const catalogId = this.value;
                        const labelText = this.options[this.selectedIndex]?.text || '';

                        // cambiar label + catalog_id en filas del grupo, y resetear presentaciones
                        tbody.querySelectorAll(`tr.fila-presentacion[data-group="${groupId}"]`).forEach(r => {
                            const label = r.querySelector('[data-role="generico-label"]');
                            const inputCatalog = r.querySelector('[data-role="input-catalog-id"]');
                            const selPres = r.querySelector('[data-role="presentacion"]');

                            if (label) label.textContent = labelText;
                            if (inputCatalog) inputCatalog.value = catalogId;
                            if (selPres) selPres.value = '';
                        });

                        // refrescar genéricos en todos (ocultar usados)
                        refreshGenericosOptions();

                        // refrescar presentaciones del grupo (ocultar usadas)
                        refreshPresentacionesGroup(groupId, catalogId);
                    });

                    btnAddPres.addEventListener('click', function() {
                        if (!selectGenerico.value) {
                            alert('Selecciona primero el medicamento genérico.');
                            return;
                        }
                        addPresentacionRow(groupId, selectGenerico);
                        // cada vez que agregas fila, refresca presentaciones
                        refreshPresentacionesGroup(groupId, selectGenerico.value);
                    });

                    btnRemoveGrupo.addEventListener('click', function() {
                        // borrar filas del grupo + header
                        tbody.querySelectorAll(`[data-group="${groupId}"]`).forEach(el => el.remove());
                        header.remove();
                        renumerarFilas();
                        // al eliminar grupo, refresca genéricos en todos
                        refreshGenericosOptions();
                    });

                    return {
                        groupId,
                        header,
                        selectGenerico
                    };
                }

                document.getElementById('btn-add-grupo')?.addEventListener('click', function() {
                    addGrupo();
                    refreshGenericosOptions();
                });

                // ======================
                // 6) Reconstrucción desde old()
                // ======================
                function buildFromOld(oldItems) {
                    const rows = Object.values(oldItems || {});
                    if (!rows.length) {
                        addGrupo();
                        refreshGenericosOptions();
                        return;
                    }

                    tbody.innerHTML = '';
                    globalIndex = 0;
                    groupCounter = 0;

                    // agrupar por catalog_id (con orden)
                    const order = [];
                    const grouped = {};
                    rows.forEach(item => {
                        const catalogId = item?.catalog_id ? String(item.catalog_id) : '';
                        if (!catalogId) return;

                        if (!grouped[catalogId]) {
                            grouped[catalogId] = [];
                            order.push(catalogId);
                        }
                        grouped[catalogId].push(item);
                    });

                    if (!order.length) {
                        addGrupo();
                        refreshGenericosOptions();
                        return;
                    }

                    order.forEach(catalogId => {
                        const {
                            groupId,
                            selectGenerico
                        } = addGrupo();

                        // set catalog
                        selectGenerico.value = catalogId;

                        // refresca genéricos (ocultar usados) ya con ese valor
                        refreshGenericosOptions();

                        grouped[catalogId].forEach(item => {
                            const row = addPresentacionRow(groupId, selectGenerico);

                            const selPresent = row.querySelector('[data-role="presentacion"]');
                            const selCharge = row.querySelector('[data-role="charge-by"]');
                            const hidCharge = row.querySelector('[data-role="charge-by-hidden"]');
                            const inpPrecio = row.querySelector('[data-role="precio"]');

                            // refrescar presentaciones antes de set value
                            refreshPresentacionesGroup(groupId, catalogId);

                            if (item.presentation_id) selPresent.value = String(item.presentation_id);

                            const gCharge = getGlobalChargeBy();
                            if (selCharge) selCharge.value = gCharge;
                            if (hidCharge) hidCharge.value = gCharge;

                            if (item.precio !== undefined && item.precio !== null) inpPrecio.value =
                                item.precio;

                            // refrescar presentaciones para ocultar las ya elegidas
                            refreshPresentacionesGroup(groupId, catalogId);
                        });

                        // labels correctos
                        selectGenerico.dispatchEvent(new Event('change'));
                    });

                    renumerarFilas();
                    syncChargeByLabel();
                    refreshGenericosOptions();
                }

                buildFromOld(OLD_MEDS);

                if (!tbody.querySelector('tr')) {
                    addGrupo();
                    refreshGenericosOptions();
                }
            });
        </script>
    @endpush
</x-admin-layout>
