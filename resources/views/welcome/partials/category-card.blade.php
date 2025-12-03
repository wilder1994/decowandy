{{-- resources/views/welcome/partials/category-card.blade.php --}}
@php
    use Illuminate\Support\Str;

    $slug = Str::slug($category['slug'] ?? $category['key'] ?? $category['name'] ?? 'categoria');
    $items = collect($category['items'] ?? []);
@endphp

<a href="{{ route('catalog.category', $slug) }}#{{ $slug }}-full"
   class="block rounded-3xl overflow-hidden shadow-md hover:-translate-y-1 hover:shadow-xl transition bg-white">
    <div class="h-40 w-full overflow-hidden bg-gradient-to-br from-purple-100 to-purple-200 relative">
        <div class="absolute top-3 left-3 inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/80 text-[color:var(--dw-primary)] text-xs shadow-sm">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                <path d="M4 5h16M7 9h10M5 9l1 10h12l1-10" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M9 13h1m4 0h1" stroke-linecap="round"/>
            </svg>
            {{ $category['cta_label'] ?? 'Ver más' }}
        </div>
        @if(!empty($category['cover_image'] ?? null))
            <img src="{{ $category['cover_image'] }}" class="w-full h-full object-cover" alt="{{ $category['name'] }}">
        @endif
    </div>

    <div class="p-6 space-y-3">
        <div class="flex items-center justify-between">
            <h3 class="text-xl font-semibold">{{ $category['name'] }}</h3>
            <span class="text-xs px-2 py-1 rounded-full bg-purple-50 text-purple-700">
                {{ Str::title($slug) }}
            </span>
        </div>
        @if($items->isEmpty())
            <p class="text-sm text-gray-500">{{ $category['tag_empty'] ?? 'Sin productos' }}</p>
        @else
            <ul class="text-sm text-gray-700 space-y-1">
                @foreach($items->take(5) as $item)
                    <li class="flex items-center justify-between">
                        <span>{{ $item->title ?? $item['title'] ?? 'Item' }}</span>
                        @php
                            $price = $item->price ?? $item['price'] ?? null;
                            $showPrice = $item->show_price ?? $item['show_price'] ?? false;
                        @endphp
                        <span class="text-xs text-gray-500">
                            @if($showPrice && $price)
                                ${{ number_format((int) $price, 0, ',', '.') }}
                            @else
                                Cotizar
                            @endif
                        </span>
                    </li>
                @endforeach
            </ul>
            @if($items->count() > 5)
                <p class="text-xs text-gray-400">y {{ $items->count() - 5 }} más…</p>
            @endif
        @endif
        <p class="mt-1 font-semibold text-[color:var(--dw-accent)]">Ver productos</p>
    </div>
</a>
