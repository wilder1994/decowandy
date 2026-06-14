<x-guest-layout>
    <div class="mx-auto max-w-md">
        <x-dw-page-header title="Confirma tu contraseña" subtitle="Antes de continuar, valida tu contraseña para mantener tu cuenta segura." />

        <form method="POST" action="{{ route('password.confirm') }}" class="space-y-4">
            @csrf

            <div>
                <x-dw-input id="password" name="password" type="password" label="Contraseña" required autocomplete="current-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-1" />
            </div>

            <x-dw-button type="submit" class="w-full">Confirmar</x-dw-button>
        </form>
    </div>
</x-guest-layout>
