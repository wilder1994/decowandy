<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Http\Requests\StorePurchaseRequest;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Services\InventoryService;
use Illuminate\Support\Facades\DB;


class PurchaseController extends Controller
{
    public function __construct(private readonly InventoryService $inventory) {}


    /**
     * POST /api/purchases
     * Request esperado (ejemplo):
     * {
     * date: '2025-10-25',
     * category: 'Papelería'|'Impresión'|'Diseño',
     * supplier?: 'Proveedor X',
     * note?: 'Factura 123',
     * to_inventory: true|false,
     * items: [
     * { product_name:'Resma Carta', quantity:5, total_cost:75000, item_id: 2 },
     * { product_name:'Tinta Cyan', quantity:1, total_cost:45000 } // sin item_id → gasto/consumo, no stock
     * ]
     * }
     */
    public function store(StorePurchaseRequest $request)
    {
        $purchaseDate = $request->date('date')->toDateString();
        $category = $request->string('category')->trim()->value();
        $supplier = $request->filled('supplier')
            ? $request->string('supplier')->trim()->value()
            : null;
        $note = $request->filled('note')
            ? $request->string('note')->trim()->value()
            : null;
        $toInventory = $request->boolean('to_inventory');

        $items = collect($request->validated()['items'] ?? []);

        $purchase = DB::transaction(function () use ($purchaseDate, $category, $supplier, $note, $toInventory, $items) {
            $purchase = Purchase::create([
                'date' => $purchaseDate,
                'category' => $category,
                'supplier' => $supplier,
                'note' => $note,
                'to_inventory' => $toInventory,
                'total' => 0,
            ]);


            $total = 0;
            foreach ($items as $line) {
                $qty = (int)$line['quantity'];
                $tcost = (int)$line['total_cost'];
                $ucost = intdiv($tcost, max(1, $qty)); // COP enteros
                $pi = PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_name' => $line['product_name'],
                    'quantity' => $qty,
                    'total_cost' => $tcost,
                    'unit_cost' => $ucost,
                    'item_id' => $line['item_id'] ?? null,
                ]);


                $total += $tcost;


                // Stock IN si aplica: Papelería o insumo de Impresión y hay item_id
                if ($toInventory && isset($line['item_id'])) {
                    // Nota: en F1 solo agregamos a inventario si explicitamente viene item_id
                    $this->inventory->in((int)$line['item_id'], $qty, $ucost, 'compra', $purchase->id);
                }
            }


            $purchase->update(['total' => $total]);


            return $purchase;
        });


        return response()->json(['ok' => true, 'purchase_id' => $purchase->id, 'total' => $purchase->total], 201);
    }
}
