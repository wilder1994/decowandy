<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Http\Requests\AdjustItemStockRequest;
use App\Models\Item;
use App\Models\Stock;
use App\Services\InventoryService;
use Illuminate\Support\Facades\DB;


class InventoryController extends Controller
{
    public function __construct(
        private readonly InventoryService $inventory,
    ) {}

    /**
     * PATCH /api/inventory/items/{item}/stock
     * Ajuste de existencias sin modificar la ficha del producto (barcode, precio, etc.).
     */
    public function adjustStock(AdjustItemStockRequest $request, Item $item)
    {
        if ($item->type !== 'product') {
            return response()->json([
                'ok' => false,
                'message' => 'Solo productos con control de stock pueden ajustarse aquí.',
            ], 422);
        }

        $stockQty = (int) $request->validated('stock');
        $minStock = (int) ($request->validated('min_stock') ?? 0);

        DB::transaction(function () use ($item, $stockQty, $minStock) {
            $stock = Stock::firstOrCreate(['item_id' => $item->id], ['quantity' => 0]);
            $stock->min_threshold = $minStock;
            $stock->save();

            $this->inventory->adjustToQuantity(
                $item->id,
                $stockQty,
                'Ajuste manual desde Inventario.'
            );
        });

        $item->load('stock');

        return response()->json([
            'ok' => true,
            'item' => [
                'id' => $item->id,
                'stock' => (int) ($item->stock->quantity ?? 0),
                'min_stock' => (int) ($item->stock->min_threshold ?? 0),
                'barcode' => $item->barcode,
            ],
        ]);
    }

    /**
     * GET /api/stocks/low
     * Lista items con stock en 0 o por debajo del umbral.
     */
    public function lowStock()
    {
        $thresholdSql = 'COALESCE(s.min_threshold, 0)';

        $rows = DB::table('stocks as s')
            ->join('items as i', 'i.id', '=', 's.item_id')
            ->select(
                'i.id as item_id',
                'i.name',
                'i.sector',
                's.quantity',
                DB::raw($thresholdSql . ' as min_threshold')
            )
            ->where(function ($q) use ($thresholdSql) {
                $q->where('s.quantity', '<=', DB::raw($thresholdSql))
                    ->orWhere('s.quantity', '<=', 0);
            })
            ->orderBy('s.quantity')
            ->orderBy('i.name')
            ->get();


        return response()->json($rows);
    }
}
