<?php

namespace App\Services;


use App\Models\Stock;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use RuntimeException;


class InventoryService
{
    /**
     * Entrada a inventario (IN)
     */
    public function in(int $itemId, int $quantity, ?int $unitCost = null, string $reason = 'purchase', ?int $relatedId = null): void
    {
        if ($quantity <= 0) return;


        DB::transaction(function () use ($itemId, $quantity, $unitCost, $reason, $relatedId) {
            StockMovement::create([
                'item_id' => $itemId,
                'type' => 'in',
                'quantity' => $quantity,
                'unit_cost' => $unitCost,
                'reason' => $reason,
                'ref_id' => $relatedId,
            ]);


            $stock = Stock::firstOrCreate(['item_id' => $itemId], ['quantity' => 0]);
            $stock->increment('quantity', $quantity);
        });
    }


    /**
     * Salida de inventario (OUT)
     */
    public function out(int $itemId, int $quantity, string $reason = 'sale', ?int $relatedId = null): void
    {
        if ($quantity <= 0) return;


        DB::transaction(function () use ($itemId, $quantity, $reason, $relatedId) {
            $stock = Stock::firstOrCreate(['item_id' => $itemId], ['quantity' => 0]);


            $newQty = max(0, $stock->quantity - $quantity); // evita negativos en F1
            $delta = $newQty - $stock->quantity; // será negativo o cero


            StockMovement::create([
                'item_id' => $itemId,
                'type' => 'out',
                'quantity' => abs($delta),
                'reason' => $reason,
                'ref_id' => $relatedId,
            ]);


            $stock->quantity = $newQty;
            $stock->save();
        });
    }


    /**
     * Recalcular stock desde movimientos (opcional, F2)
     */
    public function recalcStock(int $itemId): void
    {
        $totals = StockMovement::selectRaw(
            "SUM(CASE WHEN type='in' THEN quantity ELSE 0 END) as tin, " .
                "SUM(CASE WHEN type='out' THEN quantity ELSE 0 END) as tout"
        )->where('item_id', $itemId)->first();


        $qty = (int)$totals->tin - (int)$totals->tout;
        $stock = Stock::firstOrCreate(['item_id' => $itemId], ['quantity' => 0]);
        $stock->quantity = max(0, $qty);
        $stock->save();
    }
}
