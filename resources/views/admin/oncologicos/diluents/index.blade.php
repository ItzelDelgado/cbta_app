<x-admin-layout>
    <div class="mt-2">
        <div class="flex items-center justify-between mb-4">
            <h1 class="text-2xl font-semibold text-gray-800">Diluyentes</h1>
            <a href="{{ route('admin.oncologicos.diluents.create') }}"
                class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                + Nuevo diluyente
            </a>
        </div>

        @if (session('success'))
            <div class="mb-4 p-3 rounded bg-green-100 text-green-800">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white rounded shadow overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-100 text-gray-600">
                    <tr>
                        <th class="px-4 py-3 text-left">Denominación genérica</th>
                        <th class="px-4 py-3 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse ($diluents as $d)
                        <tr>
                            <td class="px-4 py-3">{{ $d->denominacion_generica }}</td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('admin.oncologicos.diluent_presentations.index', $d) }}"
                                    class="px-3 py-1 bg-indigo-600 text-white rounded hover:bg-indigo-700">
                                    Presentaciones
                                </a>

                                <a href="{{ route('admin.oncologicos.diluents.edit', $d) }}"
                                    class="px-3 py-1 bg-amber-500 text-white rounded hover:bg-amber-600">
                                    Editar
                                </a>

                                <form action="{{ route('admin.oncologicos.diluents.destroy', $d) }}" method="POST"
                                    class="inline-block" onsubmit="return confirm('¿Eliminar este diluyente?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700">
                                        Eliminar
                                    </button>
                                </form>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td class="px-4 py-6 text-center text-gray-500" colspan="2">
                                No hay diluyentes.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $diluents->links() }}
        </div>
    </div>
</x-admin-layout>
