{{-- resources/views/purchases/show.blade.php
     Detalle de una compra específica con sus líneas
--}}
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
            <a href="{{ route('purchases.index') }}" class="inline-flex items-center gap-2 text-sm text-[color:var(--dw-primary)] hover:underline">
                ← Volver al listado
            </a>
            <h1 class="mt-2 text-2xl font-bold">Compra del {{ optional($purchase->date)->format('d/m/Y') }}</h1>
            <p class="text-sm text-gray-500">Categoría: <span class="font-semibold text-gray-700">{{ $purchase->category }}</span></p>
        </div>
        <div class="rounded-2xl border border-violet-100 bg-white px-5 py-3 text-right shadow-sm">
            <span class="block text-xs uppercase tracking-wide text-gray-500">Total compra</span>
            <span class="text-2xl font-semibold text-[color:var(--dw-primary)]">${{ number_format($purchase->total, 0, ',', '.') }}</span>
        </div>
    </div>

    <div class="grid gap-6 md:grid-cols-2">
        <section class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
            <h2 class="text-lg font-semibold text-gray-800">Información general</h2>
            <dl class="mt-4 grid grid-cols-1 gap-4 text-sm text-gray-600">
                <div>
                    <dt class="text-xs uppercase tracking-wide text-gray-500">Fecha</dt>
                    <dd class="mt-1 text-gray-800">{{ optional($purchase->date)->format('d/m/Y') }}</dd>
                </div>
                <div>
                    <dt class="text-xs uppercase tracking-wide text-gray-500">Proveedor</dt>
                    <dd class="mt-1 text-gray-800">{{ $purchase->supplier ?: 'No especificado' }}</dd>
                </div>
                <div>
                    <dt class="text-xs uppercase tracking-wide text-gray-500">Nota</dt>
                    <dd class="mt-1 text-gray-800">{{ $purchase->note ?: '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs uppercase tracking-wide text-gray-500">Inventario</dt>
                    <dd class="mt-1">
                        @if($purchase->to_inventory)
                            <span class="inline-flex items-center gap-2 rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">Sí, se actualizó inventario</span>
                        @else
                            <span class="inline-flex items-center gap-2 rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-600">No afecta inventario</span>
                        @endif
                    </dd>
                </div>
                <div>
                    <dt class="text-xs uppercase tracking-wide text-gray-500">Registrada</dt>
                    <dd class="mt-1 text-gray-800">{{ $purchase->created_at?->format('d/m/Y H:i') }}</dd>
                </div>
            </dl>
        </section>

        <section class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
            <h2 class="text-lg font-semibold text-gray-800">Resumen</h2>
            <div class="mt-4 grid gap-3 text-sm">
                <div class="rounded-xl bg-gray-50 p-3">
                    <span class="block text-xs uppercase tracking-wide text-gray-500">Líneas</span>
                    <span class="text-lg font-semibold text-gray-900">{{ $purchase->items->count() }}</span>
                </div>
                <div class="rounded-xl bg-gray-50 p-3">
                    <span class="block text-xs uppercase tracking-wide text-gray-500">Unidades</span>
                    <span class="text-lg font-semibold text-gray-900">{{ number_format($totalUnits, 0, ',', '.') }}</span>
                </div>
                <div class="rounded-xl bg-gray-50 p-3">
                    <span class="block text-xs uppercase tracking-wide text-gray-500">Actualizó inventario</span>
                    <span class="text-lg font-semibold text-gray-900">{{ $purchase->to_inventory ? 'Sí' : 'No' }}</span>
                </div>
            </div>
        </section>
    </div>

    <section class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
        <h2 class="text-lg font-semibold text-gray-800">Líneas de la compra</h2>
        <div class="mt-4 overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="text-gray-500">
                    <tr class="text-left">
                        <th class="px-3 py-2">Producto / Servicio</th>
                        <th class="px-3 py-2 text-right">Cantidad</th>
                        <th class="px-3 py-2 text-right">Costo total</th>
                        <th class="px-3 py-2 text-right">Costo unidad</th>
                        <th class="px-3 py-2">Ítem inventario</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($purchase->items as $line)
                        <tr>
                            <td class="px-3 py-2 font-medium text-gray-800">{{ $line->product_name }}</td>
                            <td class="px-3 py-2 text-right text-gray-700">{{ number_format($line->quantity, 0, ',', '.') }}</td>
                            <td class="px-3 py-2 text-right font-semibold text-gray-900">${{ number_format($line->total_cost, 0, ',', '.') }}</td>
                            <td class="px-3 py-2 text-right text-gray-700">${{ number_format($line->unit_cost, 0, ',', '.') }}</td>
                            <td class="px-3 py-2 text-gray-600">
                                @if($line->item)
                                    <span class="font-medium text-gray-800">{{ $line->item->name }}</span>
                                    <span class="block text-xs text-gray-500">{{ $sectorLabels[$line->item->sector] ?? ucfirst($line->item->sector) }}</span>
                                @else
                                    <span class="text-gray-500">— Sin enlace a inventario</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection
