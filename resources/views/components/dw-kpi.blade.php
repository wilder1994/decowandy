@props([
    'label',
    'value',
    'hint' => null,
    'icon' => null,
    'tone' => 'primary',
])

@php
    $iconColor = match ($tone) {
        'yellow' => 'text-dw-yellow',
        'rose' => 'text-dw-rose',
        'lilac' => 'text-dw-lilac',
        default => 'text-dw-primary',
    };
@endphp

<x-dw-card hover class="dw-kpi-card p-3 sm:p-3.5">
    <div class="flex items-center justify-between gap-2">
        <span class="text-xs font-medium uppercase tracking-wide text-dw-muted">{{ $label }}</span>
        @if ($icon)
            <span class="material-symbols-outlined text-base {{ $iconColor }}">{{ $icon }}</span>
        @endif
    </div>
    <div class="mt-1.5 font-display text-xl font-bold text-dw-text">{{ $value }}</div>
    @if ($hint)
        <div class="mt-1 text-xs text-dw-muted">{{ $hint }}</div>
    @endif
</x-dw-card>
