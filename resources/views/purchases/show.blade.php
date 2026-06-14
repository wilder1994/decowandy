{{-- resources/views/purchases/show.blade.php --}}
@extends('layouts.admin')

@section('title','Compra #'.$purchase->id.' — DecoWandy')

@section('content')
@php
    $sectorLabels = [
        'papeleria' => 'Papelería',
        'impresion' => 'Impresión',
        'diseno' => 'Diseño',
    ];
    $totalUnits = $purchase->items->sum('quantity');
@endphp
<div class="space-y-6">
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <a href="{{ route('purchases.index') }}" class="dw-link">← Volver al listado</a>
            <x-dw-page-header
                class="mt-2"
                :title="'Compra del '.optional($purchase->date)->format('d/m/Y')"
                :subtitle="'Categoría: '.$purchase->category"
            />
        </div>
        <x-dw-kpi label="Total compra" :value="'$'.number_format($purchase->total, 0, ',', '.')" />
    </div>

    <div class="grid gap-6 md:grid-cols-2">
        <x-dw-card>
            <h2 class="font-display text-sm font-semibold text-dw-text">Información general</h2>
            <dl class="mt-4 grid grid-cols-1 gap-4 text-sm">
                <div>
                    <dt class="dw-label">Fecha</dt>
                    <dd class="mt-1 text-dw-text">{{ optional($purchase->date)->format('d/m/Y') }}</dd>
                </div>
                <div>
                    <dt class="dw-label">Proveedor</dt>
                    <dd class="mt-1 text-dw-text">{{ $purchase->supplier ?: 'No especificado' }}</dd>
                </div>
                <div>
                    <dt class="dw-label">Nota</dt>
                    <dd class="mt-1 text-dw-text">{{ $purchase->note ?: '—' }}</dd>
                </div>
                <div>
                    <dt class="dw-label">Inventario</dt>
                    <dd class="mt-1">
                        @if($purchase->to_inventory)
                            <x-dw-badge variant="primary">Sí, se actualizó inventario</x-dw-badge>
                        @else
                            <x-dw-badge variant="warning">No afecta inventario</x-dw-badge>
                        @endif
                    </dd>
                </div>
                <div>
                    <dt class="dw-label">Registrada</dt>
                    <dd class="mt-1 text-dw-text">{{ $purchase->created_at?->format('d/m/Y H:i') }}</dd>
                </div>
            </dl>
        </x-dw-card>

        <x-dw-card>
            <h2 class="font-display text-sm font-semibold text-dw-text">Resumen</h2>
            <div class="mt-4 grid gap-3 text-sm">
                <div class="rounded-dw border-hairline border-dw-border bg-dw-lilac-soft p-3">
                    <span class="block text-xs uppercase tracking-wide text-dw-muted">Líneas</span>
                    <span class="font-display text-lg font-semibold text-dw-text">{{ $purchase->items->count() }}</span>
                </div>
                <div class="rounded-dw border-hairline border-dw-border bg-dw-lilac-soft p-3">
                    <span class="block text-xs uppercase tracking-wide text-dw-muted">Unidades</span>
                    <span class="font-display text-lg font-semibold text-dw-text">{{ number_format($totalUnits, 0, ',', '.') }}</span>
                </div>
                <div class="rounded-dw border-hairline border-dw-border bg-dw-lilac-soft p-3">
                    <span class="block text-xs uppercase tracking-wide text-dw-muted">Actualizó inventario</span>
                    <span class="font-display text-lg font-semibold text-dw-text">{{ $purchase->to_inventory ? 'Sí' : 'No' }}</span>
                </div>
            </div>
        </x-dw-card>
    </div>

    <x-dw-card padding="p-0" class="overflow-hidden">
        <div class="border-b px-4 py-3 dw-hairline">
            <h2 class="font-display text-sm font-semibold text-dw-text">Líneas de la compra</h2>
        </div>
        <div class="overflow-x-auto px-4 py-3">
            <table class="dw-table min-w-full text-sm">
                <thead>
                    <tr class="text-left">
                        <th class="px-3 py-2">Producto / Servicio</th>
                        <th class="px-3 py-2 text-right">Cantidad</th>
                        <th class="px-3 py-2 text-right">Costo total</th>
                        <th class="px-3 py-2 text-right">Costo unidad</th>
                        <th class="px-3 py-2">Ítem inventario</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($purchase->items as $line)
                        <tr>
                            <td class="px-3 py-2 font-medium text-dw-text">{{ $line->product_name }}</td>
                            <td class="px-3 py-2 text-right text-dw-text">{{ number_format($line->quantity, 0, ',', '.') }}</td>
                            <td class="px-3 py-2 text-right font-semibold text-dw-text">${{ number_format($line->total_cost, 0, ',', '.') }}</td>
                            <td class="px-3 py-2 text-right text-dw-text">${{ number_format($line->unit_cost, 0, ',', '.') }}</td>
                            <td class="px-3 py-2 text-dw-muted">
                                @if($line->item)
                                    <span class="font-medium text-dw-text">{{ $line->item->name }}</span>
                                    <span class="block text-xs text-dw-muted">{{ $sectorLabels[$line->item->sector] ?? ucfirst($line->item->sector) }}</span>
                                @else
                                    — Sin enlace a inventario
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-dw-card>
</div>
@endsection
