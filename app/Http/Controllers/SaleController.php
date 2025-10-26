<?php

namespace App\Http\Controllers;


use App\Models\Item;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;


class SaleController extends Controller
{
    public function __construct(private readonly InventoryService $inventory) {}


    /**
     * POST /ventas
     * Request esperado (ejemplo):
     * {
     * sold_at: '2025-10-25 13:05:00',
     * customer_name?, customer_email?, customer_phone?,
     * payment_method: 'cash'|'transfer'|'card'|'mixed'|'other',
     * amount_received: 25000,
     * items: [
     * { item_id:1, quantity:2 },
     * { item_id:3, quantity:1, unit_price:5000, sheets_used:1 } // impresión
     * ]
     * }
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'sold_at' => 'nullable|date',
            'customer_name' => 'nullable|string|max:255',
            'customer_email' => 'nullable|email|max:255',
            'customer_phone' => 'nullable|string|max:50',
            'payment_method' => 'required|string|max:20',
            'amount_received' => 'required|integer|min:0',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|integer|exists:items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'nullable|integer|min:0',
            'items.*.sheets_used' => 'nullable|integer|min:0',
        ]);


        $sale = DB::transaction(function () use ($data) {
            $total = 0;
            $lines = [];


            foreach ($data['items'] as $i => $line) {
                $item = Item::findOrFail($line['item_id']);
                $unitPrice = $line['unit_price'] ?? (int)$item->price; // trae precio del item si no viene
                $qty = (int)$line['quantity'];
                $lineTotal = $unitPrice * $qty; // COP enteros


                $lines[] = [
                    'item' => $item,
                    'quantity' => $qty,
                    'unit_price' => $unitPrice,
                    'line_total' => $lineTotal,
                    'sheets_used' => $line['sheets_used'] ?? null,
                ];


                $total += $lineTotal;
            }


            $sale = Sale::create([
                'sale_code' => 'DW-' . str_pad((string)((int)(optional(Sale::latest('id')->first())->id ?? 0) + 1), 6, '0', STR_PAD_LEFT),
                'sold_at' => $data['sold_at'] ?? now(),
                'customer_name' => $data['customer_name'] ?? null,
                'customer_email' => $data['customer_email'] ?? null,
                'customer_phone' => $data['customer_phone'] ?? null,
                'user_id' => optional(auth()->user())->id,
                'payment_method' => $data['payment_method'],
                'amount_received' => $data['amount_received'],
                'total' => $total,
                'change_due' => max(0, (int)$data['amount_received'] - $total),
            ]);


            foreach ($lines as $l) {
                $si = new SaleItem();
                $si->sale_id = $sale->id;
                $si->item_id = $l['item']->id;
                $si->category = $l['item']->category; // copia para reportes
                $si->quantity = $l['quantity'];
                $si->unit_price = $l['unit_price'];
                $si->line_total = $l['line_total'];
                $si->sheets_used = $l['sheets_used']; // solo aplica impresión
                $si->save();

                // Hook inventario: solo Papelería en F1
                if (strtolower($l['item']->category) === 'papelería' || strtolower($l['item']->category) === 'papeleria') {
                    $this->inventory->out($l['item']->id, $l['quantity'], 'venta', $sale->id);
                }
            }


            return $sale;
        });


        return response()->json([
            'ok' => true,
            'sale_id' => $sale->id,
            'sale_code' => $sale->sale_code,
            'total' => $sale->total,
            'change_due' => $sale->change_due,
        ], 201);
    }
}
