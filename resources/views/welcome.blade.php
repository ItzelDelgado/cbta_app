<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>
    {{-- Font Awesome --}}
    <script src="https://kit.fontawesome.com/b023f039d3.js" crossorigin="anonymous"></script>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Styles -->
    @livewireStyles
</head>

<body class="font-sans antialiased">
    <div class="flex justify-evenly items-center bg-azul-prodifem text-white py-4">
        <div class="w-[300px]">
            <img src="{{ asset('img/Logo_Prodifem.png') }}" alt="">
        </div>
        <p class="text-center font-semibold text-2xl">Para cualquier pregunta, contáctenos.</p>
        <p class="text-center font-semibold text-2xl"><i class="fa-solid fa-phone" style="color: #ffffff;"></i> 5513535412 <br> <i class="fa-brands fa-whatsapp"></i> 5513535412 <br> <i class="fa-solid fa-envelope"></i>
            mezclasp@gmail.com</p>
    </div>
    <div class="max-w-[600px] mx-auto text-center mt-36">
        <h1 class="font-bold text-2xl mb-8 mt-8">¡Bienvenido al portal de solicitud de mezclas estériles de PRODIFEM!
        </h1>
        <p class="font-semibold text-xl mb-8">¿Qué tipo de mezcla necesitas?</p>
        <p class="text-lg mb-8">Elija una opción entre mezclas de nutrición parenteral y mezclas oncológicas</p>
        <div class="flex flex-col max-w-96 mx-auto gap-8">
            <button class="bg-azul-prodifem p-4 rounded-xl text-2xl"><a class="text-white" href="{{ route('admin.solicitudes.index') }}">Mezclas de
                    nutrición
                    parenteral</a></button>
            <button class="bg-azul-prodifem p-4 rounded-xl text-2xl"><a class="text-white" href="">Mezclas
                    oncológicas</a></button>
        </div>
    </div>

    @stack('modals')

    @livewireScripts
</body>

</html>
