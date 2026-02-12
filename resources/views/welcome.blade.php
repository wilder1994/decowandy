{{-- resources/views/welcome.blade.php --}}
@extends('layouts.public')

@section('title', 'DecoWandy — Diseñar es crear, aprender es crecer')

@section('content')
    {{-- HERO --}}
    <section class="relative overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 pt-12 pb-16 grid gap-10 md:grid-cols-2 items-center">
            <div>
                <h1 class="text-4xl md:text-5xl font-bold leading-tight" style="font-family:'Poppins',Inter,system-ui">
                    <a href="{{ route('catalog.category', 'diseno') }}" class="hover:text-[color:var(--dw-accent)] transition">Diseños</a>,
                    <a href="{{ route('catalog.category', 'papeleria') }}" class="hover:text-[color:var(--dw-accent)] transition">papelería</a> e
                    <span class="block">
                        <a href="{{ route('catalog.category', 'impresion') }}" class="hover:text-[color:var(--dw-accent)] transition">impresiones</a>
                    </span>
                </h1>

                <div class="mt-4">
                    <svg viewBox="0 0 800 100" class="w-full h-20">
                        <defs>
                            <linearGradient id="dwBrush" x1="0%" y1="0%" x2="100%" y2="0%">
                                <stop offset="0%"  stop-color="var(--dw-primary)" />
                                <stop offset="100%" stop-color="var(--dw-accent)" />
                            </linearGradient>
                        </defs>
                        <path d="M20,70 C120,20 220,90 320,60 C420,30 520,100 620,55 C700,30 760,65 780,60"
                              stroke="url(#dwBrush)" stroke-width="28" stroke-linecap="round" fill="none" opacity=".85"/>
                    </svg>
                </div>

                <p class="mt-4 text-gray-600">
                    Logos, tarjetas, papelería y servicios de impresión. Hecho con cariño por DecoWandy.
                </p>

                <div class="mt-5 flex flex-wrap gap-3 text-sm text-gray-700">
                    <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white shadow-sm border border-gray-100">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M6 6h12v12H6z"/><path d="M8 10h8M8 14h5"/></svg>
                        Fotocopias y escáner
                    </span>
                    <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white shadow-sm border border-gray-100">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M5 7h14v10H5z" stroke-linejoin="round"/><path d="M9 7V5h6v2M9 14h6M9 11h6"/></svg>
                        Impresión fotográfica
                    </span>
                    <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white shadow-sm border border-gray-100">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M4 17l5.5-9L13 13l3-5 4 9" stroke-linecap="round" stroke-linejoin="round"/><circle cx="11" cy="17" r="1"/><circle cx="17" cy="17" r="1"/></svg>
                        Detalles y regalos
                    </span>
                </div>

                <div class="mt-6 flex flex-wrap gap-3">
                    <a href="https://wa.me/{{ env('DW_WHATSAPP','57XXXXXXXXXX') }}?text={{ urlencode('Hola DecoWandy, quiero cotizar un diseño o impresión ✨') }}"
                       target="_blank" rel="noopener"
                       class="inline-flex items-center px-5 py-3 rounded-2xl text-white brand-gradient shadow hover:opacity-90 transition gap-2">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M12.04 2.01h-.08a10 10 0 00-8.9 14.5l-1 3.65a.75.75 0 00.92.92l3.58-.97a10 10 0 104.48-18.1zM18 16.57c-.2.57-.99 1.04-1.61 1.18-.43.1-.99.17-3.05-.65-2.56-1.06-4.2-3.66-4.33-3.83-.13-.17-1.03-1.37-1.03-2.61s.65-1.85.88-2.11c.23-.26.5-.32.67-.32.17 0 .34 0 .49.01.16.01.37-.06.58.45.22.53.75 1.84.82 1.98.07.14.11.3.02.47-.08.17-.12.28-.24.43-.12.14-.26.31-.37.42-.12.12-.25.24-.11.47.13.23.57.93 1.21 1.51.83.74 1.52.97 1.75 1.08.23.11.36.09.49-.05.13-.14.57-.66.72-.89.15-.23.3-.19.51-.11.21.08 1.34.63 1.57.74.23.12.38.17.44.26.06.09.06.52-.14 1.09z"/></svg>
                        Solicitar por WhatsApp
                    </a>
                    <a href="#catalogo"
                       class="inline-flex items-center px-5 py-3 rounded-2xl border border-[color:var(--dw-lilac-2)] text-[color:var(--dw-accent)] hover:bg-[color:var(--dw-lilac)] transition gap-2">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M4 7h16M4 12h16M4 17h16" stroke-linecap="round"/></svg>
                        Ver catálogo
                    </a>
                </div>
            </div>

            {{-- ilustración --}}
            <div class="justify-self-center">
                {{-- si quieres dejamos el SVG que ya tenías, lo omito aquí para no alargar --}}
            </div>
        </div>
    </section>

    {{-- CATÁLOGO / TARJETAS --}}
    <section id="catalogo" class="max-w-7xl mx-auto px-4 py-12 grid gap-6">
        <h2 class="text-2xl font-bold mb-2">Explora por categoría</h2>
        <div class="grid gap-6 md:grid-cols-3">
            @foreach($categories as $category)
                @include('welcome.partials.category-card', ['category' => $category])
            @endforeach
        </div>
    </section>

    {{-- DESTACADOS --}}
    <section class="max-w-7xl mx-auto px-4 pb-16">
        <h2 class="text-2xl font-bold mb-4">Destacados</h2>
        <div class="grid gap-6 md:grid-cols-3">
            @forelse($destacados as $d)
                <div class="rounded-3xl bg-white border border-gray-100 shadow-sm hover:shadow-lg transition overflow-hidden">
                    <div class="h-48 flex items-center justify-center bg-[color:var(--dw-lilac)]/20 overflow-hidden">
                        @if($d->image_path)
                            <img src="{{ $d->image_path }}"
                                class="max-h-44 w-auto object-contain transition-transform duration-300 hover:scale-105"
                                alt="{{ $d->title }}">
                        @else
                            <div class="text-gray-400 text-sm">Sin imagen</div>
                        @endif
                    </div>
                    <div class="p-5">
                        <h3 class="font-semibold">{{ $d->title }}</h3>
                        @if($d->description)
                            <p class="text-sm text-gray-600">{{ $d->description }}</p>
                        @endif
                        <div class="mt-3 flex items-center justify-between">
                            <span class="text-[color:var(--dw-accent)] font-semibold">
                                @if($d->show_price && $d->price)
                                    $ {{ number_format($d->price, 0, ',', '.') }}
                                @else
                                    Cotizar
                                @endif
                            </span>
                            <a href="https://wa.me/{{ env('DW_WHATSAPP','57XXXXXXXXXX') }}?text={{ urlencode('Hola, me interesa: '.$d->title) }}"
                               target="_blank" rel="noopener"
                               class="text-sm px-3 py-1.5 rounded-xl bg-[color:var(--dw-primary)] text-white hover:opacity-90 transition">Pedir</a>
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-500">No hay destacados por ahora.</p>
            @endforelse
        </div>
    </section>
@endsection

