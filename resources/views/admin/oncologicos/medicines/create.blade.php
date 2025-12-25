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
                            remisión.
                        </p>
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
                    </div>
                </div>
            </div>

            {{-- Tabla de medicamentos agrupados --}}
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

                {{-- Plantillas ocultas --}}

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
                        <tr id="tpl-presentacion-row" class="fila-presentacion border-t" data-group="">
                            <td class="px-4 py-2 text-center align-top" data-role="row-index">1</td>

                            {{-- Genérico solo como etiqueta --}}
                            <td class="px-4 py-2 align-top text-gray-800 text-sm" data-role="generico-label"></td>

                            <td class="px-4 py-2 align-top">
                                <select class="w-full border-gray-300 rounded text-sm select-presentacion"
                                    data-role="presentacion">
                                    <option value="">Seleccione presentación...</option>
                                </select>

                                {{-- catalog_id e index reales para el backend --}}
                                <input type="hidden" data-role="input-catalog-id">
                                <input type="hidden" data-role="input-index">
                            </td>

                            <td class="px-4 py-2 text-center align-top">
                                <select class="border-gray-300 rounded text-sm" data-role="charge-by">
                                    <option value="mg">mg</option>
                                    <option value="frasco">frasco</option>
                                </select>
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

                // ===== Toggle Distributor Form =====
                const btnToggleDistributor = document.getElementById('btn-toggle-distributor');
                const distributorForm = document.getElementById('distributor-form');
                const btnClearDistributor = document.getElementById('btn-clear-distributor');

                const inpDistName = document.getElementById('distributor_name');
                const inpDistAddress = document.getElementById('distributor_address');
                const inpDistLogo = document.getElementById('distributor_logo');

                const previewWrap = document.getElementById('distributor-logo-preview-wrap');
                const previewImg = document.getElementById('distributor-logo-preview');

                function setDistributorVisible(visible) {
                    if (!distributorForm || !btnToggleDistributor) return;

                    distributorForm.classList.toggle('hidden', !visible);
                    btnToggleDistributor.textContent = visible ? 'Ocultar distribuidor' : '+ Agregar distribuidor';
                }

                if (btnToggleDistributor && distributorForm) {
                    btnToggleDistributor.addEventListener('click', () => {
                        const isHidden = distributorForm.classList.contains('hidden');
                        setDistributorVisible(isHidden);
                    });
                }

                // Si hay old() (por error de validación), abrir automáticamente
                const hasOldDistributor =
                    (inpDistName && inpDistName.value.trim() !== '') ||
                    (inpDistAddress && inpDistAddress.value.trim() !== '');

                if (hasOldDistributor) {
                    setDistributorVisible(true);
                }

                if (inpDistLogo && previewWrap && previewImg) {
                    inpDistLogo.addEventListener('change', () => {
                        const file = inpDistLogo.files && inpDistLogo.files[0];
                        if (!file) {
                            previewWrap.classList.add('hidden');
                            previewImg.src = '';
                            return;
                        }
                        const url = URL.createObjectURL(file);
                        previewImg.src = url;
                        previewWrap.classList.remove('hidden');
                    });
                }

                if (btnClearDistributor) {
                    btnClearDistributor.addEventListener('click', () => {
                        if (inpDistName) inpDistName.value = '';
                        if (inpDistAddress) inpDistAddress.value = '';
                        if (inpDistLogo) inpDistLogo.value = '';

                        if (previewWrap && previewImg) {
                            previewWrap.classList.add('hidden');
                            previewImg.src = '';
                        }

                        setDistributorVisible(false);
                    });
                }


                const tbody = document.getElementById('tbody-medicamentos');
                const tplGrupoHeader = document.getElementById('tpl-grupo-header');
                const tplPresentacionRow = document.getElementById('tpl-presentacion-row');

                const chargeByHidden = document.getElementById('charge_by');
                const chargeSwitch = document.getElementById('charge_by_switch');
                const chargeLabel = document.getElementById('charge_by_label');

                let globalIndex = 0; // índice plano para name="medicamentos[globalIndex][...]"
                let groupCounter = 0; // id interno de grupo

                const PRESENT = window.PRESENTACIONES_POR_CATALOGO || {};

                // === Cobro global mg/frasco ===
                function syncChargeByLabel() {
                    chargeByHidden.value = chargeSwitch.checked ? 'frasco' : 'mg';
                    chargeLabel.textContent = chargeSwitch.checked ? 'frasco' : 'mg';

                    // actualizar selects de cobro de todas las filas
                    tbody.querySelectorAll('[data-role="charge-by"]').forEach(sel => {
                        sel.value = chargeByHidden.value;
                    });
                }
                if (chargeSwitch && chargeByHidden && chargeLabel) {
                    chargeSwitch.addEventListener('change', syncChargeByLabel);
                    syncChargeByLabel();
                }

                // === Helpers para presentaciones (evitar duplicados POR GRUPO) ===

                // IDs de presentaciones ya elegidas dentro de un grupo
                function getUsedPresentationIds(groupId) {
                    const ids = [];
                    tbody.querySelectorAll(
                        'tr.fila-presentacion[data-group="' + groupId + '"] [data-role="presentacion"]'
                    ).forEach(sel => {
                        if (sel.value) {
                            ids.push(String(sel.value));
                        }
                    });
                    return ids;
                }

                // Rellena un select de presentaciones respetando los ya usados en el grupo
                function cargarPresentacionesEnSelect(selectEl, catalogId, groupId) {
                    const lista = PRESENT[catalogId] || [];
                    const currentValue = selectEl.value ? String(selectEl.value) : null;
                    const used = getUsedPresentationIds(groupId)
                        .filter(id => id !== currentValue); // quitamos el propio valor actual

                    selectEl.innerHTML = '<option value="">Seleccione presentación...</option>';

                    lista.forEach(p => {
                        const idStr = String(p.id);
                        const isCurrent = idStr === currentValue;
                        const isDisabled = !isCurrent && used.includes(idStr);

                        const opt = document.createElement('option');
                        opt.value = p.id;
                        opt.textContent = p.text;
                        if (isDisabled) opt.disabled = true;
                        if (isCurrent) opt.selected = true;

                        selectEl.appendChild(opt);
                    });

                    selectEl.disabled = !catalogId;
                }

                function renumerarFilas() {
                    let n = 1;
                    tbody.querySelectorAll('tr.fila-presentacion').forEach(row => {
                        const cellIndex = row.querySelector('[data-role="row-index"]');
                        if (cellIndex) {
                            cellIndex.textContent = n++;
                        }
                    });
                }

                function addPresentacionRow(groupId, selectGenerico) {
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

                    // insertar al final del grupo
                    let insertAfter = tbody.querySelector('tr.fila-presentacion[data-group="' + groupId +
                        '"]:last-of-type');
                    if (!insertAfter) {
                        insertAfter = tbody.querySelector('tr[data-group="' + groupId + '"]'); // header
                    }
                    if (insertAfter) {
                        insertAfter.insertAdjacentElement('afterend', row);
                    } else {
                        tbody.appendChild(row);
                    }

                    const catalogId = selectGenerico.value;
                    labelGenerico.textContent = selectGenerico.options[selectGenerico.selectedIndex].text;
                    inputCatalog.name = `medicamentos[${globalIndex}][catalog_id]`;
                    inputCatalog.value = catalogId;

                    inputIndex.value = globalIndex;
                    selectPresent.name = `medicamentos[${globalIndex}][presentation_id]`;
                    selectChargeBy.name = `medicamentos[${globalIndex}][charge_by]`;
                    inputPrecio.name = `medicamentos[${globalIndex}][precio]`;
                    globalIndex++;

                    // tipo de cobro global
                    if (chargeByHidden) {
                        selectChargeBy.value = chargeByHidden.value || 'mg';
                    }

                    // cargar opciones de presentación (con bloqueo de usados)
                    cargarPresentacionesEnSelect(selectPresent, catalogId, groupId);

                    // cada vez que cambie una presentación, recargamos TODAS las de ese grupo
                    selectPresent.addEventListener('change', function() {
                        const filasGrupo = tbody.querySelectorAll('tr.fila-presentacion[data-group="' +
                            groupId + '"]');
                        filasGrupo.forEach(r => {
                            const sel = r.querySelector('[data-role="presentacion"]');
                            cargarPresentacionesEnSelect(sel, catalogId, groupId);
                        });
                    });

                    btnRemoveRow.addEventListener('click', function() {
                        row.remove();
                        renumerarFilas();

                        // al borrar, refrescamos las opciones para liberar esa presentación
                        const filasGrupo = tbody.querySelectorAll('tr.fila-presentacion[data-group="' +
                            groupId + '"]');
                        filasGrupo.forEach(r => {
                            const sel = r.querySelector('[data-role="presentacion"]');
                            cargarPresentacionesEnSelect(sel, catalogId, groupId);
                        });
                    });

                    renumerarFilas();
                }

                function addGrupo() {
                    const groupId = 'g' + (groupCounter++);

                    const header = tplGrupoHeader.cloneNode(true);
                    header.id = '';
                    header.dataset.group = groupId;

                    tbody.appendChild(header);

                    const selectGenerico = header.querySelector('[data-role="generico"]');
                    const btnAddPres = header.querySelector('[data-role="add-presentacion"]');
                    const btnRemoveGrupo = header.querySelector('[data-role="remove-grupo"]');

                    btnAddPres.addEventListener('click', function() {
                        if (!selectGenerico.value) {
                            alert('Selecciona primero el medicamento genérico.');
                            return;
                        }
                        addPresentacionRow(groupId, selectGenerico);
                    });

                    btnRemoveGrupo.addEventListener('click', function() {
                        const filas = tbody.querySelectorAll('[data-group="' + groupId + '"]');
                        filas.forEach(f => f.remove());
                        header.remove();
                        renumerarFilas();
                    });

                    // Si cambia el genérico, reiniciamos las presentaciones del grupo
                    selectGenerico.addEventListener('change', function() {
                        const catalogId = this.value;
                        const labelText = this.options[this.selectedIndex]?.text || '';

                        const groupRows = tbody.querySelectorAll('tr.fila-presentacion[data-group="' + groupId +
                            '"]');
                        groupRows.forEach(row => {
                            const selectPresent = row.querySelector('[data-role="presentacion"]');
                            const label = row.querySelector('[data-role="generico-label"]');
                            const inputCatalog = row.querySelector('[data-role="input-catalog-id"]');

                            selectPresent.value = '';
                            label.textContent = labelText;
                            inputCatalog.value = catalogId;

                            cargarPresentacionesEnSelect(selectPresent, catalogId, groupId);
                        });
                    });
                }

                document.getElementById('btn-add-grupo').addEventListener('click', function() {
                    addGrupo();
                });

                // Grupo inicial vacío
                addGrupo();
            });
        </script>
    @endpush

</x-admin-layout>
