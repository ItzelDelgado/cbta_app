<x-admin-layout>
    <h1 class="text-2xl font-semibold mb-4">
        Presentaciones de: {{ $catalog->denominacion }} (#{{ $catalog->id }})
    </h1>

    <form action="{{ route('admin.oncologicos.medicines.catalog.presentations.store', $catalog) }}" method="POST"
        class="space-y-6">
        @csrf

        @if ($errors->any())
            <div class="mb-4 p-3 rounded bg-red-100 text-red-800">
                <div class="font-semibold mb-1">Revisa los campos:</div>
                <ul class="list-disc list-inside text-sm">
                    @foreach ($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="flex items-center justify-between">
            <div class="text-sm text-gray-600">
                Agrega una o más presentaciones. Los lotes/caducidades se gestionan por laboratorio desde el módulo de
                Inventario.
            </div>

            <button type="button" id="addRow" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded">
                + Agregar presentación
            </button>
        </div>

        <div id="rows" class="space-y-4"></div>

        <div class="text-right">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded">
                Guardar presentaciones
            </button>
        </div>
    </form>

    <template id="row-tpl">
        <div class="p-4 border rounded-lg bg-white">
            <div class="flex items-center justify-between mb-3">
                <div class="font-semibold text-gray-700">
                    Presentación #__n__
                </div>

                <button type="button" class="rm-row px-3 py-2 bg-red-500 hover:bg-red-600 text-white rounded">
                    Eliminar
                </button>
            </div>

            <div class="grid grid-cols-12 gap-3">

                <!-- =========================
             DATOS DE PRESENTACIÓN
        ========================== -->
                <div class="col-span-12 md:col-span-4">
                    <label class="text-sm">Presentación</label>
                    <input class="w-full border rounded p-2" name="presentations[__i__][presentacion]"
                        placeholder="Frasco 500 mg" required>
                </div>

                <div class="col-span-6 md:col-span-2">
                    <label class="text-sm">Contenido (valor)</label>
                    <input class="w-full border rounded p-2" type="number" step="0.01" min="0"
                        name="presentations[__i__][contenido_valor]" required>
                </div>

                <div class="col-span-6 md:col-span-2">
                    <label class="text-sm">Unidad</label>
                    <select class="w-full border rounded p-2" name="presentations[__i__][contenido_unidad]">
                        <option>mg</option>
                        <option>g</option>
                        <option>ml</option>
                        <option>UI</option>
                    </select>
                </div>

                <div class="col-span-12 md:col-span-4">
                    <label class="text-sm">Marca (opcional)</label>
                    <input class="w-full border rounded p-2" name="presentations[__i__][marca]"
                        placeholder="Ej. Pfizer">
                </div>

                <!-- =========================
             COSTOS / COMPOSICIÓN
        ========================== -->
                <div class="col-span-12 md:col-span-3">
                    <label class="text-sm">Precio frasco (opcional)</label>
                    <input class="w-full border rounded p-2" type="number" step="0.01" min="0"
                        name="presentations[__i__][precio_frasco]" placeholder="0.00">
                </div>

                <div class="col-span-12 md:col-span-3">
                    <label class="text-sm">Cantidad de medicamento (mg) (opcional)</label>
                    <input class="w-full border rounded p-2" type="number" step="0.01" min="0"
                        name="presentations[__i__][cantidad_medicamento]" placeholder="Ej. 500">
                </div>

                <div class="col-span-12 md:col-span-3">
                    <label class="text-sm">Volumen del diluyente (mL) (opcional)</label>
                    <input class="w-full border rounded p-2" type="number" step="0.01" min="0"
                        name="presentations[__i__][volumen_diluyente]" placeholder="Ej. 10">
                </div>

                <!-- =========================
             ESTABILIDAD (NUEVO)
        ========================== -->
                <div class="col-span-12 md:col-span-3">
                    <label class="text-sm">Estabilidad (horas) (opcional)</label>
                    <input class="w-full border rounded p-2" type="number" min="0"
                        name="presentations[__i__][stability_hours]" placeholder="Ej. 24">
                    <div class="text-xs text-gray-500 mt-1">
                        Tiempo máximo de uso recomendado.
                    </div>
                </div>

                <div class="col-span-6 md:col-span-2">
                    <label class="text-sm">Temp. mín (°C) (opcional)</label>
                    <input class="w-full border rounded p-2" type="number" min="0"
                        name="presentations[__i__][temp_min_c]" placeholder="Ej. 8">
                </div>

                <div class="col-span-6 md:col-span-2">
                    <label class="text-sm">Temp. máx (°C) (opcional)</label>
                    <input class="w-full border rounded p-2" type="number" min="0"
                        name="presentations[__i__][temp_max_c]" placeholder="Ej. 20">
                </div>

                <div class="col-span-12 md:col-span-5">
                    <label class="text-sm">Leyenda / Indicaciones (opcional)</label>
                    <textarea class="w-full border rounded p-2" rows="2" name="presentations[__i__][legend]"
                        placeholder="Ej. Refrigerar. Proteger de la luz. No agitar."></textarea>
                </div>

                <!-- =========================
     INVENTARIO POR LABORATORIO (INFO)
========================== -->
                <div class="col-span-12 md:col-span-6">
                    <div class="p-3 rounded bg-blue-50 border border-blue-200 text-blue-800 text-sm">
                        <div class="font-semibold">Lotes y caducidades</div>
                        <div class="text-xs mt-1">
                            Se administran por laboratorio desde <b>Oncológicos → Inventario</b>.
                            Aquí solo se capturan los datos de la presentación.
                        </div>
                    </div>
                </div>

                <!-- =========================
             DISPONIBILIDAD
        ========================== -->
                <div class="col-span-12 md:col-span-3">
                    <label class="text-sm">Disponible</label>
                    <select class="w-full border rounded p-2" name="presentations[__i__][is_available]">
                        <option value="1" selected>Sí</option>
                        <option value="0">No</option>
                    </select>
                </div>

            </div>
        </div>
    </template>

    <script>
        const rows = document.getElementById('rows');
        const tpl = document.getElementById('row-tpl').innerHTML;
        const add = document.getElementById('addRow');
        let i = 0;

        const addRow = () => {
            const html = tpl
                .replaceAll('__i__', i)
                .replaceAll('__n__', (i + 1).toString());
            rows.insertAdjacentHTML('beforeend', html);
            i++;
        };

        add.addEventListener('click', addRow);

        rows.addEventListener('click', (e) => {
            if (e.target.classList.contains('rm-row')) {
                e.target.closest('.p-4').remove();
            }
        });

        // primera fila inicial
        addRow();
    </script>
</x-admin-layout>
