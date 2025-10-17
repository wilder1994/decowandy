{{-- resources/views/welcome.blade.php --}}
{{-- Landing pública de DecoWandy: usa el layout public --}}
@extends('layouts.public')

@section('title', 'DecoWandy — Diseñar es crear, aprender es crecer')

@section('content')
    {{-- HERO principal (morados sutiles + trazo de pincel + animación continua) --}}
    <section class="relative overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 pt-12 pb-16 grid gap-10 md:grid-cols-2 items-center">

        {{-- Lado izquierdo: título + trazo de pincel --}}
        <div>
        <h1 class="text-4xl md:text-5xl font-bold leading-tight"
            style="font-family:'Poppins',Inter,system-ui">
            Diseños, papelería e
            <span class="block">impresiones</span>
        </h1>

        {{-- Trazo tipo pincel (brush stroke) debajo del título --}}
        <div class="mt-4">
            <svg viewBox="0 0 800 100" class="w-full h-20">
            <defs>
                <linearGradient id="dwBrush" x1="0%" y1="0%" x2="100%" y2="0%">
                <stop offset="0%"  stop-color="var(--dw-primary)" />
                <stop offset="100%" stop-color="var(--dw-accent)" />
                </linearGradient>
                <filter id="softShadow" x="-10%" y="-50%" width="120%" height="200%">
                <feDropShadow dx="0" dy="6" stdDeviation="12" flood-color="#000" flood-opacity="0.06"/>
                </filter>
            </defs>
            <path d="M20,70 C120,20 220,90 320,60 C420,30 520,100 620,55 C700,30 760,65 780,60"
                    stroke="url(#dwBrush)" stroke-width="28" stroke-linecap="round" fill="none"
                    filter="url(#softShadow)" opacity=".85"/>
            </svg>
        </div>

        <p class="mt-4 text-gray-600">
            Logos, tarjetas, papelería y servicios de impresión. Hecho con cariño por DecoWandy.
        </p>

        <div class="mt-6 flex flex-wrap gap-3">
            <a href="https://wa.me/TU_NUMERO?text=Hola%20DecoWandy,%20quiero%20cotizar%20un%20dise%C3%B1o%20o%20impresi%C3%B3n%20🙂"
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

        {{-- Lado derecho: animación en bucle (nube, estrellas, lapicero) --}}
        <div class="justify-self-center">
        <svg viewBox="0 0 460 320" class="w-[360px] md:w-[400px] h-auto">
            <defs>
            <linearGradient id="drawGrad" x1="0%" y1="0%" x2="100%" y2="0%">
                <stop offset="0%"  stop-color="var(--dw-primary)"/>
                <stop offset="100%" stop-color="var(--dw-accent)"/>
            </linearGradient>
            <filter id="soft" x="-20%" y="-20%" width="140%" height="140%">
                <feDropShadow dx="0" dy="8" stdDeviation="12" flood-color="#000" flood-opacity=".08"/>
            </filter>

            <style>
                @keyframes drawLoop {
                0%   { stroke-dashoffset: 1000; opacity: 1; }
                42%  { stroke-dashoffset: 0;    opacity: 1; }
                60%  { stroke-dashoffset: 0;    opacity: 1; }
                80%  { stroke-dashoffset: 1000; opacity: .9; }
                100% { stroke-dashoffset: 1000; opacity: 1; }
                }
                @keyframes floatY {
                0%,100% { transform: translateY(0px); }
                50%     { transform: translateY(-8px); }
                }
                @keyframes twinkle {
                0%,100% { opacity: .35; transform: scale(1); }
                50%     { opacity: .9;  transform: scale(1.06); }
                }

                .dw-draw {
                stroke: url(#drawGrad);
                stroke-width: 4;
                stroke-linecap: round;
                stroke-linejoin: round;
                fill: none;
                stroke-dasharray: 1000;
                stroke-dashoffset: 1000;
                animation: drawLoop 8s ease-in-out infinite;
                filter: url(#soft);
                }
                .delay2 { animation-delay: .6s; }
                .cloud { fill: #fff; opacity:.95; filter:url(#soft); animation: floatY 5.5s ease-in-out infinite; }
                .star  { fill: none; stroke: url(#drawGrad); stroke-width: 3.5; filter:url(#soft); animation: twinkle 2.8s ease-in-out infinite; }
                .star.s2 { animation-delay: .7s; }
                .star.s3 { animation-delay: 1.3s; }
                .pen { fill: var(--dw-accent); }
            </style>
            </defs>

            <!-- Nube flotando -->
            <path class="cloud"
                d="M90,165c-12,0-22-9-24-20-16-5-28-20-28-38 0-22 18-40 40-40 8,0 15,2 21,6 7-18 24-30 44-30 26,0 48,22 48,48 0,2 0,5-1,7 4-1 8-2 12-2 23,0 42,19 42,42s-19,42-42,42H90z"/>

            <!-- Estrellas -->
            <path class="star s1"
                d="M340,70l10,20 22,3-16,15 4,22-20-10-20,10 4-22-16-15 22-3z"/>
            <path class="star s2"
                d="M285,112l8,14 16,2-12,11 3,16-14-7-14,7 3-16-12-11 16-2z"/>
            <path class="star s3"
                d="M385,110l7,12 13,2-10,9 3,13-12-6-12,6 3-13-10-9 13-2z"/>

            <!-- Trazos -->
            <path id="trace1" class="dw-draw"
                d="M70,228c32,0 44,-23 70,-23 28,0 30,23 60,23 32,0 36,-23 66,-23 28,0 34,23 62,23" />
            <path id="trace2" class="dw-draw delay2"
                d="M80,252 C170,262 250,262 340,252" />

            <!-- Lapicero animado -->
            <circle r="4.5" class="pen">
            <animateMotion dur="8s" repeatCount="indefinite" rotate="auto">
                <mpath xlink:href="#trace1"/>
            </animateMotion>
            </circle>
        </svg>
        </div>
    </div>
    </section>


    {{-- SECCIÓN CATÁLOGO POR SECTOR --}}
    <section id="catalogo" class="max-w-7xl mx-auto px-4 py-12 grid gap-6">
        <h2 class="text-2xl font-bold mb-2">Explora por categoría</h2>
        <div class="grid gap-6 md:grid-cols-3">
            {{-- Papelería --}}
            <a id="papeleria" ...>
                <div class="h-36 rounded-2xl" style="background:linear-gradient(135deg, var(--dw-lilac), #f6f1ff)"></div>
                <h3 class="text-lg font-semibold">Papelería</h3>
                <p class="text-sm text-gray-600">Cuadernos, sobres, papel bond, cartulinas y más.</p>
                <span class="mt-3 inline-flex text-[color:var(--dw-accent)] group-hover:underline">Ver productos</span>
            </a>
            {{-- Impresión --}}
            <a id="impresion" ...>
                <div class="h-36 rounded-2xl" style="background:linear-gradient(135deg, #f1ecff, var(--dw-lilac))"></div>
                <h3 class="text-lg font-semibold">Impresión</h3>
                <p class="text-sm text-gray-600">Copias B/N y color, escaneo, fotos, anillados.</p>
                <span class="mt-3 inline-flex text-[color:var(--dw-accent)] group-hover:underline">Ver servicios</span>
            </a>
            {{-- Diseño --}}
            <a id="diseno" ...>
                <div class="h-36 rounded-2xl" style="background:linear-gradient(135deg, var(--dw-primary), var(--dw-lilac))" class="opacity-60"></div>
                <h3 class="text-lg font-semibold">Diseño</h3>
                <p class="text-sm text-gray-600">Logos, tarjetas y piezas gráficas a medida.</p>
                <span class="mt-3 inline-flex text-[color:var(--dw-accent)] group-hover:underline">Ver portafolio</span>
            </a>
        </div>
    </section>

    {{-- DESTACADOS (placeholder) --}}
    <section class="max-w-7xl mx-auto px-4 pb-16">
        <h2 class="text-2xl font-bold mb-4">Destacados</h2>
        <div class="grid gap-6 md:grid-cols-3">
            {{-- Tarjetas de ejemplo visual, luego se poblarán desde BD --}}
            @foreach ([1,2,3] as $i)
                <div class="rounded-3xl bg-white border border-gray-100 shadow-sm hover:shadow-lg transition overflow-hidden">
                    <div class="h-40 bg-[color:var(--dw-lilac)]/30"></div>
                    <div class="p-5">
                        <h3 class="font-semibold">Producto/Servicio {{ $i }}</h3>
                        <p class="text-sm text-gray-600">Descripción breve del ítem destacado.</p>
                        <div class="mt-3 flex items-center justify-between">
                            <span class="text-[color:var(--dw-accent)] font-semibold">$ —</span>
                            <a href="https://wa.me/TU_NUMERO?text=Hola%20DecoWandy,%20me%20interesa%20este%20producto%20destacado%20{{ $i }}"
                               target="_blank" rel="noopener"
                               class="text-sm px-3 py-1.5 rounded-xl bg-[color:var(--dw-primary)] text-white hover:opacity-90 transition">Pedir</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
@endsection
