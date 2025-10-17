@extends('layouts.admin')

@section('title', 'Registrar venta')

@section('content')
<div class="max-w-4xl mx-auto bg-white shadow-sm rounded-2xl p-6 border border-gray-100">
    <h1 class="text-xl font-semibold mb-4 text-[color:var(--dw-primary)]">Registrar nueva venta</h1>

    @if ($errors->any())
        <div class="mb-4 p-3 rounded-xl bg-red-50 border border-red-100 text-sm text-red-800">
            <ul class="list-disc ml-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('sales.store') }}">
        @csrf

        {{-- Datos del cliente (opcionales) --}}
        <div class="grid md:grid-cols-3 gap-4 mb-6">
            <div>
                <label class="block text-sm font-medium">Nombre del cliente</label>
                <input type="text" name="customer_name" class="w-full border rounded-xl px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium">Correo electrónico</label>
                <input type="email" name="customer_email" class="w-full border rounded-xl px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium">Teléfono</label>
                <input type="text" name="customer_phone" class="w-full border rounded-xl px-3 py-2">
            </div>
        </div>

        {{-- Tabla de productos --}}
        <div class="mb-6">
            <label class="block text-sm font-medium mb-2">Productos / servicios</label>
            <table class="w-full text-sm border">
                <thead class="bg-[color:var(--dw-lilac)]/20">
                    <tr>
                        <th class="p-2 border">Producto</th>
                        <th class="p-2 border">Cantidad</th>
                        <th class="p-2 border">Precio</th>
                        <th class="p-2 border">Subtotal</th>
                    </tr>
                </thead>
                <tbody id="itemsTable">
                    <tr>
                        <td class="p-2 border">
                            <select name="items[0][id]" class="w-full border rounded-lg px-2 py-1">
                                @foreach($items as $i)
                                    <option value="{{ $i->id }}">{{ $i->name }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td class="p-2 border">
                            <input type="number" name="items[0][quantity]" class="w-full border rounded-lg px-2 py-1" value="1" min="1">
                        </td>
                        <td class="p-2 border">
                            <input type="number" name="items[0][price]" class="w-full border rounded-lg px-2 py-1" value="0" min="0" step="0.01">
                        </td>
                        <td class="p-2 border text-center subtotal">0.00</td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- Método de pago --}}
        <div class="grid md:grid-cols-3 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium">Método de pago</label>
                <select name="payment_method" class="w-full border rounded-xl px-3 py-2">
                    <option value="cash">Efectivo</option>
                    <option value="transfer">Transferencia</option>
                    <option value="card">Tarjeta</option>
                    <option value="mixed">Mixto</option>
                    <option value="other">Otro</option>
                </select>
            </div>
        </div>

        {{-- Totales --}}
        <div class="grid md:grid-cols-3 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium">Total</label>
                <input type="number" id="total" name="total" readonly class="w-full border rounded-xl px-3 py-2 bg-gray-50">
            </div>
            <div>
                <label class="block text-sm font-medium">Monto recibido</label>
                <input type="number" id="amount_received" name="amount_received" class="w-full border rounded-xl px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium">Vuelto</label>
                <input type="number" id="change_due" readonly class="w-full border rounded-xl px-3 py-2 bg-gray-50">
            </div>
        </div>

        <div class="text-right">
            <button type="submit" class="px-6 py-2 rounded-xl text-white brand-gradient shadow hover:opacity-90">
                Guardar venta
            </button>
        </div>
    </form>
</div>

{{-- Script: calcula subtotales, total, vuelto en tiempo real --}}
<script>
document.addEventListener('input', function() {
    let rows = document.querySelectorAll('#itemsTable tr');
    let total = 0;
    rows.forEach(row => {
        let qty = parseFloat(row.querySelector('[name*="[quantity]"]').value) || 0;
        let price = parseFloat(row.querySelector('[name*="[price]"]').value) || 0;
        let sub = qty * price;
        row.querySelector('.subtotal').textContent = sub.toFixed(2);
        total += sub;
    });
    document.getElementById('total').value = total.toFixed(2);
    let paid = parseFloat(document.getElementById('amount_received').value) || 0;
    let change = paid - total;
    document.getElementById('change_due').value = change > 0 ? change.toFixed(2) : '0.00';
});
</script>
@endsection
