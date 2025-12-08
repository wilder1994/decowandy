<x-guest-layout>
    <div class="max-w-xl mx-auto">
        <div class="mb-6">
            <p class="text-xs uppercase tracking-[0.25em] text-emerald-700 font-semibold">Acceso</p>
            <h1 class="text-2xl font-bold text-slate-900">Inicia sesión en DecoWandy</h1>
            <p class="text-sm text-slate-600 mt-1">Administra ventas, clientes e inventario desde un solo panel.</p>
        </div>

        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf

            <div>
                <label for="email" class="block text-sm font-semibold text-slate-800">Correo electrónico</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                       class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-emerald-600 focus:ring-emerald-500/30">
                <x-input-error :messages="$errors->get('email')" class="mt-1" />
            </div>

            <div>
                <label for="password" class="block text-sm font-semibold text-slate-800">Contraseña</label>
                <input id="password" name="password" type="password" required autocomplete="current-password"
                       class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-emerald-600 focus:ring-emerald-500/30">
                <x-input-error :messages="$errors->get('password')" class="mt-1" />
            </div>

            <div class="flex items-center justify-between pt-2">
                <label for="remember_me" class="inline-flex items-center gap-2 text-sm text-slate-700">
                    <input id="remember_me" type="checkbox" name="remember" class="rounded border-slate-300 text-emerald-700 focus:ring-emerald-500/50">
                    <span>Recordarme</span>
                </label>
                @if (Route::has('password.request'))
                    <a class="text-sm font-semibold text-emerald-700 hover:text-emerald-800" href="{{ route('password.request') }}">
                        ¿Olvidaste tu contraseña?
                    </a>
                @endif
            </div>

            <div class="pt-2">
                <button type="submit" class="w-full rounded-xl bg-emerald-700 px-4 py-2.5 text-white font-semibold shadow-md shadow-emerald-700/20 hover:bg-emerald-800 focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
                    Ingresar
                </button>
            </div>

            @if (Route::has('register'))
                <p class="text-sm text-center text-slate-600">
                    ¿No tienes cuenta?
                    <a href="{{ route('register') }}" class="font-semibold text-emerald-700 hover:text-emerald-800">Crear cuenta</a>
                </p>
            @endif
        </form>
    </div>
</x-guest-layout>
