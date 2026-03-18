<x-admin-layout>
    <div class="mt-2">
        <h1 class="text-2xl font-medium text-gray-800">Lista de Clientes</h1>
    </div>

    <div class="flex justify-end mb-4">
        <a class="text-white bg-azul-prodifem hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-sm px-5 py-2.5 text-center me-2 mb-2 dark:bg-blue-600 dark:hover:bg-azul-prodifem dark:focus:ring-blue-800"
            href="{{ route('admin.clientes.create') }}">
            <i class="fa-solid fa-plus pr-1"></i> Agregar
        </a>
    </div>

    <div class="relative overflow-x-auto">
        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-6 py-3">Id</th>
                    <th scope="col" class="px-6 py-3">Nombre</th>
                    <th scope="col" class="px-6 py-3">Razón social</th>
                    <th scope="col" class="px-6 py-3"></th>
                </tr>
            </thead>

            <tbody>
                @forelse ($clientes as $cliente)
                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                        <th scope="row"
                            class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            {{ $cliente->id }}
                        </th>

                        <td class="px-6 py-4">
                            {{ $cliente->nombre }}
                        </td>

                        <td class="px-6 py-4">
                            {{ $cliente->razon_social }}
                        </td>

                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <a class="text-white bg-azul-prodifem hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-sm px-5 py-2.5 text-center me-2 mb-2 dark:bg-blue-600 dark:hover:bg-azul-prodifem dark:focus:ring-blue-800"
                                    href="{{ route('admin.clientes.edit', $cliente) }}">
                                    <i class="fa-solid fa-pen pr-1"></i> Editar
                                </a>

                                <form action="{{ route('admin.clientes.destroy', $cliente) }}" method="POST"
                                    onsubmit="return confirm('¿Seguro que deseas eliminar este cliente?');">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                        class="text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-4 focus:ring-red-300 font-medium rounded-full text-sm px-5 py-2.5 text-center">
                                        <i class="fa-solid fa-trash pr-1"></i> Eliminar
                                    </button>
                                </form>
                                <a href="{{ route('admin.clientes.exportarMezclasOnco', $cliente) }}" target="_blank"
                                    class="text-white bg-green-600 hover:bg-green-800 focus:outline-none focus:ring-4 focus:ring-green-300 font-medium rounded-full text-sm px-5 py-2.5 text-center">
                                    <i class="fa-solid fa-file-excel pr-1"></i> Reporte
                                </a>

                            </div>
                        </td>
                    </tr>
                @empty
                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                        <td colspan="4" class="px-6 py-6 text-center text-gray-500">
                            No hay clientes registrados.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="mt-4">
            {{ $clientes->links() }}
        </div>
    </div>
</x-admin-layout>
