{{-- resources/views/layouts/public.blade.php --}}
{{-- Layout público para DecoWandy
     - Código/IDs/clases en inglés
     - Textos visibles y comentarios en español
--}}
<!DOCTYPE html>
<html lang="es" class="h-full bg-[#F8F8F8]">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'DecoWandy')</title>
    <meta name="description" content="@yield('meta_description', 'Diseñar es crear, aprender es crecer')">
    @vite(['resources/css/app.css','resources/js/app.js'])
    {{-- Tipografías sugeridas (opcional): Poppins/Inter --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Poppins:wght@500;700&display=swap" rel="stylesheet">
    <style>
        :root{
            --dw-primary:#C4A1E0;  /* Lila gris sutil (principal) */
            --dw-accent:#A98AD4;   /* Morado suave para acentos/hover */
            --dw-lilac:#EDE7F6;    /* Lila muy claro para fondos suaves */
            --dw-lilac-2:#BCA5EA;  /* Morado medio para bordes/detalles */
            --dw-yellow:#F7D87B;   /* Amarillo pastel (acentos pequeños) */
            --dw-bg:#F8F8F8;       /* Fondo base */
            --dw-text:#333333;     /* Texto principal */
        }
        body{ font-family: 'Inter', system-ui, -apple-system, Segoe UI, Roboto, 'Helvetica Neue', Arial, 'Noto Sans'; }
        .brand-gradient{ background: linear-gradient(135deg, var(--dw-primary), var(--dw-accent)); }
    </style>
</head>
<body class="min-h-full text-[color:var(--dw-text)]">
    {{-- NAVBAR --}}
    <header class="sticky top-0 z-40 bg-white/90 backdrop-blur border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 py-3 flex items-center justify-between">
            <a href="{{ url('/') }}" class="flex items-center gap-3">
                <img src="{{ asset('images/logo-decowandy.png') }}" alt="DecoWandy" class="h-10 w-auto">

                <span class="sr-only">DecoWandy</span>
            </a>
            <nav class="hidden md:flex items-center gap-6 font-semibold">
                <a href="{{ route('catalog.category', 'papeleria') }}" class="hover:text-[color:var(--dw-accent)] transition">Papelería</a>
                <a href="{{ route('catalog.category', 'impresion') }}" class="hover:text-[color:var(--dw-accent)] transition">Impresión</a>
                <a href="{{ route('catalog.category', 'diseno') }}" class="hover:text-[color:var(--dw-accent)] transition">Diseño</a>
                <a href="#contacto" class="hover:text-[color:var(--dw-accent)] transition">Contacto</a>
            </nav>
            <div class="flex items-center gap-3">
                <a href="{{ route('login') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-2xl border border-[color:var(--dw-lilac-2)] text-[color:var(--dw-accent)] hover:bg-[color:var(--dw-lilac)] transition">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M15 3h4a2 2 0 012 2v14a2 2 0 01-2 2h-4"/><path d="M10 17l5-5-5-5"/><path d="M15 12H3" stroke-linecap="round"/></svg>
                    Ingresar
                </a>
                {{-- CTA a WhatsApp (reemplaza TU_NUMERO con el real, solo dígitos con código país, ej: 57xxxxxxxxxx) --}}
                <a href="https://wa.me/TU_NUMERO?text=Hola%20DecoWandy,%20quiero%20realizar%20un%20pedido%20🙂"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-2xl text-white brand-gradient shadow hover:opacity-90 transition"
                   target="_blank" rel="noopener">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor"><path d="M20.52 3.48A11.94 11.94 0 0 0 12.06.05C5.74.05.59 5.2.59 11.52c0 2.03.53 4.03 1.54 5.79L.05 24l6.86-2c1.7.92 3.62 1.41 5.58 1.41h.01c6.32 0 11.47-5.15 11.47-11.47 0-3.06-1.19-5.93-3.45-8.19ZM12.5 21.04h-.01a9.5 9.5 0 0 1-4.86-1.32l-.35-.2-4.07 1.19 1.2-3.97-.23-.36a9.5 9.5 0 1 1 8.31 4.66Zm5.44-7.13c-.3-.15-1.78-.88-2.05-.98s-.47-.15-.67.15-.77.98-.95 1.18-.35.22-.64.07c-.3-.15-1.25-.46-2.38-1.47a9.06 9.06 0 0 1-1.67-2.07c-.18-.3 0-.46.14-.61.14-.14.3-.37.45-.55.15-.18.2-.3.3-.5.1-.2.05-.37-.03-.52-.08-.15-.67-1.6-.92-2.2-.24-.58-.49-.5-.67-.5h-.57c-.2 0-.52.07-.79.37-.27.29-1.04 1.02-1.04 2.48s1.07 2.87 1.22 3.07c.15.2 2.1 3.21 5.09 4.49.71.31 1.27.5 1.7.64.71.22 1.36.19 1.87.12.57-.08 1.78-.73 2.03-1.43.25-.7.25-1.3.17-1.43-.08-.13-.27-.2-.56-.35Z"/></svg>
                    Pedir por WhatsApp
                </a>
            </div>
        </div>
    </header>

    {{-- CONTENIDO PRINCIPAL --}}
    <main>
        @yield('content')
    </main>

    {{-- FOOTER --}}
    <footer id="contacto" class="mt-16 bg-white border-t border-gray-100">
        <div class="max-w-7xl mx-auto px-4 py-10 grid gap-6 md:grid-cols-3">
            <div>
                <img src="{{ asset('images/logo-decowandy.png') }}" alt="DecoWandy" class="h-10 mb-3">
                <p class="text-sm text-gray-600">Diseñar es crear, aprender es crecer.</p>
            </div>
            <div>
                <h3 class="font-semibold mb-2">Contacto</h3>
                <p class="text-sm text-gray-600">Jamundí, Colombia</p>
                <p class="text-sm text-gray-600">Tel/WhatsApp: <span class="font-semibold">TU_NUMERO</span></p>
                <p class="text-sm text-gray-600">Email: contacto@decowandy.com</p>
            </div>
            <div>
                <h3 class="font-semibold mb-2">Enlaces</h3>
                <ul class="text-sm text-gray-600 space-y-1">
                    <li><a class="hover:text-[color:var(--dw-accent)]" href="#papeleria">Papelería</a></li>
                    <li><a class="hover:text-[color:var(--dw-accent)]" href="#impresion">Impresión</a></li>
                    <li><a class="hover:text-[color:var(--dw-accent)]" href="#diseno">Diseño</a></li>
                </ul>
            </div>
        </div>
        <div class="text-center text-xs text-gray-500 py-4 border-t">© {{ date('Y') }} DecoWandy. Todos los derechos reservados.</div>
    </footer>
</body>
</html>
