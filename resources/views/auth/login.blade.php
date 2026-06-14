<x-guest-layout>
    <div class="mx-auto max-w-md">
        <x-dw-page-header title="Inicia sesión" subtitle="Administra ventas, clientes e inventario desde un solo panel." />

        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf

            <div>
                <x-dw-input id="email" name="email" type="email" label="Correo electrónico" :value="old('email')" required autofocus autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-1" />
            </div>

            <div>
                <x-dw-input id="password" name="password" type="password" label="Contraseña" required autocomplete="current-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-1" />
            </div>

            <div class="flex items-center justify-between pt-1">
                <label for="remember_me" class="inline-flex items-center gap-2 text-sm text-dw-muted">
                    <input id="remember_me" type="checkbox" name="remember" class="rounded border-dw-border text-dw-primary focus:ring-dw-lilac/40">
                    <span>Recordarme</span>
                </label>
                @if (Route::has('password.request'))
                    <a class="text-sm font-semibold text-dw-primary hover:text-dw-primary-dark" href="{{ route('password.request') }}">
                        ¿Olvidaste tu contraseña?
                    </a>
                @endif
            </div>

            <x-dw-button type="submit" class="w-full">Ingresar</x-dw-button>

            @if (Route::has('register'))
                <p class="text-center text-sm text-dw-muted">
                    ¿No tienes cuenta?
                    <a href="{{ route('register') }}" class="font-semibold text-dw-primary hover:text-dw-primary-dark">Crear cuenta</a>
                </p>
            @endif
        </form>
    </div>
</x-guest-layout>
