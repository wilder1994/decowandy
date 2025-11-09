{{-- resources/views/welcome.blade.php --}}
@extends('layouts.public')

@section('title', 'DecoWandy — Diseñar es crear, aprender es crecer')

@section('content')
    {{-- HERO --}}
    <section class="relative overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 pt-12 pb-16 grid gap-10 md:grid-cols-2 items-center">
            <div>
                <h1 class="text-4xl md:text-5xl font-bold leading-tight" style="font-family:'Poppins',Inter,system-ui">
                    Diseños, papelería e
                    <span class="block">impresiones</span>
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

                <div class="mt-6 flex flex-wrap gap-3">
                    <a href="https://wa.me/{{ env('DW_WHATSAPP','57XXXXXXXXXX') }}?text={{ urlencode('Hola DecoWandy, quiero cotizar un diseño o impresión 🙂') }}"
                       target="_blank" rel="noopener"
                       class="inline-flex items-center px-5 py-3 rounded-2xl text-white brand-gradient shadow hover:opacity-90 transition">
                        Solicitar por WhatsApp
                    </a>
                    <a href="#catalogo"
                       class="inline-flex items-center px-5 py-3 rounded-2xl border border-[color:var(--dw-lilac-2)] text-[color:var(--dw-accent)] hover:bg-[color:var(--dw-lilac)] transition">
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

    {{-- LISTAS COMPLETAS (anclas) --}}
    <section class="max-w-7xl mx-auto px-4 pb-12 space-y-10">
        @foreach($categories as $category)
            @include('welcome.partials.category-list', ['category' => $category])
        @endforeach
    </section>

    {{-- DESTACADOS --}}
    <section class="max-w-7xl mx-auto px-4 pb-16">
        <h2 class="text-2xl font-bold mb-4">Destacados</h2>
        <div class="grid gap-6 md:grid-cols-3">
            @forelse($destacados as $d)
                <div class="rounded-3xl bg-white border border-gray-100 shadow-sm hover:shadow-lg transition overflow-hidden">
                    <div class="h-40 bg-[color:var(--dw-lilac)]/30 overflow-hidden">
                        @if($d->image_path)
                            <img src="{{ $d->image_path }}" class="w-full h-full object-cover" alt="{{ $d->title }}">
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
                                    $ —
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
