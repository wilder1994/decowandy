<x-guest-layout>
    <div class="mx-auto max-w-md">
        <x-dw-page-header title="Crear cuenta" subtitle="Configura tu acceso para gestionar el panel administrativo." />

        <form method="POST" action="{{ route('register') }}" class="space-y-4">
            @csrf

            <div>
                <x-dw-input id="name" name="name" type="text" label="Nombre completo" :value="old('name')" required autofocus autocomplete="name" />
                <x-input-error :messages="$errors->get('name')" class="mt-1" />
            </div>

            <div>
                <x-dw-input id="email" name="email" type="email" label="Correo electrónico" :value="old('email')" required autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-1" />
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <x-dw-input id="password" name="password" type="password" label="Contraseña" required autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password')" class="mt-1" />
                </div>
                <div>
                    <x-dw-input id="password_confirmation" name="password_confirmation" type="password" label="Confirmar contraseña" required autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
                </div>
            </div>

            <x-dw-button type="submit" class="w-full">Crear cuenta</x-dw-button>

            <p class="text-center text-sm text-dw-muted">
                ¿Ya tienes cuenta?
                <a href="{{ route('login') }}" class="font-semibold text-dw-primary hover:text-dw-primary-dark">Inicia sesión</a>
            </p>
        </form>
    </div>
</x-guest-layout>
