<x-admin-layout>
  <h1 class="text-2xl font-semibold mb-4">
    Presentaciones de: {{ $catalog->denominacion }} (#{{ $catalog->id }})
  </h1>

  <form action="{{ route('admin.oncologicos.medicines.catalog.presentations.store', $catalog) }}"
        method="POST"
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

    <div id="rows" class="space-y-4"></div>

    <button type="button" id="addRow"
            class="px-4 py-2 bg-green-600 text-white rounded">
      + Agregar presentación
    </button>

    <div class="text-right">
      <button type="submit"
              class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded">
        Guardar presentaciones
      </button>
    </div>
  </form>

  <template id="row-tpl">
    <div class="p-4 border rounded-lg grid grid-cols-12 gap-3">

      <div class="col-span-3">
        <label class="text-sm">Presentación</label>
        <input class="w-full border rounded p-2"
               name="presentations[__i__][presentacion]"
               placeholder="Frasco 500 mg"
               required>
      </div>

      <div class="col-span-2">
        <label class="text-sm">Contenido (valor)</label>
        <input class="w-full border rounded p-2"
               type="number"
               step="0.01"
               min="0"
               name="presentations[__i__][contenido_valor]"
               required>
      </div>

      <div class="col-span-2">
        <label class="text-sm">Unidad</label>
        <select class="w-full border rounded p-2"
                name="presentations[__i__][contenido_unidad]">
          <option>mg</option>
          <option>g</option>
          <option>ml</option>
          <option>UI</option>
        </select>
      </div>

      <div class="col-span-2">
        <label class="text-sm">Marca (opcional)</label>
        <input class="w-full border rounded p-2"
               name="presentations[__i__][marca]">
      </div>

      <div class="col-span-2">
        <label class="text-sm">Precio frasco</label>
        <input class="w-full border rounded p-2"
               type="number"
               step="0.01"
               min="0"
               name="presentations[__i__][precio_frasco]">
      </div>

      <!-- ⭐ NUEVO: Cantidad de medicamento (mg) -->
      <div class="col-span-3">
        <label class="text-sm">Cantidad de medicamento (mg)</label>
        <input class="w-full border rounded p-2"
               type="number"
               step="0.01"
               min="0"
               name="presentations[__i__][cantidad_medicamento]"
               placeholder="Ej. 500">
      </div>

      <!-- ⭐ NUEVO: Volumen del diluyente (mL) -->
      <div class="col-span-3">
        <label class="text-sm">Volumen del diluyente (mL)</label>
        <input class="w-full border rounded p-2"
               type="number"
               step="0.01"
               min="0"
               name="presentations[__i__][volumen_diluyente]"
               placeholder="Ej. 10">
      </div>

      <div class="col-span-1 flex items-end">
        <button type="button"
                class="rm-row px-3 py-2 bg-red-500 text-white rounded">
          X
        </button>
      </div>

      <div class="col-span-3">
        <label class="text-sm">Lote vigente</label>
        <input class="w-full border rounded p-2"
               name="presentations[__i__][batch][lote]"
               placeholder="ABC123"
               required>
      </div>

      <div class="col-span-3">
        <label class="text-sm">Caducidad vigente</label>
        <input class="w-full border rounded p-2"
               type="date"
               name="presentations[__i__][batch][caducidad]"
               required>
      </div>

      <input type="hidden"
             name="presentations[__i__][batch][is_current]"
             value="1">

      <div class="col-span-3">
        <label class="text-sm">Disponible</label>
        <select class="w-full border rounded p-2"
                name="presentations[__i__][is_available]">
          <option value="1" selected>Sí</option>
          <option value="0">No</option>
        </select>
      </div>

    </div>
  </template>

  <script>
    const rows = document.getElementById('rows');
    const tpl  = document.getElementById('row-tpl').innerHTML;
    const add  = document.getElementById('addRow');
    let i = 0;

    const addRow = () => {
      rows.insertAdjacentHTML('beforeend', tpl.replaceAll('__i__', i++));
    };

    add.addEventListener('click', addRow);

    rows.addEventListener('click', e => {
      if (e.target.classList.contains('rm-row')) {
        e.target.closest('.p-4').remove();
      }
    });

    // primera fila inicial
    addRow();
  </script>
</x-admin-layout>
