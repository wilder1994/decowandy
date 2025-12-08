<x-guest-layout>
    <div class="max-w-xl mx-auto">
        <div class="mb-6">
            <p class="text-xs uppercase tracking-[0.25em] text-emerald-700 font-semibold">Registro</p>
            <h1 class="text-2xl font-bold text-slate-900">Crear cuenta en DecoWandy</h1>
            <p class="text-sm text-slate-600 mt-1">Configura tu acceso para gestionar el panel administrativo.</p>
        </div>

        <form method="POST" action="{{ route('register') }}" class="space-y-4">
            @csrf

            <div>
                <label for="name" class="block text-sm font-semibold text-slate-800">Nombre completo</label>
                <input id="name" name="name" type="text" value="{{ old('name') }}" required autofocus autocomplete="name"
                       class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-emerald-600 focus:ring-emerald-500/30">
                <x-input-error :messages="$errors->get('name')" class="mt-1" />
            </div>

            <div>
                <label for="email" class="block text-sm font-semibold text-slate-800">Correo electrónico</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="username"
                       class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-emerald-600 focus:ring-emerald-500/30">
                <x-input-error :messages="$errors->get('email')" class="mt-1" />
            </div>

            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label for="password" class="block text-sm font-semibold text-slate-800">Contraseña</label>
                    <input id="password" name="password" type="password" required autocomplete="new-password"
                           class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-emerald-600 focus:ring-emerald-500/30">
                    <x-input-error :messages="$errors->get('password')" class="mt-1" />
                </div>
                <div>
                    <label for="password_confirmation" class="block text-sm font-semibold text-slate-800">Confirmar contraseña</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password"
                           class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-emerald-600 focus:ring-emerald-500/30">
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
                </div>
            </div>

            <div class="pt-2">
                <button type="submit" class="w-full rounded-xl bg-emerald-700 px-4 py-2.5 text-white font-semibold shadow-md shadow-emerald-700/20 hover:bg-emerald-800 focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
                    Crear cuenta
                </button>
            </div>

            <p class="text-sm text-center text-slate-600">
                ¿Ya tienes cuenta?
                <a href="{{ route('login') }}" class="font-semibold text-emerald-700 hover:text-emerald-800">Inicia sesión</a>
            </p>
        </form>
    </div>
</x-guest-layout>
