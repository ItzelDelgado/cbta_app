<x-admin-layout>
    <h1 class="text-2xl font-semibold mb-4">
        Editar presentación de: {{ $catalog->denominacion }} (#{{ $catalog->id }})
    </h1>

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

    @if (session('success'))
        <div class="mb-4 p-3 rounded bg-green-100 text-green-800">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('admin.oncologicos.medicines.catalog.presentations.update', [$catalog, $presentation]) }}"
        method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="p-4 border rounded-lg bg-white">
            <div class="grid grid-cols-12 gap-3">

                <!-- =========================
             DATOS DE PRESENTACIÓN
        ========================== -->
                <div class="col-span-12 md:col-span-4">
                    <label class="text-sm">Presentación</label>
                    <input class="w-full border rounded p-2" name="presentacion"
                        value="{{ old('presentacion', $presentation->presentacion) }}" required>
                </div>
                <div class="col-span-12 md:col-span-4">
                    <label class="text-sm">Presentación comercial</label>
                    <input class="w-full border rounded p-2" name="marca"
                        value="{{ old('marca', $presentation->marca) }}">
                </div>

                <div class="col-span-6 md:col-span-2">
                    <label class="text-sm">Contenido (valor)</label>
                    <input class="w-full border rounded p-2" type="number" step="0.01" min="0"
                        name="contenido_valor" value="{{ old('contenido_valor', $presentation->contenido_valor) }}"
                        required>
                </div>

                <div class="col-span-6 md:col-span-2">
                    <label class="text-sm">Unidad</label>
                    <select class="w-full border rounded p-2" name="contenido_unidad">
                        @foreach (['mg', 'g', 'ml', 'UI', 'smg'] as $u)
                            <option value="{{ $u }}" @selected(old('contenido_unidad', $presentation->contenido_unidad) === $u)>
                                {{ $u }}
                            </option>
                        @endforeach
                    </select>
                </div>



                <!-- =========================
             COSTOS / COMPOSICIÓN
        ========================== -->
                <div class="col-span-12 md:col-span-3">
                    <label class="text-sm">Precio frasco (opcional)</label>
                    <input class="w-full border rounded p-2" type="number" step="0.01" min="0"
                        name="precio_frasco" value="{{ old('precio_frasco', $presentation->precio_frasco) }}">
                </div>

                <div class="col-span-12 md:col-span-3">
                    <label class="text-sm">Cantidad de medicamento (mg) (opcional)</label>
                    <input class="w-full border rounded p-2" type="number" step="0.01" min="0"
                        name="cantidad_medicamento"
                        value="{{ old('cantidad_medicamento', $presentation->cantidad_medicamento) }}">
                </div>

                <div class="col-span-12 md:col-span-3">
                    <label class="text-sm">Volumen del diluyente (mL) (opcional)</label>
                    <input class="w-full border rounded p-2" type="number" step="0.01" min="0"
                        name="volumen_diluyente"
                        value="{{ old('volumen_diluyente', $presentation->volumen_diluyente) }}">
                </div>

                <!-- =========================
             ESTABILIDAD (NUEVO)
        ========================== -->
                <div class="col-span-12 md:col-span-3">
                    <label class="text-sm">Estabilidad (horas) (opcional)</label>
                    <input class="w-full border rounded p-2" type="number" min="0" name="stability_hours"
                        value="{{ old('stability_hours', $presentation->stability_hours) }}" placeholder="Ej. 24">
                    <div class="text-xs text-gray-500 mt-1">
                        Tiempo máximo de uso recomendado.
                    </div>
                </div>

                <div class="col-span-6 md:col-span-2">
                    <label class="text-sm">Temp. mín (°C) (opcional)</label>
                    <input class="w-full border rounded p-2" type="number" min="0" name="temp_min_c"
                        value="{{ old('temp_min_c', $presentation->temp_min_c) }}" placeholder="Ej. 8">
                </div>

                <div class="col-span-6 md:col-span-2">
                    <label class="text-sm">Temp. máx (°C) (opcional)</label>
                    <input class="w-full border rounded p-2" type="number" min="0" name="temp_max_c"
                        value="{{ old('temp_max_c', $presentation->temp_max_c) }}" placeholder="Ej. 20">
                </div>

                <div class="col-span-12 md:col-span-5">
                    <label class="text-sm">Leyenda / Indicaciones (opcional)</label>
                    <textarea class="w-full border rounded p-2" rows="2" name="legend"
                        placeholder="Ej. Refrigerar. Proteger de la luz. No agitar.">{{ old('legend', $presentation->legend) }}</textarea>
                </div>

                <!-- =========================
             LOTE / CADUCIDAD (VIGENTE)
        ========================== -->
                <div class="col-span-12 md:col-span-3">
                    <label class="text-sm">Lote vigente</label>
                    <input class="w-full border rounded p-2" name="batch[lote]"
                        value="{{ old('batch.lote', $currentBatch->lote ?? '') }}" required>
                </div>

                <div class="col-span-12 md:col-span-3">
                    <label class="text-sm">Caducidad vigente</label>
                    <input class="w-full border rounded p-2" type="date" name="batch[caducidad]"
                        value="{{ old(
                            'batch.caducidad',
                            isset($currentBatch->caducidad) ? \Carbon\Carbon::parse($currentBatch->caducidad)->format('Y-m-d') : '',
                        ) }}"
                        required>
                </div>

                <!-- =========================
             DISPONIBILIDAD
        ========================== -->
                <div class="col-span-12 md:col-span-3">
                    <label class="text-sm">Disponible</label>
                    <select class="w-full border rounded p-2" name="is_available">
                        <option value="1" @selected(old('is_available', $presentation->is_available) == 1)>Sí</option>
                        <option value="0" @selected(old('is_available', $presentation->is_available) == 0)>No</option>
                    </select>
                </div>

            </div>
        </div>

        <div class="text-right">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded">
                Actualizar presentación
            </button>
        </div>
    </form>
</x-admin-layout>
