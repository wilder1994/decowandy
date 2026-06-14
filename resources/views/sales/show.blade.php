{{-- resources/views/sales/show.blade.php --}}
@extends('layouts.admin')

@section('title','Factura #'.$sale->sale_code)

@section('content')
  @php($subtotal = $sale->items->sum('line_total'))
  <style>
    .paper {
      box-shadow: var(--dw-glow-md, 0 22px 50px rgba(0,0,0,0.14));
      border-radius: 18px;
      overflow: hidden;
      background: #fff;
    }
    .paper header {
      background: linear-gradient(115deg, var(--dw-primary-dark) 0%, var(--dw-primary-dark) 55%, var(--dw-primary) 75%, var(--dw-lilac-soft) 75%);
      color: #fff;
      padding: 30px 32px 34px;
      position: relative;
      min-height: 190px;
    }
    .paper header::after {
      content:'';
      position:absolute;
      bottom:-1px;
      right:-6%;
      width:60%;
      height:120px;
      background:var(--dw-primary-dark);
      border-bottom-left-radius:80% 80%;
    }
    .paper table th,
    .paper table td {
      border-bottom: 1px solid var(--dw-border, #e5e7eb);
    }
    .paper table tbody tr:nth-child(odd){
      background:var(--dw-lilac-soft, #f8fafc);
    }
    .paper .invoice-total {
      background:var(--dw-primary-dark);
      color:#fff;
      font-weight:700;
    }
  </style>

  <div class="min-h-screen bg-dw-lilac-soft py-14 px-3">
    <div class="max-w-5xl mx-auto paper dw-hairline-neon">
      <header>
        <div class="relative z-10 flex flex-col md:flex-row md:items-start md:justify-between gap-4">
          <div>
            <p class="text-xs uppercase tracking-[0.35em] text-white/75">DecoWandy</p>
            <h1 class="font-display text-4xl font-black leading-tight tracking-tight">DECOWANDY</h1>
            <p class="text-sm text-white/85">Tu socio en impresiones y papelería</p>
            <div class="mt-4 space-y-1 text-white/90">
              <p class="text-xs uppercase tracking-wider">Factura para:</p>
              <p class="text-lg font-semibold">{{ $sale->customer_name ?? 'Venta de mostrador' }}</p>
              <p class="text-sm">{{ $sale->customer_email ?? $sale->customer_phone ?? 'Sin contacto' }}</p>
              <p class="text-sm">Direccion: N/A</p>
              <p class="text-sm">Registrado por: {{ $sale->user->name ?? 'N/A' }}</p>
            </div>
          </div>
          <div class="text-right text-white space-y-2">
            <p class="text-xs uppercase tracking-wider text-white/80">Factura</p>
            <p class="text-3xl font-black">#{{ $sale->sale_code }}</p>
            <div class="space-y-1 text-sm text-white/90">
              <div class="flex items-center gap-2 justify-end"><span class="font-semibold text-white">Fecha:</span><span>{{ optional($sale->sold_at ?? $sale->created_at)->format('d/m/Y H:i') }}</span></div>
              <div class="flex items-center gap-2 justify-end"><span class="font-semibold text-white">Metodo:</span><span>{{ ucfirst($sale->payment_method ?? 'N/A') }}</span></div>
            </div>
          </div>
        </div>
      </header>

      <div class="px-5 pb-16 pt-4 space-y-6">
        <div class="overflow-hidden rounded-dw border-hairline border-dw-border">
          <table class="min-w-full text-sm">
            <thead class="text-white" style="background:var(--dw-primary-dark);">
              <tr class="text-left">
                <th class="py-3 px-4 w-14">N°</th>
                <th class="py-3 px-4">Producto / Descripcion</th>
                <th class="py-3 px-4 w-32">Precio</th>
                <th class="py-3 px-4 w-24">Cant.</th>
                <th class="py-3 px-4 w-32 text-right">Total</th>
              </tr>
            </thead>
            <tbody>
              @foreach($sale->items as $idx => $item)
                <tr>
                  <td class="py-3 px-4 align-top text-dw-muted">{{ str_pad($idx+1, 2, '0', STR_PAD_LEFT) }}.</td>
                  <td class="py-3 px-4 align-top">
                    <div class="font-semibold text-dw-text">{{ $item->item->name ?? $item->description ?? 'Item' }}</div>
                    <p class="text-xs text-dw-muted leading-snug">{{ $item->description ?? 'Producto agregado a la venta.' }}</p>
                  </td>
                  <td class="py-3 px-4 align-top text-dw-text">$ {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                  <td class="py-3 px-4 align-top text-dw-text">{{ $item->quantity }}</td>
                  <td class="py-3 px-4 align-top text-right font-semibold text-dw-text">$ {{ number_format($item->line_total, 0, ',', '.') }}</td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>

        <div class="grid gap-4 md:grid-cols-2">
          <div class="rounded-dw border-hairline border-dw-border bg-dw-card p-5 shadow-dw-neon-sm">
            <div class="mb-2 font-semibold text-dw-text">Información de pago</div>
            <p class="text-sm text-dw-muted">Nombre de cuenta: DecoWandy</p>
            <p class="text-sm text-dw-muted">Número de cuenta: 0000 2222 1111 3333</p>
            <p class="text-sm text-dw-muted">Banco: N/A</p>
            <div class="pt-4 font-semibold text-dw-text">Términos y condiciones</div>
            <p class="text-xs text-dw-muted leading-relaxed">Gracias por su compra. No hay reembolsos después de 7 días. Soporte: soporte@deco.com</p>
          </div>
          <div class="overflow-hidden rounded-dw border-hairline border-dw-border bg-dw-card shadow-dw-neon-sm">
            <table class="w-full text-sm text-dw-text">
              <tr class="border-b border-dw-border">
                <td class="py-3 px-4">Subtotal</td>
                <td class="py-3 px-4 text-right">$ {{ number_format($subtotal, 0, ',', '.') }}</td>
              </tr>
              <tr class="border-b border-dw-border">
                <td class="py-3 px-4">Impuestos</td>
                <td class="py-3 px-4 text-right">0%</td>
              </tr>
              <tr class="border-b border-dw-border">
                <td class="py-3 px-4">Descuentos</td>
                <td class="py-3 px-4 text-right">0%</td>
              </tr>
              <tr class="invoice-total">
                <td class="py-3 px-4 text-base">Total</td>
                <td class="py-3 px-4 text-right text-base">$ {{ number_format($sale->total, 0, ',', '.') }}</td>
              </tr>
            </table>
          </div>
        </div>

        <div class="overflow-hidden rounded-dw border-hairline border-dw-border" style="background:linear-gradient(120deg, var(--dw-lilac-soft) 0%, #fff 100%);">
          <div class="flex w-full justify-center px-10 py-16">
            <div class="w-full max-w-3xl">
              <div class="grid gap-14 text-sm text-dw-muted md:grid-cols-2">
                <div class="space-y-3 px-6">
                  <div class="font-semibold text-dw-text">Contacto</div>
                  <p>Email: soporte@deco.com</p>
                  <p>Web: deco.com</p>
                  <p>Dirección: Calle principal, Ciudad</p>
                </div>
                <div class="space-y-3 px-6">
                  <div class="font-semibold text-dw-text">Nota</div>
                  <p class="text-xs leading-relaxed">Gracias por su compra. Si necesita ayuda o facturación personalizada, escríbanos a soporte@deco.com.</p>
                </div>
              </div>
            </div>
          </div>
        </div>
    </div>
  </div>
@endsection

