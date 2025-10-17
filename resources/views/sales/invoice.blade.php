<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; color:#333; font-size:12px; }
        .header { text-align:center; border-bottom:2px solid #A98AD4; margin-bottom:10px; }
        .header img { height:60px; margin-bottom:5px; }
        .title { color:#A98AD4; font-weight:bold; font-size:16px; }
        table { width:100%; border-collapse:collapse; margin-top:10px; }
        th, td { border:1px solid #ddd; padding:6px; text-align:left; }
        th { background:#F0E6FB; color:#333; }
        .totals { margin-top:20px; width:100%; }
        .totals td { padding:5px; }
        .footer { text-align:center; font-size:10px; margin-top:15px; color:#777; }
    </style>
</head>
<body>
<div class="header">
    <img src="{{ public_path('images/logo-decowandy.png') }}" alt="DecoWandy">
    <div class="title">Factura de venta</div>
    <div>DecoWandy - Diseño & Papelería</div>
</div>

<table>
    <tr>
        <td><strong>Código:</strong> {{ $sale->sale_code }}</td>
        <td><strong>Fecha:</strong> {{ $sale->date }} {{ $sale->time }}</td>
    </tr>
    <tr>
        <td><strong>Cliente:</strong> {{ $sale->customer_name ?? 'N/A' }}</td>
        <td><strong>Vendedor:</strong> {{ $sale->user->name ?? '' }}</td>
    </tr>
    <tr>
        <td><strong>Correo:</strong> {{ $sale->customer_email ?? 'N/A' }}</td>
        <td><strong>Teléfono:</strong> {{ $sale->customer_phone ?? 'N/A' }}</td>
    </tr>
</table>

<table>
    <thead>
        <tr>
            <th>Producto</th>
            <th>Cant.</th>
            <th>Precio</th>
            <th>Subtotal</th>
        </tr>
    </thead>
    <tbody>
        @foreach($sale->items as $li)
            <tr>
                <td>{{ $li->item->name ?? 'Producto eliminado' }}</td>
                <td>{{ $li->quantity }}</td>
                <td>${{ number_format($li->unit_price, 2) }}</td>
                <td>${{ number_format($li->line_total, 2) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<table class="totals">
    <tr>
        <td><strong>Total:</strong></td>
        <td>${{ number_format($sale->total, 2) }}</td>
    </tr>
    <tr>
        <td><strong>Monto recibido:</strong></td>
        <td>${{ number_format($sale->amount_received, 2) }}</td>
    </tr>
    <tr>
        <td><strong>Vuelto:</strong></td>
        <td>${{ number_format($sale->change_due, 2) }}</td>
    </tr>
</table>

<div class="footer">
    Gracias por tu compra 💜 — DecoWandy
</div>
</body>
</html>
