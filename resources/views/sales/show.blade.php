{{-- resources/views/sales/show.blade.php --}}
@extends('layouts.admin')

@section('title','Factura #'.$sale->sale_code)

@section('content')
  @php($subtotal = $sale->items->sum('line_total'))
  <style>
    .paper {
      box-shadow: 0 22px 50px rgba(0,0,0,0.14);
      border-radius: 18px;
      overflow: hidden;
      background: #fff;
    }
    .paper header {
      background: linear-gradient(115deg, #0f3d35 0%, #0f3d35 55%, #1b5c4d 75%, #cbd4d0 75%);
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
      background:#0f3d35;
      border-bottom-left-radius:80% 80%;
    }
    .paper table th,
    .paper table td {
      border-bottom: 1px solid #e5e7eb;
    }
    .paper table tbody tr:nth-child(odd){
      background:#f8fafc;
    }
  </style>

  <div class="bg-slate-100 min-h-screen py-14 px-3">
    <div class="max-w-5xl mx-auto paper">
      <header>
        <div class="relative z-10 flex flex-col md:flex-row md:items-start md:justify-between gap-4">
          <div>
            <p class="text-xs uppercase tracking-[0.35em] text-white/75">DecoWandy</p>
            <h1 class="text-4xl font-black leading-tight tracking-tight">COMPANY NAME</h1>
            <p class="text-sm text-white/85">Tu socio en impresiones y papeleria</p>
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
        <div class="border border-slate-200 rounded-xl overflow-hidden">
          <table class="min-w-full text-sm">
            <thead class="text-white" style="background:#0f3d35;">
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
                  <td class="py-3 px-4 align-top text-gray-700">{{ str_pad($idx+1, 2, '0', STR_PAD_LEFT) }}.</td>
                  <td class="py-3 px-4 align-top">
                    <div class="font-semibold text-gray-900">{{ $item->item->name ?? $item->description ?? 'Item' }}</div>
                    <p class="text-xs text-gray-600 leading-snug">{{ $item->description ?? 'Producto agregado a la venta.' }}</p>
                  </td>
                  <td class="py-3 px-4 align-top text-gray-800">$ {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                  <td class="py-3 px-4 align-top text-gray-800">{{ $item->quantity }}</td>
                  <td class="py-3 px-4 align-top text-right font-semibold text-gray-900">$ {{ number_format($item->line_total, 0, ',', '.') }}</td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>

        <div class="grid gap-4 md:grid-cols-2">
          <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="font-semibold text-gray-900 mb-2">Informacion de pago</div>
            <p class="text-sm text-gray-800">Nombre de cuenta: DecoWandy</p>
            <p class="text-sm text-gray-800">Numero de cuenta: 0000 2222 1111 3333</p>
            <p class="text-sm text-gray-800">Banco: N/A</p>
            <div class="pt-4 font-semibold text-gray-900">Terminos y condiciones</div>
            <p class="text-xs text-gray-600 leading-relaxed">Gracias por su compra. No hay reembolsos despues de 7 dias. Soporte: soporte@deco.com</p>
          </div>
          <div class="rounded-xl border border-slate-200 bg-white overflow-hidden shadow-sm">
            <table class="w-full text-sm text-gray-800">
              <tr class="border-b border-slate-200">
                <td class="py-3 px-4">Subtotal</td>
                <td class="py-3 px-4 text-right">$ {{ number_format($subtotal, 0, ',', '.') }}</td>
              </tr>
              <tr class="border-b border-slate-200">
                <td class="py-3 px-4">Impuestos</td>
                <td class="py-3 px-4 text-right">0%</td>
              </tr>
              <tr class="border-b border-slate-200">
                <td class="py-3 px-4">Descuentos</td>
                <td class="py-3 px-4 text-right">0%</td>
              </tr>
              <tr style="background:#0f3d35;color:#fff;font-weight:700;">
                <td class="py-3 px-4 text-base">Total</td>
                <td class="py-3 px-4 text-right text-base">$ {{ number_format($sale->total, 0, ',', '.') }}</td>
              </tr>
            </table>
          </div>
        </div>

        <div class="rounded-xl border border-slate-200 overflow-hidden" style="background:linear-gradient(120deg, #f3fbf8 0%, #eef2f7 100%);">
          <div class="w-full flex justify-center px-10 py-16">
            <div class="max-w-3xl w-full">
              <div class="grid md:grid-cols-2 gap-14 text-sm text-gray-800">
                <div class="space-y-3 px-6">
                  <div class="font-semibold text-gray-900">Contacto</div>
                  <p>Email: soporte@deco.com</p>
                  <p>Web: deco.com</p>
                  <p>Direccion: Calle principal, Ciudad</p>
                </div>
                <div class="space-y-3 px-6">
                  <div class="font-semibold text-gray-900">Nota</div>
                  <p class="text-xs leading-relaxed text-gray-700">Gracias por su compra. Si necesita ayuda o facturacion personalizada, escribanos a soporte@deco.com.</p>
                </div>
              </div>
            </div>
          </div>
        </div>
    </div>
  </div>
@endsection

