<x-guest-layout>
    <div class="mx-auto max-w-md">
        <x-dw-page-header title="Restablecer contraseña" subtitle="Ingresa tu correo y te enviaremos un enlace para crear una nueva contraseña." />

        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
            @csrf

            <div>
                <x-dw-input id="email" name="email" type="email" label="Correo electrónico" :value="old('email')" required autofocus />
                <x-input-error :messages="$errors->get('email')" class="mt-1" />
            </div>

            <x-dw-button type="submit" class="w-full">Enviar enlace de restablecimiento</x-dw-button>
        </form>
    </div>
</x-guest-layout>
