@props(['category'])

@php
    $items = collect($category['items'] ?? []);
    $slug = $category['slug'] ?? \Illuminate\Support\Str::slug($category['name'] ?? '');
    $emptyMessage = $category['list_empty'] ?? 'No hay elementos disponibles.';
    $dwWhatsappNumber = preg_replace('/\D+/', '', env('DW_WHATSAPP', ''));
    $dwHasWhatsapp = strlen($dwWhatsappNumber) >= 10;
@endphp

<div id="{{ $slug }}-full">
    <h2 class="text-xl font-semibold mb-4">{{ $category['name'] ?? '' }}</h2>
    <div class="grid gap-6 md:grid-cols-3">
        @forelse($items as $item)
            <div class="rounded-3xl bg-white border border-gray-100 shadow-sm overflow-hidden">
                <div class="h-36 bg-slate-100 overflow-hidden">
                    @if($item->image_path)
                        <img src="{{ $item->image_path }}" class="w-full h-full object-cover" alt="{{ $item->title }}">
                    @endif
                </div>
                <div class="p-4">
                    <h3 class="font-semibold">{{ $item->title }}</h3>
                    @if($item->description)
                        <p class="text-sm text-gray-600">{{ $item->description }}</p>
                    @endif
                    <div class="mt-3 flex items-center justify-between">
                        <span class="text-[color:var(--dw-accent)] font-semibold">
                            @if($item->show_price && $item->price)
                                $ {{ number_format($item->price, 0, ',', '.') }}
                            @else
                                $ —
                            @endif
                        </span>
                        <a href="{{ $dwHasWhatsapp ? 'https://wa.me/'.$dwWhatsappNumber.'?text='.rawurlencode('Hola, me interesa: '.$item->title) : '#contacto' }}"
                           @if($dwHasWhatsapp) target="_blank" rel="noopener" @endif
                           class="text-sm px-3 py-1 rounded-xl bg-[color:var(--dw-primary)] text-white">{{ $dwHasWhatsapp ? 'Pedir' : 'Contacto' }}</a>
                    </div>
                </div>
            </div>
        @empty
            <p class="text-sm text-gray-500">{{ $emptyMessage }}</p>
        @endforelse
    </div>
</div>
