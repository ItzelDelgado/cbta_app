<x-app-layout>
    <div class="flex justify-evenly items-center bg-azul-prodifem text-white py-4">
        <div class="w-[300px]">
            <img src="{{ asset('img/Logo_Prodifem.png') }}" alt="">
        </div>
        <p class="text-center font-semibold text-2xl">Si tiene alguna duda no dude <br> en contactarnos</p>
        <p class="text-center font-semibold text-2xl">Tel: 55 13535412 <br> WatsApp: 55 13535412 <br> Correo:
            mezclasp@gmail.com</p>
    </div>
    <div class="max-w-[600px] mx-auto text-center mt-36">
        <h1 class="font-bold text-2xl mb-8 mt-8">¡Bienvenido al portal de solicitud de mezclas estériles de PRODIFEM!</h1>
        <p class="font-semibold text-xl mb-8">¿Qué tipo de mezcla necesitas?</p>
        <p class="text-lg mb-8">Elija una opción entre mezclas de nutrición parenteral y mezclas oncológicas</p>
        <div class="flex flex-col max-w-96 mx-auto gap-8">
            <button class="bg-azul-prodifem p-4 rounded-xl text-2xl"><a class="text-white" href="">Mezclas de nutrición
                    parenteral</a></button>
            <button class="bg-azul-prodifem p-4 rounded-xl text-2xl"><a class="text-white" href="">Mezclas
                    oncológicas</a></button>
        </div>
    </div>
</x-app-layout>
