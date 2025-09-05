<div>
    @if ($mostrarModalInspeccion)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
            <div class="bg-white rounded-lg shadow-lg w-full max-w-6xl p-6">

                <h2 class="text-2xl font-semibold mb-6 text-center">Inspección de Mezcla</h2>

                <div class="flex justify-end mb-4">
                    <button onclick="toggleCheckboxes()"
                        class="px-4 py-2 bg-indigo-600 text-white text-sm rounded hover:bg-indigo-700 transition">
                        Marcar/Desmarcar todos
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    {{-- Columna izquierda --}}
                    <div>
                        <h3 class="text-lg font-semibold mb-4 text-gray-800">Verificación general</h3>

                        <div class="space-y-3">
                            <div>
                                <label for="tipo_contenedor" class="block text-sm font-medium text-gray-700">Tipo de
                                    contenedor</label>
                                <select wire:model.defer="tipo_contenedor" id="tipo_contenedor"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200">
                                    <option value="">Seleccione...</option>
                                    <option value="Frasco">Frasco</option>
                                    <option value="Bolsa">Bolsa</option>
                                    <option value="Jeringa">Jeringa</option>
                                    <option value="Otro">Infusor</option>
                                </select>
                            </div>

                            <h3 class="text-lg font-semibold mb-4 text-gray-800">Inspección del contenido</h3>

                            @foreach ([
        'es_limpia' => '¿Es limpia?',
        'es_libre' => '¿Es libre?',
        'esta_rotulado' => '¿Está rotulado?',
        'numero_lote' => '¿Número de lote visible?',
        'medicamento' => '¿Incluye medicamento correcto?',
        'dosis_volumen_total' => '¿Dosis y volumen total correctos?',
        'volumen_medicamento' => '¿Volumen de medicamento correcto?',
        'rubrica_preparador' => '¿Tiene rúbrica del preparador?',
        'sello_seguridad' => '¿Tiene sello de seguridad?',
        'presenta_grietas' => '¿Presenta grietas?',
        'presenta_fugas' => '¿Presenta fugas?',
        'esta_roto' => '¿Está roto?',
    ] as $field => $label)
                                <label class="inline-flex items-center">
                                    <input type="checkbox" class="check-inspeccion" wire:model="{{ $field }}">
                                    <span class="ml-2 text-sm text-gray-700">{{ $label }}</span>
                                </label>
                            @endforeach

                            @foreach ([
        'coloracion_apropiada' => '¿Coloración apropiada?',
        'contenido_homogeneo' => '¿Contenido homogéneo?',
        'presenta_particulas' => '¿Presenta partículas?',
        'presenta_turbidez' => '¿Presenta turbidez?',
        'volumen_correcto' => '¿Volumen correcto?',
        'aprueba_contenido' => '¿Aprueba contenido?',
        'aprueba_contenedor' => '¿Aprueba contenedor?',
        'mezcla_aprobada' => '¿Mezcla aprobada?',
    ] as $field => $label)
                                <label class="inline-flex items-center">
                                    <input type="checkbox" class="check-inspeccion" wire:model="{{ $field }}">
                                    <span class="ml-2 text-sm text-gray-700">{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- Columna derecha --}}
                    <div>
                        <div class="space-y-3">
                            <div>
                                <label for="dosis_volumen" class="block text-sm font-medium text-gray-700">Dosis Volumen
                                    (ml)</label>
                                <input type="number" step="0.01" wire:model.defer="dosis_volumen" id="dosis_volumen"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200" />
                            </div>

                            <div>
                                <label for="peso_mezcla" class="block text-sm font-medium text-gray-700">Peso de la
                                    Mezcla (g)</label>
                                <input type="number" step="0.01" wire:model.defer="peso_mezcla" id="peso_mezcla"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200" />
                            </div>

                            <div>
                                <label for="observaciones"
                                    class="block text-sm font-medium text-gray-700">Observaciones</label>
                                <textarea wire:model.defer="observaciones" id="observaciones" rows="3"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200"></textarea>
                            </div>

                            <div>
                                <label for="reviso_nombre" class="block text-sm font-medium text-gray-700">Nombre de
                                    quien revisó</label>
                                <input type="text" wire:model.defer="reviso_nombre" id="reviso_nombre"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200" />
                            </div>

                            <div>
                                <label for="aprobo_nombre" class="block text-sm font-medium text-gray-700">Nombre de
                                    quien aprobó</label>
                                <input type="text" wire:model.defer="aprobo_nombre" id="aprobo_nombre"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end mt-6">
                    <button wire:click="$set('mostrarModalInspeccion', false)"
                        class="px-4 py-2 bg-gray-300 text-gray-800 rounded mr-2">Cancelar</button>

                    <button wire:click="guardarInspeccion"
                        class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Guardar</button>
                </div>
            </div>
        </div>
    @endif

    @push('js')
        <script>
            function toggleCheckboxes() {
                const checkboxes = document.querySelectorAll('.check-inspeccion');
                const allChecked = Array.from(checkboxes).every(cb => cb.checked);
                checkboxes.forEach(cb => {
                    cb.checked = !allChecked;
                    cb.dispatchEvent(new Event('change', {
                        bubbles: true
                    })); // ✅ cambio importante
                });
            }


            window.addEventListener('mezcla-inspeccionada', () => {
                Swal.fire({
                    icon: 'success',
                    title: '¡Mezcla inspeccionada correctamente!',
                    showConfirmButton: false,
                    timer: 2000
                }).then(() => {
                    location.reload();
                });
            });
        </script>
    @endpush
</div>
