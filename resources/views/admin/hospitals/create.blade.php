<x-admin-layout>
    <div class="mt-2 mb-4">
        <h1 class="text-2xl font-medium text-gray-800">Agregar nuevo hospital</h1>
    </div>

    <form action="{{ route('admin.hospitals.store') }}" method="POST" class="bg-white rounded-lg p-6 shadow-lg">
        @csrf

        <x-validation-errors class="mb-4" />

        <div class="mb-4">
            <x-label class="mb-2">
                Nombre
            </x-label>
            <x-input value="{{ old('name_hp') }}" name="name_hp" class="w-full"
                placeholder="Escriba el nombre del hospital" />
        </div>

        <div class="mb-4">
            <x-label class="mb-2">
                Dirección
            </x-label>
            <x-input value="{{ old('adress') }}" name="adress" class="w-full"
                placeholder="Tlacotalpan 59, Col. Roma Sur , Cuauhtemoc, CDMX, 06760" />
        </div>

        {{-- ASIGNAR CLIENTES (buscador + dual list) --}}
        <div class="mb-4" x-data="clientesPicker(@js($clientes), @js(old('clientes', [])))">
            <x-label class="mb-2">
                Clientes asociados
            </x-label>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                {{-- Disponibles --}}
                <div class="border rounded-lg p-3">
                    <div class="mb-2">
                        <x-input x-model="search" class="w-full" placeholder="Buscar cliente por nombre o apellido..." />
                    </div>

                    <div class="h-64 overflow-auto divide-y">
                        <template x-for="c in filteredDisponibles()" :key="c.id">
                            <div class="flex items-center justify-between py-2">
                                <div class="text-gray-800">
                                    <span class="font-medium" x-text="c.nombre"></span>
                                    <span x-text="c.apellido"></span>
                                </div>

                                <button type="button"
                                    class="text-white bg-azul-prodifem hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-sm px-4 py-2"
                                    @click="add(c)">
                                    <i class="fa-solid fa-arrow-right"></i>
                                </button>
                            </div>
                        </template>

                        <div x-show="filteredDisponibles().length === 0" class="py-6 text-center text-gray-500">
                            No hay clientes con ese filtro.
                        </div>
                    </div>
                </div>

                {{-- Seleccionados --}}
                <div class="border rounded-lg p-3">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-sm text-gray-600">
                            Seleccionados: <span class="font-semibold" x-text="selected.length"></span>
                        </p>

                        <button type="button"
                            class="text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-4 focus:ring-red-300 font-medium rounded-full text-sm px-4 py-2"
                            x-show="selected.length > 0"
                            @click="clearAll()">
                            Limpiar
                        </button>
                    </div>

                    <div class="h-64 overflow-auto divide-y">
                        <template x-for="c in selected" :key="c.id">
                            <div class="flex items-center justify-between py-2">
                                <div class="text-gray-800">
                                    <span class="font-medium" x-text="c.nombre"></span>
                                    <span x-text="c.apellido"></span>
                                </div>

                                <button type="button"
                                    class="text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-4 focus:ring-red-300 font-medium rounded-full text-sm px-4 py-2"
                                    @click="remove(c)">
                                    <i class="fa-solid fa-xmark"></i>
                                </button>
                            </div>
                        </template>

                        <div x-show="selected.length === 0" class="py-6 text-center text-gray-500">
                            Aún no has agregado clientes.
                        </div>
                    </div>

                    {{-- inputs hidden que se envían como clientes[] --}}
                    <template x-for="id in selectedIds()" :key="'hid_'+id">
                        <input type="hidden" name="clientes[]" :value="id">
                    </template>
                </div>

            </div>

            <p class="text-sm text-gray-500 mt-2">
                Tip: escribe en el buscador y usa la flecha para agregar. Puedes quitar con la ✕.
            </p>
        </div>

        <div class="flex justify-end">
            <x-button>
                Crear hospital
            </x-button>
        </div>
    </form>

    <script>
        function clientesPicker(clientes, oldSelectedIds) {
            const oldIds = (oldSelectedIds || []).map(v => Number(v));

            const byId = new Map((clientes || []).map(c => [Number(c.id), {
                id: Number(c.id),
                nombre: c.nombre ?? '',
                apellido: c.apellido ?? ''
            }]));

            const selectedInitial = oldIds
                .map(id => byId.get(id))
                .filter(Boolean);

            return {
                search: '',
                all: Array.from(byId.values()),
                selected: selectedInitial,

                selectedIds() {
                    return this.selected.map(s => s.id);
                },

                disponibles() {
                    const sel = new Set(this.selectedIds());
                    return this.all.filter(c => !sel.has(c.id));
                },

                filteredDisponibles() {
                    const q = (this.search || '').trim().toLowerCase();
                    const list = this.disponibles();

                    if (!q) return list;

                    return list.filter(c => {
                        const full = `${c.nombre} ${c.apellido}`.toLowerCase();
                        return full.includes(q);
                    });
                },

                add(c) {
                    if (this.selectedIds().includes(c.id)) return;
                    this.selected.push(c);
                    this.search = '';
                },

                remove(c) {
                    this.selected = this.selected.filter(x => x.id !== c.id);
                },

                clearAll() {
                    this.selected = [];
                }
            }
        }
    </script>
</x-admin-layout>
