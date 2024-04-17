<x-guest-layout>
    <div class="flex justify-evenly items-center bg-azul-prodifem text-white py-4">
        <div class="w-[300px]">
            <img src="{{ asset('img/Logo_Prodifem.png') }}" alt="">
        </div>
        <p class="text-center font-semibold text-2xl">Para cualquier pregunta, contáctenos.</p>
        <p class="text-center font-semibold text-2xl"><i class="fa-solid fa-phone" style="color: #ffffff;"></i> 5513535412 <br> <i class="fa-brands fa-whatsapp"></i> 5513535412 <br> <i class="fa-solid fa-envelope"></i>
            mezclasp@gmail.com</p>
    </div>
    <h1 class="font-bold text-3xl text-center pt-20">Acceso al portal para solicitar <br> mezclas de nutrición parenteral</h1>
    <x-authentication-card>
        <x-slot name="logo">

        </x-slot>

        <x-validation-errors class="mb-4" />

        @if (session('status'))
            <div class="mb-4 font-medium text-sm text-green-600">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div>
                <x-label for="username" class="text-white" value="{{ __('Usuario') }}" />
                <x-input id="username" class="block mt-1 w-full" type="text" name="username" :value="old('username')"
                    required autofocus autocomplete="username" />
            </div>

            <div class="mt-4">
                <x-label for="password" class="text-white" value="{{ __('Contraseña') }}" />
                <x-input id="password" class="block mt-1 w-full" type="password" name="password" required
                    autocomplete="current-password" />
            </div>

            <div class="block mt-4">
                <label for="remember_me" class="flex items-cente">
                    <x-checkbox id="remember_me" name="remember" />
                    <span class="ms-2 text-sm text-white">{{ __('Recordar Usuario') }}</span>
                </label>
            </div>

            <div class="flex items-center justify-end mt-4">
                @if (Route::has('password.request'))
                    <a class="underline text-sm text-white hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                        href="{{ route('password.request') }}">
                        {{ __('¿Has olvidado tu contraseña?') }}
                    </a>
                @endif

                <x-button class="ms-4">
                    {{ __('Iniciar Sesión') }}
                </x-button>
            </div>
        </form>
    </x-authentication-card>
</x-guest-layout>
