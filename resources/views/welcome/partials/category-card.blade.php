{{-- resources/views/welcome/partials/category-card.blade.php --}}
@php
    use Illuminate\Support\Str;
@endphp

<a href="{{ route('catalog.category', Str::slug($category['key'] ?? $category->key)) }}"
   class="block rounded-3xl overflow-hidden shadow-md hover:-translate-y-1 hover:shadow-xl transition bg-white">
    
    <div class="h-40 w-full overflow-hidden">
        @if(!empty($category['cover_image'] ?? null))
            <img src="{{ $category['cover_image'] }}" class="w-full h-full object-cover" alt="{{ $category['name'] }}">
        @else
            <div class="w-full h-full bg-gradient-to-br from-purple-100 to-purple-200"></div>
        @endif
    </div>

    <div class="p-6">
        <h3 class="text-xl font-semibold">{{ $category['name'] }}</h3>
        <p class="mt-2 text-gray-600 text-sm">{{ $category['description'] ?? '' }}</p>
        <p class="mt-4 font-semibold text-[color:var(--dw-accent)]">Ver productos</p>
    </div>
</a>
