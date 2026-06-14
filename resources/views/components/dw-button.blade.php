@props([
    'variant' => 'primary',
    'type' => 'button',
    'href' => null,
])

@php
    $classes = match ($variant) {
        'primary' => 'dw-btn-primary',
        'secondary' => 'dw-btn-secondary',
        'danger' => 'dw-btn-danger',
        'ghost' => 'dw-btn-ghost',
        default => 'dw-btn-primary',
    };
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@else
    <button {{ $attributes->merge(['type' => $type, 'class' => $classes]) }}>
        {{ $slot }}
    </button>
@endif
