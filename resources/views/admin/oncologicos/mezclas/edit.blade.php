<x-admin-layout>
    <div class="mb-4">
        <h1 class="text-2xl font-bold">Editar Mezcla #{{ $mezcla->id }}</h1>
    </div>

    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <strong>Se encontraron errores:</strong>
            <ul class="mt-2 list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-800 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <form id="formularioMezcla" action="{{ route('admin.oncologicos.mezclas.update', $mezcla->id) }}" method="POST"
        class="bg-white rounded-lg p-6 shadow">
        @csrf
        @method('PUT')

        {{-- Datos de la solicitud / paciente --}}
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
                <input type="text" name="servicio" id="servicio" value="{{ old('servicio', $solicitud->servicio) }}"
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

        <div id="contenedorMezcla"></div>
        <input type="hidden" name="mezcla_json" id="mezcla_json">

        <div class="flex justify-end mt-4 gap-4">
            <input type="hidden" name="accion" id="accion" value="actualizar">

            <x-button type="submit" onclick="document.getElementById('accion').value='actualizar'">
                ACTUALIZAR MEZCLA
            </x-button>

            @if ($mezcla->estado === 'pendiente')
                <x-button type="submit" class="bg-green-600 hover:bg-green-700"
                    onclick="document.getElementById('accion').value='aprobar'">
                    APROBAR MEZCLA
                </x-button>
            @endif
        </div>
    </form>

    <script>
        const medicamentos = @json($medicamentos);
        const infoAdicional = @json($infoAdicional);
        const mezcla = @json($mezcla);
        const infusors = @json($infusors ?? []);
        const presentacionesPorCatalogo = @json($presentacionesPorCatalogo ?? []);
        // NUEVO: presentaciones de diluyente agrupadas por diluent_id
        const diluentPresentationsPorDiluyente = @json($diluentPresentationsPorDiluyente ?? []);

        let contadorFilas = 0;

        // ===== Helpers Infusor vs Set =====
        function requiereInfusorParaMed(medId) {
            if (!medId) return false;
            const med = medicamentos.find(m => String(m.id) === String(medId));
            if (med && typeof med.requires_infusor !== 'undefined') {
                return Number(med.requires_infusor) === 1;
            }
            const info = infoAdicional[medId];
            if (info && typeof info.requires_infusor !== 'undefined') {
                return Number(info.requires_infusor) === 1;
            }
            return false;
        }

        function mezclaAdmiteInfusor() {
            const filas = document.querySelectorAll('#medicamentos_mezcla tr');
            for (const fila of filas) {
                const medSel = fila.querySelector('[data-name="medicamento"]') || fila.querySelector('.medicamento-select');
                if (medSel && medSel.value && requiereInfusorParaMed(medSel.value)) {
                    return true;
                }
            }
            return false;
        }

        function updateInfusorDisponibilidad() {
            const setCb = document.querySelector('[data-name="set_infusion"]');
            const selInf = document.querySelector('[data-name="infusor_id"]');
            if (!setCb || !selInf) return;

            const admite = mezclaAdmiteInfusor();

            if (setCb.checked) {
                selInf.value = '';
                selInf.disabled = true;
                return;
            }

            selInf.disabled = !admite;
            if (!admite) selInf.value = '';
        }

        function toggleSetInfusion(checkbox) {
            const selInf = document.querySelector('[data-name="infusor_id"]');
            if (!selInf) return;

            if (checkbox.checked) {
                selInf.value = '';
                selInf.disabled = true;
            } else {
                selInf.disabled = !mezclaAdmiteInfusor();
            }
        }

        function toggleInfusorSelect(select) {
            const setCb = document.querySelector('[data-name="set_infusion"]');
            if (!setCb) return;

            if (select.value) {
                setCb.checked = false;
                setCb.disabled = true;
            } else {
                setCb.disabled = false;
            }
        }
        // ==================================

        // ===== Presentaciones por medicamento =====
        function onMedicamentoChange(selectElem) {
            const filaId = parseInt(selectElem.closest('tr').id.replace('fila_', ''), 10) || 0;
            const medId = selectElem.value;
            inicializarPresentacionesFila(filaId, medId);
            recalcularResumenPresentaciones(filaId);
            updateInfusorDisponibilidad();
            updateDiluentPresentationSelector();
            autoSelectDiluentPresentationForVolume();
        }

        function togglePresentaciones(filaId) {
            const wrap = document.getElementById(`presentaciones_wrap_${filaId}`);
            if (!wrap) return;
            wrap.classList.toggle('hidden');
        }

        function inicializarPresentacionesFila(filaId, medicamentoId, presentacionesGuardadas = []) {
            const info = infoAdicional[medicamentoId];
            if (!info) return;

            const catalogId = info.catalog_id;
            const lista = presentacionesPorCatalogo[catalogId] || [];
            const tbody = document.getElementById(`presentaciones_body_${filaId}`);
            if (!tbody) return;

            tbody.innerHTML = "";

            lista.forEach(p => {
                const tr = document.createElement('tr');
                tr.classList.add('presentacion-row');
                tr.dataset.presentationId = p.id;
                tr.dataset.cantidadMg = p.cantidad_medicamento || 0;
                tr.dataset.batchId = p.batch_id || '';

                const guardada = presentacionesGuardadas.find(g =>
                    Number(g.medicine_batch_id) === Number(p.batch_id)
                );
                const frascosValue = guardada ? Number(guardada.unidades_usadas) : 0;

                tr.innerHTML = `
                <td class="border px-1 py-1">${p.presentacion}</td>
                <td class="border px-1 py-1 text-right">${p.cantidad_medicamento ?? '—'}</td>
                <td class="border px-1 py-1 text-right">${p.volumen_diluyente ?? '—'}</td>
                <td class="border px-1 py-1">${p.lote ?? '—'}</td>
                <td class="border px-1 py-1">
                    ${p.caducidad ? (new Date(p.caducidad)).toLocaleDateString() : '—'}
                </td>
                <td class="border px-1 py-1">
                    <input type="number"
                           min="0"
                           step="1"
                           class="w-16 border rounded px-1 py-0.5 text-right input-frascos"
                           value="${frascosValue}"
                           oninput="recalcularResumenPresentaciones(${filaId})">
                </td>
            `;
                tbody.appendChild(tr);
            });

            recalcularResumenPresentaciones(filaId);
        }

        function recalcularResumenPresentaciones(filaId) {
            const fila = document.getElementById(`fila_${filaId}`);
            if (!fila) return;

            const dosisInput = fila.querySelector('.dosis-input');
            const objetivo = parseFloat(dosisInput?.value || "0");

            const rows = fila.querySelectorAll('.presentacion-row');
            let aportada = 0;

            rows.forEach(r => {
                const mgPorFrasco = parseFloat(r.dataset.cantidadMg || "0");
                const frascos = parseFloat(r.querySelector('.input-frascos')?.value || "0");
                aportada += mgPorFrasco * frascos;
            });

            const resumen = document.getElementById(`resumen_dosis_${filaId}`);
            if (resumen) {
                const diff = objetivo - aportada;
                const textoDiff = diff > 0 ?
                    `faltan ${diff.toFixed(2)} mg` :
                    diff < 0 ?
                    `sobran ${Math.abs(diff).toFixed(2)} mg` :
                    'dosis exacta';

                resumen.innerHTML = `
                Dosis objetivo: ${objetivo.toFixed(2)} mg<br>
                Dosis aportada: ${aportada.toFixed(2)} mg (${textoDiff})
            `;
            }
        }
        // ==================================

        // ===== Diluyente común + presentaciones de diluyente =====
        function getDiluyenteComunId() {
            const filas = document.querySelectorAll('#medicamentos_mezcla tr');
            let diluyenteComun = null;

            for (const fila of filas) {
                const selDil = fila.querySelector('[data-name="diluyente"]');
                if (!selDil || !selDil.value) continue;

                if (diluyenteComun === null) {
                    diluyenteComun = selDil.value;
                } else if (String(diluyenteComun) !== String(selDil.value)) {
                    return null;
                }
            }

            return diluyenteComun;
        }

        function updateDiluentPresentationSelector(preservedId = null) {
            const select = document.querySelector('[data-name="diluent_presentation_id"]');
            const hint = document.getElementById('diluent_presentation_hint');
            if (!select) return;

            const dilId = getDiluyenteComunId();

            select.innerHTML = '';
            if (!dilId || !diluentPresentationsPorDiluyente[dilId]) {
                const opt = document.createElement('option');
                opt.value = '';
                opt.textContent = 'Seleccione un diluyente (y su presentación) para esta mezcla';
                select.appendChild(opt);

                if (hint) {
                    hint.textContent = 'Se sugerirá una presentación en función del volumen de dilución.';
                }
                return;
            }

            const lista = diluentPresentationsPorDiluyente[dilId] || [];

            const placeholder = document.createElement('option');
            placeholder.value = '';
            placeholder.textContent = 'Seleccione una presentación';
            select.appendChild(placeholder);

            lista.forEach(p => {
                const opt = document.createElement('option');
                opt.value = p.id;

                let txt = `${p.presentacion} (${p.volume_ml} mL`;
                if (p.denominacion_comercial) txt += ` · ${p.denominacion_comercial}`;
                if (p.lote) txt += ` · Lote ${p.lote}`;
                if (p.caducidad) txt += ` · Cad. ${p.caducidad}`;
                txt += ')';

                opt.textContent = txt;
                select.appendChild(opt);
            });

            if (preservedId) {
                select.value = String(preservedId);

                if (hint) {
                    const optSel = select.options[select.selectedIndex];
                    if (optSel && optSel.value) {
                        hint.textContent = `Presentación guardada previamente: ${optSel.textContent}.`;
                    } else {
                        hint.textContent = 'Se sugerirá una presentación en función del volumen de dilución.';
                    }
                }
            }

        }

        function autoSelectDiluentPresentationForVolume() {
            const select = document.querySelector('[data-name="diluent_presentation_id"]');
            const hint = document.getElementById('diluent_presentation_hint');
            const volumenInput = document.querySelector('[data-name="volumen_dilucion"]');
            if (!select || !volumenInput) return;

            const dilId = getDiluyenteComunId();
            if (!dilId) return;

            const lista = diluentPresentationsPorDiluyente[dilId] || [];
            if (!lista.length) return;

            const vol = parseFloat(volumenInput.value || '0');
            if (!vol || vol <= 0) return;

            let elegida = null;

            lista.forEach(p => {
                const v = parseFloat(p.volume_ml || '0');
                if (!v || v <= 0) return;

                if (v >= vol && (!elegida || v < elegida.volume_ml)) {
                    elegida = {
                        ...p,
                        volume_ml: v
                    };
                }
            });

            if (!elegida) {
                lista.forEach(p => {
                    const v = parseFloat(p.volume_ml || '0');
                    if (!v || v <= 0) return;

                    if (!elegida || v > elegida.volume_ml) {
                        elegida = {
                            ...p,
                            volume_ml: v
                        };
                    }
                });
            }

            if (elegida) {
                select.value = String(elegida.id);
                if (hint) {
                    hint.textContent =
                        `Sugerida: ${elegida.presentacion} (${elegida.volume_ml} mL) ` +
                        `para un volumen de dilución de ${vol} mL. Puedes cambiarla si lo requieres.`;
                }
            }
        }
        // ==================================

        function renderMezcla(mezclaData) {

            const mezclaDiv = document.createElement('div');
            mezclaDiv.classList.add("border", "border-black", "p-4", "relative");

            // 👇 diluyente de la mezcla (todos los meds comparten diluyente_id)
            const firstDiluentId = (mezclaData.medicamentos && mezclaData.medicamentos.length > 0) ?
                mezclaData.medicamentos[0].diluyente_id :
                null;

            // 👇 id de presentación guardado en BD
            const selectedDiluentPresId = mezclaData.diluent_presentation_id || null;

            // Opciones del select de presentación de diluyente
            let opcionesPresentacionDiluyente = '<option value="">Selecciona presentación</option>';

            if (firstDiluentId && diluentPresentationsPorDiluyente[firstDiluentId]) {
                diluentPresentationsPorDiluyente[firstDiluentId].forEach(p => {
                    const seleccionado = Number(selectedDiluentPresId) === Number(p.id) ? 'selected' : '';
                    const cad = p.caducidad ? (new Date(p.caducidad)).toLocaleDateString() : '—';

                    opcionesPresentacionDiluyente += `
                <option value="${p.id}" ${seleccionado}>
                    ${p.presentacion}
                    ${p.denominacion_comercial ? ' · ' + p.denominacion_comercial : ''}
                    ${p.lote ? ' · Lote ' + p.lote : ''}
                    ${cad !== '—' ? ' · Cad. ' + cad : ''}
                </option>
            `;
                });
            }

            mezclaDiv.innerHTML = `
    <h3 class="text-lg font-semibold mb-4">Mezcla</h3>

    <table class="table-auto w-full border text-center mb-4">
        <thead class="bg-gray-100">
            <tr>
                <th class="border px-2 py-1 text-xs">MEDICAMENTO</th>
                <th class="border px-2 py-1 text-xs">DOSIS</th>
                <th class="border px-2 py-1 text-xs">DILUYENTE</th>
                <th class="border px-2 py-1 text-xs">VÍA DE ADMINISTRACIÓN</th>
            </tr>
        </thead>
        <tbody id="medicamentos_mezcla"></tbody>
    </table>

    <div class="grid grid-cols-2 gap-4 mb-4">
        <div>
            <label>Volumen de dilución (ml)*</label>
            <input type="number" data-name="volumen_dilucion"
                   class="w-full border rounded px-2 py-1 text-sm"
                   value="${mezclaData.volumen_dilucion ?? ''}">
        </div>
        <div>
            <label>Tiempo de infusión (min)*</label>
            <input type="number" data-name="tiempo_infusion"
                   class="w-full border rounded px-2 py-1 text-sm"
                   value="${mezclaData.tiempo_infusion ?? ''}">
        </div>
    </div>

    <div class="mb-4">
        <label>Presentación del diluyente</label>
        <select data-name="diluent_presentation_id"
                class="w-full border rounded px-2 py-1 text-sm">
            ${opcionesPresentacionDiluyente}
        </select>
        <p class="text-xs text-gray-500" id="diluent_presentation_hint">
            Se sugerirá en función del volumen de dilución de la mezcla.
        </p>
    </div>

    <div class="grid grid-cols-2 gap-4 mb-4">
        <div class="flex items-center gap-2">
            <input type="checkbox"
                   data-name="set_infusion"
                   class="w-4 h-4"
                   ${mezclaData.set_infusion ? 'checked' : ''}
                   onchange="toggleSetInfusion(this)">
            <label class="select-none">Set de infusión</label>
        </div>
        <div>
            <label>Infusor</label>
            <select data-name="infusor_id"
                    class="w-full border rounded px-2 py-1 text-sm"
                    onchange="toggleInfusorSelect(this)">
                <option value="">Selecciona infusor</option>
                ${
                    (infusors || [])
                        .map(i => `
                                        <option value="${i.id}"
                                            ${Number(mezclaData.infusor_id || '') === Number(i.id) ? 'selected' : ''}>
                                            ${i.nombre_generico ?? i.nombre_comercial ?? ('Infusor #'+i.id)}
                                        </option>
                                    `).join('')
                }
            </select>
            <p class="text-xs text-gray-500">
                Se habilita si la mezcla contiene medicamento(s) que admiten infusor.
            </p>
        </div>
    </div>

    <button type="button"
            class="btn-agregar-medicamento bg-green-600 hover:bg-green-700 text-white text-xs font-medium py-1 px-2 rounded">
        + Agregar Medicamento
    </button>
`;


            document.getElementById('contenedorMezcla').appendChild(mezclaDiv);

            const tbody = mezclaDiv.querySelector('#medicamentos_mezcla');
            (mezclaData.medicamentos || []).forEach(med => {
                contadorFilas++;
                const fila = document.createElement('tr');
                fila.id = `fila_${contadorFilas}`;

                const medicamentoId = med.medicamento_id;
                const data = infoAdicional[medicamentoId] || {
                    diluyentes: [],
                    vias: []
                };

                const diluyenteOptions = data.diluyentes.map(d =>
                    `<option value="${d.id}" ${d.id == med.diluyente_id ? 'selected' : ''}>${d.name ?? d.denominacion_generica ?? '—'}</option>`
                ).join('');

                const viaOptions = data.vias.map(v =>
                    `<option value="${v.id}" ${v.id == med.via_administracion_id ? 'selected' : ''}>${v.name}</option>`
                ).join('');

                fila.innerHTML = `
                <td class="border align-top">
                    <select class="medicamento-select w-full border px-2 py-1 text-sm"
                            data-name="medicamento"
                            name="medicamento_existente[]"
                            onchange="actualizarDiluentesYVias(this, ${contadorFilas}); onMedicamentoChange(this)">
                        ${medicamentos.map(m =>
                            `<option value="${m.id}" ${m.id == medicamentoId ? 'selected' : ''}>
                                                    ${m.denominacion} (${m.presentacion})
                                                 </option>`
                        ).join('')}
                    </select>

                    <div class="mt-2 text-left">
                        <button type="button"
                                class="text-xs text-blue-600 underline"
                                onclick="togglePresentaciones(${contadorFilas})">
                            Configurar presentaciones
                        </button>
                    </div>
                </td>
                <td class="border align-top">
                    <input type="number"
                           name="dosis_existente[]"
                           value="${med.dosis}"
                           class="w-full border px-2 py-1 text-sm dosis-input"
                           oninput="recalcularResumenPresentaciones(${contadorFilas})">
                    <div class="mt-1 text-sm text-red-600" id="resumen_dosis_${contadorFilas}">
                        Dosis objetivo: ${med.dosis} mg<br>
                        Dosis aportada: 0 mg (restan ${med.dosis} mg)
                    </div>
                </td>
                <td class="border align-top">
                    <select name="diluyente_existente[]"
                            data-name="diluyente"
                            class="w-full border px-2 py-1 text-sm">
                        <option value="">Diluyentes</option>
                        ${diluyenteOptions}
                    </select>
                </td>
                <td class="border align-top">
                    <select name="via_existente[]"
                            data-name="via_administracion"
                            class="w-full border px-2 py-1 text-sm">
                        <option value="">Vía de admin</option>
                        ${viaOptions}
                    </select>

                    <div id="presentaciones_wrap_${contadorFilas}"
                         class="mt-2 border-t pt-2 hidden">
                        <div class="text-xs font-semibold mb-1">
                            Presentaciones disponibles
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-[11px] border">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="border px-1 py-1">Presentación</th>
                                        <th class="border px-1 py-1">Cant. (mg)</th>
                                        <th class="border px-1 py-1">Vol (mL)</th>
                                        <th class="border px-1 py-1">Lote</th>
                                        <th class="border px-1 py-1">Caducidad</th>
                                        <th class="border px-1 py-1">Frascos</th>
                                    </tr>
                                </thead>
                                <tbody id="presentaciones_body_${contadorFilas}">
                                    <!-- dinámico -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </td>
            `;
                tbody.appendChild(fila);

                const presentacionesGuardadas = med.presentaciones_usadas || [];
                inicializarPresentacionesFila(contadorFilas, medicamentoId, presentacionesGuardadas);
            });

            const setCb = mezclaDiv.querySelector('[data-name="set_infusion"]');
            const selInf = mezclaDiv.querySelector('[data-name="infusor_id"]');

            if (selInf.value) {
                setCb.checked = false;
                setCb.disabled = true;
            }
            if (setCb.checked) {
                selInf.value = '';
                selInf.disabled = true;
            }

            // id de presentación guardada en BD (puede ser null)
            const savedDilPresId = mezclaData.diluent_presentation_id || null;

            // Rellena el select con las presentaciones del diluyente común
            // y, si existe, marca la guardada
            updateDiluentPresentationSelector(savedDilPresId);

            // ⚠️ Solo sugerimos automáticamente si NO hay una presentación guardada
            if (!savedDilPresId) {
                autoSelectDiluentPresentationForVolume();
            }

            const volInput = mezclaDiv.querySelector('[data-name="volumen_dilucion"]');
            if (volInput) {
                volInput.addEventListener('input', () => {
                    autoSelectDiluentPresentationForVolume();
                });
            }

            updateInfusorDisponibilidad();

        }

        function actualizarDiluentesYVias(selectElem, filaId) {
            const medicamentoId = selectElem.value;
            const fila = document.getElementById(`fila_${filaId}`);
            const data = infoAdicional[medicamentoId] || {
                diluyentes: [],
                vias: []
            };

            const selectDiluyente = fila.querySelector('[data-name="diluyente"]');
            const selectVia = fila.querySelector('[data-name="via_administracion"]');

            selectDiluyente.innerHTML = `<option value="">Diluyentes</option>` +
                data.diluyentes.map(d => `<option value="${d.id}">${d.name ?? d.denominacion_generica ?? '—'}</option>`)
                .join('');

            selectVia.innerHTML = `<option value="">Vía de admin</option>` +
                data.vias.map(v => `<option value="${v.id}">${v.name}</option>`).join('');

            updateInfusorDisponibilidad();
            updateDiluentPresentationSelector();
            autoSelectDiluentPresentationForVolume();
        }

        document.addEventListener("DOMContentLoaded", () => {
            renderMezcla(mezcla);
        });

        document.addEventListener("click", function(e) {
            if (e.target.classList.contains("btn-agregar-medicamento")) {
                const tbody = document.querySelector("#medicamentos_mezcla");
                contadorFilas++;
                const fila = document.createElement("tr");
                fila.id = `fila_${contadorFilas}`;

                fila.innerHTML = `
                <td class="border align-top">
                    <select class="medicamento-select w-full border px-2 py-1 text-sm"
                            data-name="medicamento"
                            name="nuevo_medicamento[]"
                            onchange="actualizarDiluentesYVias(this, ${contadorFilas}); onMedicamentoChange(this)">
                        <option value="">Seleccione</option>
                        ${medicamentos.map(m => `<option value="${m.id}">${m.denominacion} (${m.presentacion})</option>`).join('')}
                    </select>

                    <div class="mt-2 text-left">
                        <button type="button"
                                class="text-xs text-blue-600 underline"
                                onclick="togglePresentaciones(${contadorFilas})">
                            Configurar presentaciones
                        </button>
                    </div>
                </td>
                <td class="border align-top">
                    <input type="number"
                           name="nueva_dosis[]"
                           class="w-full border px-2 py-1 text-sm dosis-input"
                           oninput="recalcularResumenPresentaciones(${contadorFilas})">
                    <div class="mt-1 text-xs text-gray-600" id="resumen_dosis_${contadorFilas}">
                        Dosis objetivo: 0 mg<br>
                        Dosis aportada: 0 mg (restan 0 mg)
                    </div>
                </td>
                <td class="border align-top">
                    <select name="nuevo_diluyente[]"
                            data-name="diluyente"
                            class="w-full border px-2 py-1 text-sm">
                        <option value="">Diluyentes</option>
                    </select>
                </td>
                <td class="border align-top">
                    <select name="nueva_via[]"
                            data-name="via_administracion"
                            class="w-full border px-2 py-1 text-sm">
                        <option value="">Vía de admin</option>
                    </select>

                    <div id="presentaciones_wrap_${contadorFilas}"
                         class="mt-2 border-t pt-2 hidden">
                        <div class="text-xs font-semibold mb-1">
                            Presentaciones disponibles
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-[11px] border">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="border px-1 py-1">Presentación</th>
                                        <th class="border px-1 py-1">Cant. (mg)</th>
                                        <th class="border px-1 py-1">Vol (mL)</th>
                                        <th class="border px-1 py-1">Lote</th>
                                        <th class="border px-1 py-1">Caducidad</th>
                                        <th class="border px-1 py-1">Frascos</th>
                                    </tr>
                                </thead>
                                <tbody id="presentaciones_body_${contadorFilas}">
                                    <!-- dinámico -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </td>
            `;

                tbody.appendChild(fila);
                updateInfusorDisponibilidad();
                updateDiluentPresentationSelector();
                autoSelectDiluentPresentationForVolume();
            }
        });

        // Submit: construye mezcla_json incluyendo presentaciones
        document.getElementById("formularioMezcla").addEventListener("submit", function(e) {
            e.preventDefault();

            const setCb = document.querySelector('[data-name="set_infusion"]');
            const selInf = document.querySelector('[data-name="infusor_id"]');
            const selDilPres = document.querySelector('[data-name="diluent_presentation_id"]');

            const mezclaPayload = {
                volumen_dilucion: document.querySelector('[data-name="volumen_dilucion"]').value,
                tiempo_infusion: document.querySelector('[data-name="tiempo_infusion"]').value,
                set_infusion: !!(setCb && setCb.checked),
                infusor_id: (selInf && selInf.value) ? selInf.value : null,
                diluent_presentation_id: (selDilPres && selDilPres.value) ? selDilPres.value : null,
                medicamentos: []
            };

            document.querySelectorAll('[name="medicamento_existente[]"]').forEach((medicamentoSelect) => {
                const fila = medicamentoSelect.closest('tr');
                const medObj = {
                    medicamento_id: medicamentoSelect.value,
                    nombre: medicamentoSelect.options[medicamentoSelect.selectedIndex].text,
                    dosis: fila.querySelector('[name="dosis_existente[]"]').value,
                    diluyente_id: fila.querySelector('[name="diluyente_existente[]"]').value || null,
                    via_administracion_id: fila.querySelector('[name="via_existente[]"]').value || null,
                    presentaciones: []
                };

                const presRows = fila.querySelectorAll('.presentacion-row');
                presRows.forEach(r => {
                    const frascos = parseFloat(r.querySelector('.input-frascos')?.value || "0");
                    const batchId = r.dataset.batchId || null;

                    if (frascos > 0 && batchId) {
                        medObj.presentaciones.push({
                            batch_id: batchId,
                            presentation_id: r.dataset.presentationId,
                            frascos: frascos
                        });
                    }
                });
                mezclaPayload.medicamentos.push(medObj);
            });

            document.querySelectorAll('[name="nuevo_medicamento[]"]').forEach((medicamentoSelect) => {
                const fila = medicamentoSelect.closest('tr');
                if (medicamentoSelect.value) {
                    const medObj = {
                        medicamento_id: medicamentoSelect.value,
                        nombre: medicamentoSelect.options[medicamentoSelect.selectedIndex].text,
                        dosis: fila.querySelector('[name="nueva_dosis[]"]').value,
                        diluyente_id: fila.querySelector('[name="nuevo_diluyente[]"]').value || null,
                        via_administracion_id: fila.querySelector('[name="nueva_via[]"]').value || null,
                        presentaciones: []
                    };

                    const presRows = fila.querySelectorAll('.presentacion-row');
                    presRows.forEach(r => {
                        const frascos = parseFloat(r.querySelector('.input-frascos')?.value || "0");
                        const batchId = r.dataset.batchId || null;

                        if (frascos > 0 && batchId) {
                            medObj.presentaciones.push({
                                batch_id: batchId,
                                presentation_id: r.dataset.presentationId,
                                frascos: frascos
                            });
                        }
                    });

                    mezclaPayload.medicamentos.push(medObj);
                }
            });

            if (mezclaPayload.medicamentos.length > 0) {
                let refDil = mezclaPayload.medicamentos[0].diluyente_id;
                let refVia = mezclaPayload.medicamentos[0].via_administracion_id;
                for (const med of mezclaPayload.medicamentos) {
                    if (med.diluyente_id !== refDil || med.via_administracion_id !== refVia) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Todos los medicamentos de la mezcla deben tener el mismo diluyente y la misma vía de administración.'
                        });
                        return;
                    }
                }
            }

            document.getElementById("mezcla_json").value = JSON.stringify(mezclaPayload);

            Swal.fire({
                title: '¿Actualizar mezcla?',
                text: 'Se guardarán los cambios realizados.',
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

        @if ($errors->any())
            Swal.fire({
                icon: 'error',
                title: 'Error al actualizar',
                html: `{!! implode('<br>', $errors->all()) !!}`,
                customClass: {
                    confirmButton: 'swal-button-confirm',
                    cancelButton: 'swal-button-cancel'
                }
            });
        @endif

        @if (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: '{{ session('error') }}',
                customClass: {
                    confirmButton: 'swal-button-confirm',
                    cancelButton: 'swal-button-cancel'
                }
            });
        @endif

        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: '¡Éxito!',
                text: '{{ session('success') }}',
                customClass: {
                    confirmButton: 'swal-button-confirm',
                    cancelButton: 'swal-button-cancel'
                }
            });
        @endif
    </script>



</x-admin-layout>
