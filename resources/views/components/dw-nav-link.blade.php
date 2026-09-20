@props(['href', 'active' => false, 'icon' => null])

@php
    $label = trim((string) $slot);
@endphp

<a href="{{ $href }}" title="{{ $label }}" {{ $attributes->merge(['class' => $active ? 'dw-nav-link-active' : 'dw-nav-link']) }}>
    @if ($icon)
        <span class="material-symbols-outlined shrink-0 text-base" aria-hidden="true">{{ $icon }}</span>
    @endif
    <span class="dw-nav-label">{{ $label }}</span>
</a>
