<?php

namespace App\Services;

use App\Models\Stock;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class InventoryService
{
    public function in(int $itemId, int $quantity, ?int $unitCost = null, string $reason = 'purchase', ?int $relatedId = null): void
    {
        if ($quantity <= 0) {
            return;
        }

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

    public function out(int $itemId, int $quantity, string $reason = 'sale', ?int $relatedId = null, bool $strict = true): void
    {
        if ($quantity <= 0) {
            return;
        }

        DB::transaction(function () use ($itemId, $quantity, $reason, $relatedId, $strict) {
            $stock = Stock::where('item_id', $itemId)->lockForUpdate()->first();

            if (! $stock) {
                $stock = Stock::create(['item_id' => $itemId, 'quantity' => 0]);
            }

            if ($strict && $stock->quantity < $quantity) {
                throw new RuntimeException('No hay stock suficiente para completar la salida.');
            }

            $newQty = max(0, $stock->quantity - $quantity);
            $delta = $stock->quantity - $newQty;

            if ($delta <= 0 && $strict) {
                throw new RuntimeException('No hay stock suficiente para completar la salida.');
            }

            if ($delta > 0) {
                StockMovement::create([
                    'item_id' => $itemId,
                    'type' => 'out',
                    'quantity' => $delta,
                    'reason' => $reason,
                    'ref_id' => $relatedId,
                ]);
            }

            $stock->quantity = $newQty;
            $stock->save();
        });
    }

    public function adjustToQuantity(int $itemId, int $targetQuantity, ?string $note = null): void
    {
        $targetQuantity = max(0, $targetQuantity);

        DB::transaction(function () use ($itemId, $targetQuantity, $note) {
            $stock = Stock::where('item_id', $itemId)->lockForUpdate()->first();

            if (! $stock) {
                $stock = Stock::create(['item_id' => $itemId, 'quantity' => 0]);
            }

            $currentQuantity = (int) $stock->quantity;
            $delta = $targetQuantity - $currentQuantity;

            if ($delta === 0) {
                return;
            }

            StockMovement::create([
                'item_id' => $itemId,
                'type' => $delta > 0 ? 'in' : 'out',
                'quantity' => abs($delta),
                'reason' => 'adjustment',
                'notes' => $note,
            ]);

            $stock->quantity = $targetQuantity;
            $stock->save();
        });
    }

    public function recalcStock(int $itemId): void
    {
        $totals = StockMovement::selectRaw(
            "SUM(CASE WHEN type='in' THEN quantity ELSE 0 END) as tin, " .
            "SUM(CASE WHEN type='out' THEN quantity ELSE 0 END) as tout"
        )->where('item_id', $itemId)->first();

        $qty = (int) $totals->tin - (int) $totals->tout;
        $stock = Stock::firstOrCreate(['item_id' => $itemId], ['quantity' => 0]);
        $stock->quantity = max(0, $qty);
        $stock->save();
    }
}
