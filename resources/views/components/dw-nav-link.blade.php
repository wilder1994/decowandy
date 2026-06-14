@props(['href', 'active' => false, 'icon' => null])

<a href="{{ $href }}" {{ $attributes->merge(['class' => $active ? 'dw-nav-link-active' : 'dw-nav-link']) }}>
    @if ($icon)
        <span class="material-symbols-outlined text-base">{{ $icon }}</span>
    @endif
    {{ $slot }}
</a>
