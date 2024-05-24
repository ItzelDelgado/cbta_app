<x-admin-layout>

    <!-- CSS de DataTables -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.6/css/jquery.dataTables.css">

    <!-- jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <!-- JavaScript de DataTables -->
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.6/js/jquery.dataTables.js"></script>

    <div class="mt-2">
        <h1 class="text-2xl font-medium text-gray-800">Lista de Solicitudes</h1>
    </div>

    <div class="flex justify-end mb-4">
        <a class="text-white bg-azul-prodifem hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-sm px-5 py-2.5 text-center me-2 mb-2 dark:bg-blue-600 dark:hover:bg-azul-prodifem dark:focus:ring-blue-800"
            href="{{ route('admin.solicitudes.create') }}"><i class="fa-solid fa-plus pr-1"></i> Agregar</a>
    </div>


    @livewire('solicituds')



    <script>
        $(document).ready(function() {
            $('#miTabla').DataTable();
        });
    </script>

</x-admin-layout>
