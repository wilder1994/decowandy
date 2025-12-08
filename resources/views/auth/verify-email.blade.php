<x-guest-layout>
    <div class="max-w-xl mx-auto">
        <div class="mb-6">
            <p class="text-xs uppercase tracking-[0.25em] text-emerald-700 font-semibold">Verificación</p>
            <h1 class="text-2xl font-bold text-slate-900">Confirma tu correo</h1>
            <p class="text-sm text-slate-600 mt-1">Hemos enviado un enlace de verificación. Haz clic para activar tu cuenta.</p>
        </div>

        @if (session('status') == 'verification-link-sent')
            <div class="mb-4 text-sm font-semibold text-emerald-700">
                Hemos reenviado un enlace de verificación a tu correo.
            </div>
        @endif

        <div class="mt-4 flex flex-col sm:flex-row items-center justify-between gap-3">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" class="rounded-xl bg-emerald-700 px-4 py-2.5 text-white font-semibold shadow-md shadow-emerald-700/20 hover:bg-emerald-800 focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
                    Reenviar correo de verificación
                </button>
            </form>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-sm font-semibold text-slate-600 hover:text-slate-800">
                    Cerrar sesión
                </button>
            </form>
        </div>
    </div>
</x-guest-layout>
