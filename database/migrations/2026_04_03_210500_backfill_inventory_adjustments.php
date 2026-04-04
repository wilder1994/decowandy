<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        $now = now();

        $movementTotals = DB::table('stock_movements')
            ->selectRaw("item_id, SUM(CASE WHEN type = 'in' THEN quantity ELSE 0 END) as total_in, SUM(CASE WHEN type = 'out' THEN quantity ELSE 0 END) as total_out")
            ->groupBy('item_id')
            ->get()
            ->keyBy('item_id');

        $rows = [];

        foreach (DB::table('stocks')->select('item_id', 'quantity')->get() as $stock) {
            $totals = $movementTotals->get($stock->item_id);
            $netQuantity = (int) (($totals->total_in ?? 0) - ($totals->total_out ?? 0));
            $currentQuantity = (int) $stock->quantity;
            $delta = $currentQuantity - $netQuantity;

            if ($delta === 0) {
                continue;
            }

            $rows[] = [
                'item_id' => $stock->item_id,
                'type' => $delta > 0 ? 'in' : 'out',
                'quantity' => abs($delta),
                'reason' => 'adjustment',
                'notes' => 'Backfill para conciliar stock actual con movimientos existentes.',
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if ($rows !== []) {
            DB::table('stock_movements')->insert($rows);
        }
    }

    public function down(): void
    {
        DB::table('stock_movements')
            ->where('reason', 'adjustment')
            ->where('notes', 'Backfill para conciliar stock actual con movimientos existentes.')
            ->delete();
    }
};
