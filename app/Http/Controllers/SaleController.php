<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class SaleController extends Controller
{
    /**
     * Formulario para registrar una venta (POS)
     */
    public function create()
    {
        $items = Item::where('active', true)->orderBy('name')->get();
        return view('sales.create', compact('items'));
    }

    /**
     * Guarda la venta + detalle e imprime PDF
     */
    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'nullable|string|max:100',
            'customer_email' => 'nullable|email|max:100',
            'customer_phone' => 'nullable|string|max:30',
            'total' => 'required|numeric|min:0',
            'amount_received' => 'required|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|exists:items,id',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'payment_method' => 'nullable|in:cash,transfer,card,mixed,other',
        ]);

        DB::beginTransaction();
        try {
            $change = max($request->amount_received - $request->total, 0);

            $sale = Sale::create([
                'sale_code' => 'DW-' . date('YmdHis'),
                'date' => now()->toDateString(),
                'time' => now()->toTimeString(),
                'user_id' => Auth::id(),
                'customer_name' => $request->customer_name,
                'customer_email' => $request->customer_email,
                'customer_phone' => $request->customer_phone,
                'subtotal' => $request->total, // por ahora sin impuestos/descuentos
                'discount' => 0,
                'taxes' => 0,
                'total' => $request->total,
                'amount_received' => $request->amount_received,
                'change_due' => $change,
                'payment_method' => $request->payment_method ?? 'cash',
            ]);

            foreach ($request->items as $it) {
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'item_id' => $it['id'],
                    'quantity' => $it['quantity'],
                    'unit_price' => $it['price'],
                    'line_total' => $it['quantity'] * $it['price'],
                ]);
            }

            // Cargar relaciones para el PDF
            $sale->load(['items.item','user']);

            // Generar y guardar PDF
            $pdf = Pdf::loadView('sales.invoice', ['sale' => $sale]);
            $fileName = 'invoice_' . $sale->id . '.pdf';
            Storage::disk('public')->put('invoices/'.$fileName, $pdf->output());

            // Guardar ruta en la venta
            $sale->update(['invoice_pdf_path' => 'invoices/'.$fileName]);

            DB::commit();

            return redirect()
                ->route('dashboard')
                ->with('success', 'Venta registrada y factura generada.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error: ' . $e->getMessage()]);
        }
    }
}
