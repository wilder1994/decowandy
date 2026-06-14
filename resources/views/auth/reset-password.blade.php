<x-guest-layout>
    <div class="mx-auto max-w-md">
        <x-dw-page-header title="Nueva contraseña" subtitle="Completa los campos para restaurar el acceso." />

        <form method="POST" action="{{ route('password.store') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <div>
                <x-dw-input id="email" name="email" type="email" label="Correo electrónico" :value="old('email', $request->email)" required autofocus autocomplete="username" />
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

            <x-dw-button type="submit" class="w-full">Guardar contraseña</x-dw-button>
        </form>
    </div>
</x-guest-layout>
