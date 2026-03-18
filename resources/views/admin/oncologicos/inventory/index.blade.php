<x-admin-layout>
    <div class="mt-2 mb-4 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-medium text-gray-800">
                Inventario (Lotes, caducidades y stock)
            </h1>

            <div class="text-sm text-gray-600 mt-1">
                Laboratorio:
                <span class="font-semibold text-gray-800">
                    {{ $laboratory->nombre ?? '—' }}
                </span>
                @if (!empty($laboratory->estado))
                    <span class="text-gray-500">· {{ $laboratory->estado }}</span>
                @endif
            </div>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.oncologicos.inventory.selectLaboratory') }}"
               class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-2 px-4 rounded">
                Cambiar laboratorio
            </a>

            <button type="button" id="btn-guardar"
                class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                Guardar cambios
            </button>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-4 mb-4">
        <form method="GET"
              action="{{ route('admin.oncologicos.inventory.index') }}"
              class="grid grid-cols-1 md:grid-cols-4 gap-3">

            <input type="hidden" name="laboratory_id" value="{{ $laboratoryId }}">

            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Buscar</label>
                <input type="text"
                       name="q"
                       value="{{ request('q') }}"
                       placeholder="Denominación del medicamento, presentación, marca o lote..."
                       class="w-full rounded border-gray-300 focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Solo con stock</label>
                <select name="stock" class="w-full rounded border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Todos</option>
                    <option value="1" {{ request('stock') == '1' ? 'selected' : '' }}>Sí</option>
                    <option value="0" {{ request('stock') == '0' ? 'selected' : '' }}>No</option>
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Buscar
                </button>

                <a href="{{ route('admin.oncologicos.inventory.index', ['laboratory_id' => $laboratoryId]) }}"
                   class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-2 px-4 rounded">
                    Limpiar
                </a>
            </div>
        </form>
    </div>

    <form id="bulk-form" method="POST" action="{{ route('admin.oncologicos.inventory.batches.bulkUpdate') }}">
        @csrf

        <input type="hidden" name="laboratory_id" value="{{ $laboratoryId }}">

        <div class="relative overflow-x-auto bg-white rounded-lg shadow">
            <table class="w-full text-sm text-left text-gray-600">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th class="px-4 py-3">Medicamento</th>
                        <th class="px-4 py-3">Presentación</th>
                        <th class="px-4 py-3">Marca</th>
                        <th class="px-4 py-3">Lote</th>
                        <th class="px-4 py-3">Caducidad</th>
                        <th class="px-4 py-3">Ingreso</th>
                        <th class="px-4 py-3 text-center">Stock inicial</th>
                        <th class="px-4 py-3 text-center">Stock actual</th>
                        <th class="px-4 py-3 text-center">Estado</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($rows as $row)
                        @php
                            $cad = !empty($row->caducidad_current)
                                ? \Carbon\Carbon::parse($row->caducidad_current)->format('Y-m-d')
                                : '';

                            $fechaIngreso = !empty($row->fecha_ingreso)
                                ? \Carbon\Carbon::parse($row->fecha_ingreso)->format('Y-m-d')
                                : '';

                            $origLote = $row->lote_current ?? '';
                            $origCad = $cad;
                            $origFechaIngreso = $fechaIngreso;
                            $origStockInicial = (int) ($row->stock_inicial ?? 0);
                            $origStockActual = (int) ($row->stock_actual ?? 0);

                            $hasStock = $origStockActual > 0;
                        @endphp

                        <tr class="bg-white border-b last:border-b-0 row-inv"
                            data-batch-id="{{ $row->batch_id ?? '' }}"
                            data-presentation-id="{{ $row->presentation_id }}">
                            <td class="px-4 py-2 font-medium text-gray-800">
                                {{ $row->denominacion }}
                            </td>

                            <td class="px-4 py-2">
                                <div class="font-medium text-gray-800">{{ $row->presentacion }}</div>
                                <div class="text-xs text-gray-500">
                                    {{ $row->contenido_valor }} {{ $row->contenido_unidad }}
                                    @if (!is_null($row->volumen_diluyente))
                                        · Diluyente: {{ $row->volumen_diluyente }} mL
                                    @endif
                                </div>
                            </td>

                            <td class="px-4 py-2">
                                {{ $row->marca ?: '—' }}
                            </td>

                            <td class="px-4 py-2 min-w-[140px]">
                                <input type="text"
                                       class="inv-input inv-lote w-full rounded border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                       value="{{ $origLote }}"
                                       placeholder="Lote..."
                                       data-original="{{ $origLote }}">
                            </td>

                            <td class="px-4 py-2 min-w-[150px]">
                                <input type="date"
                                       class="inv-input inv-cad w-full rounded border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                       value="{{ $origCad }}"
                                       data-original="{{ $origCad }}">
                            </td>

                            <td class="px-4 py-2 min-w-[150px]">
                                <input type="date"
                                       class="inv-input inv-fecha-ingreso w-full rounded border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                       value="{{ $origFechaIngreso }}"
                                       data-original="{{ $origFechaIngreso }}">
                            </td>

                            <td class="px-4 py-2 min-w-[110px]">
                                <input type="number"
                                       min="0"
                                       class="inv-input inv-stock-inicial w-full rounded border-gray-300 text-center focus:border-blue-500 focus:ring-blue-500"
                                       value="{{ $origStockInicial }}"
                                       data-original="{{ $origStockInicial }}">
                            </td>

                            <td class="px-4 py-2 min-w-[110px]">
                                <input type="number"
                                       min="0"
                                       class="inv-input inv-stock-actual w-full rounded border-gray-300 text-center focus:border-blue-500 focus:ring-blue-500"
                                       value="{{ $origStockActual }}"
                                       data-original="{{ $origStockActual }}">
                            </td>

                            <td class="px-4 py-2 text-center min-w-[120px]">
                                <span class="stock-badge inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                    {{ $hasStock ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $hasStock ? 'Con stock' : 'Sin stock' }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-6 text-center text-gray-500">
                                No hay medicamentos para mostrar con los filtros actuales.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <input type="hidden" name="items" id="bulk-items" value="">
    </form>

    @push('js')
        <script>
            (function() {
                const form = document.getElementById('bulk-form');
                const btnGuardar = document.getElementById('btn-guardar');
                const bulkItems = document.getElementById('bulk-items');

                function toInt(value) {
                    const n = parseInt(value, 10);
                    return Number.isNaN(n) ? 0 : n;
                }

                function toStr(value) {
                    return (value ?? '').toString().trim();
                }

                function updateStockBadge(row) {
                    const stockActual = toInt(row.querySelector('.inv-stock-actual')?.value);
                    const badge = row.querySelector('.stock-badge');

                    if (!badge) return;

                    const hasStock = stockActual > 0;

                    badge.textContent = hasStock ? 'Con stock' : 'Sin stock';
                    badge.classList.remove('bg-green-100', 'text-green-800', 'bg-red-100', 'text-red-800');
                    badge.classList.add(hasStock ? 'bg-green-100' : 'bg-red-100');
                    badge.classList.add(hasStock ? 'text-green-800' : 'text-red-800');
                }

                function markDirty(row) {
                    row.classList.add('dirty');
                    updateStockBadge(row);
                }

                document.querySelectorAll('.row-inv .inv-input').forEach(inp => {
                    inp.addEventListener('input', (e) => {
                        markDirty(e.target.closest('.row-inv'));
                    });

                    inp.addEventListener('change', (e) => {
                        markDirty(e.target.closest('.row-inv'));
                    });
                });

                btnGuardar.addEventListener('click', () => {
                    const cambios = [];

                    document.querySelectorAll('.row-inv').forEach(row => {
                        const batchId = row.dataset.batchId || null;
                        const presentationId = row.dataset.presentationId ? Number(row.dataset.presentationId) : null;

                        const loteInput = row.querySelector('.inv-lote');
                        const cadInput = row.querySelector('.inv-cad');
                        const fechaIngresoInput = row.querySelector('.inv-fecha-ingreso');
                        const stockInicialInput = row.querySelector('.inv-stock-inicial');
                        const stockActualInput = row.querySelector('.inv-stock-actual');

                        const lote = toStr(loteInput?.value);
                        const cad = toStr(cadInput?.value);
                        const fechaIngreso = toStr(fechaIngresoInput?.value);
                        const stockInicial = toInt(stockInicialInput?.value);
                        const stockActual = toInt(stockActualInput?.value);

                        const origLote = toStr(loteInput?.dataset.original);
                        const origCad = toStr(cadInput?.dataset.original);
                        const origFechaIngreso = toStr(fechaIngresoInput?.dataset.original);
                        const origStockInicial = toInt(stockInicialInput?.dataset.original);
                        const origStockActual = toInt(stockActualInput?.dataset.original);

                        const changed =
                            lote !== origLote ||
                            cad !== origCad ||
                            fechaIngreso !== origFechaIngreso ||
                            stockInicial !== origStockInicial ||
                            stockActual !== origStockActual;

                        if (changed) {
                            cambios.push({
                                batch_id: batchId ? Number(batchId) : null,
                                presentation_id: presentationId,
                                lote: lote,
                                caducidad: cad,
                                fecha_ingreso: fechaIngreso,
                                stock_inicial: stockInicial,
                                stock_actual: stockActual
                            });
                        }
                    });

                    if (cambios.length === 0) {
                        Swal.fire({
                            icon: 'info',
                            title: 'Sin cambios',
                            text: 'No hay modificaciones para guardar.'
                        });
                        return;
                    }

                    const faltanDatos = cambios.some(item => !item.lote || !item.caducidad);
                    if (faltanDatos) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Datos incompletos',
                            text: 'Cada registro modificado debe tener lote y caducidad.'
                        });
                        return;
                    }

                    bulkItems.value = JSON.stringify(cambios);

                    Swal.fire({
                        title: '¿Guardar cambios?',
                        text: `Se actualizarán ${cambios.length} registro(s).`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Sí, guardar',
                        cancelButtonText: 'Cancelar',
                        customClass: {
                            confirmButton: 'swal-button-confirm',
                            cancelButton: 'swal-button-cancel'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });

                document.querySelectorAll('.row-inv').forEach(row => updateStockBadge(row));
            })();
        </script>
    @endpush
</x-admin-layout>
