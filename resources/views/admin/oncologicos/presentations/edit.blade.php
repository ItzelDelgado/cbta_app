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

  <form
    action="{{ route('admin.oncologicos.medicines.catalog.presentations.update', [$catalog, $presentation]) }}"
    method="POST"
    class="space-y-6"
  >
    @csrf
    @method('PUT')

    <div class="p-4 border rounded-lg grid grid-cols-12 gap-3">

      <div class="col-span-3">
        <label class="text-sm">Presentación</label>
        <input
          class="w-full border rounded p-2"
          name="presentacion"
          value="{{ old('presentacion', $presentation->presentacion) }}"
          required
        >
      </div>

      <div class="col-span-2">
        <label class="text-sm">Contenido (valor)</label>
        <input
          class="w-full border rounded p-2"
          type="number" step="0.01" min="0"
          name="contenido_valor"
          value="{{ old('contenido_valor', $presentation->contenido_valor) }}"
          required
        >
      </div>

      <div class="col-span-2">
        <label class="text-sm">Unidad</label>
        <select
          class="w-full border rounded p-2"
          name="contenido_unidad"
        >
          @foreach (['mg','g','ml','UI'] as $u)
            <option value="{{ $u }}"
              @selected(old('contenido_unidad', $presentation->contenido_unidad) === $u)>
              {{ $u }}
            </option>
          @endforeach
        </select>
      </div>

      <div class="col-span-2">
        <label class="text-sm">Marca (opcional)</label>
        <input
          class="w-full border rounded p-2"
          name="marca"
          value="{{ old('marca', $presentation->marca) }}"
        >
      </div>

      <div class="col-span-2">
        <label class="text-sm">Precio frasco</label>
        <input
          class="w-full border rounded p-2"
          type="number" step="0.01" min="0"
          name="precio_frasco"
          value="{{ old('precio_frasco', $presentation->precio_frasco) }}"
        >
      </div>

      {{-- NUEVOS CAMPOS --}}
      <div class="col-span-3">
        <label class="text-sm">Cantidad de medicamento (mg)</label>
        <input
          class="w-full border rounded p-2"
          type="number" step="0.01" min="0"
          name="cantidad_medicamento"
          value="{{ old('cantidad_medicamento', $presentation->cantidad_medicamento) }}"
        >
      </div>

      <div class="col-span-3">
        <label class="text-sm">Volumen del diluyente (mL)</label>
        <input
          class="w-full border rounded p-2"
          type="number" step="0.01" min="0"
          name="volumen_diluyente"
          value="{{ old('volumen_diluyente', $presentation->volumen_diluyente) }}"
        >
      </div>

      <div class="col-span-3">
        <label class="text-sm">Lote vigente</label>
        <input
          class="w-full border rounded p-2"
          name="batch[lote]"
          value="{{ old('batch.lote', $currentBatch->lote ?? '') }}"
          required
        >
      </div>

      <div class="col-span-3">
        <label class="text-sm">Caducidad vigente</label>
        <input
          class="w-full border rounded p-2"
          type="date"
          name="batch[caducidad]"
          value="{{ old(
              'batch.caducidad',
              isset($currentBatch->caducidad)
                  ? \Carbon\Carbon::parse($currentBatch->caducidad)->format('Y-m-d')
                  : ''
          ) }}"
          required
        >
      </div>

      <div class="col-span-3">
        <label class="text-sm">Disponible</label>
        <select
          class="w-full border rounded p-2"
          name="is_available"
        >
          <option value="1" @selected(old('is_available', $presentation->is_available) == 1)>Sí</option>
          <option value="0" @selected(old('is_available', $presentation->is_available) == 0)>No</option>
        </select>
      </div>

    </div>

    <div class="text-right">
      <button
        type="submit"
        class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded"
      >
        Actualizar presentación
      </button>
    </div>
  </form>
</x-admin-layout>
