<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePurchaseRequest;
use App\Models\Item;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Services\InventoryService;
use App\Services\PurchasePapeleriaService;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    public function __construct(
        private readonly InventoryService $inventory,
        private readonly PurchasePapeleriaService $papeleria,
    ) {}

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
        $isPapeleria = $this->papeleria->isPapeleriaCategory($category);

        $items = collect($request->validated()['items'] ?? []);

        $resolvedLines = $items->map(function (array $line) use ($isPapeleria, $toInventory) {
            $qty = (int) $line['quantity'];
            $tcost = (int) $line['total_cost'];
            $ucost = intdiv($tcost, max(1, $qty));

            if ($toInventory && $isPapeleria) {
                $line['item_id'] = $this->papeleria->resolveItemId($line, $ucost);
            }

            $line['_unit_cost'] = $ucost;

            return $line;
        });

        $linkedItemIds = $resolvedLines->pluck('item_id')->filter()->unique()->values()->all();
        $catalog = Item::whereIn('id', $linkedItemIds)
            ->where('active', true)
            ->get()
            ->keyBy('id');

        if (count($linkedItemIds) !== $catalog->count()) {
            abort(422, 'Uno o mas items asociados ya no estan disponibles para inventario.');
        }

        $purchase = DB::transaction(function () use ($purchaseDate, $category, $supplier, $note, $toInventory, $resolvedLines, $catalog) {
            $purchase = Purchase::create([
                'date' => $purchaseDate,
                'category' => $category,
                'supplier' => $supplier,
                'note' => $note,
                'to_inventory' => $toInventory,
                'total' => 0,
            ]);

            $total = 0;

            foreach ($resolvedLines as $line) {
                $qty = (int) $line['quantity'];
                $tcost = (int) $line['total_cost'];
                $ucost = (int) $line['_unit_cost'];

                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_name' => $line['product_name'],
                    'quantity' => $qty,
                    'total_cost' => $tcost,
                    'unit_cost' => $ucost,
                    'item_id' => $line['item_id'] ?? null,
                ]);

                $total += $tcost;

                if ($toInventory && isset($line['item_id'])) {
                    if (! $catalog->has((int) $line['item_id'])) {
                        abort(422, 'Uno o mas items asociados ya no estan disponibles para inventario.');
                    }

                    $this->inventory->in((int) $line['item_id'], $qty, $ucost, 'purchase', $purchase->id);
                }
            }

            $purchase->update(['total' => $total]);

            return $purchase;
        });

        return response()->json([
            'ok' => true,
            'purchase_id' => $purchase->id,
            'total' => $purchase->total,
        ], 201);
    }
}
