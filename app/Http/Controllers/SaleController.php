<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSaleRequest;
use App\Models\Item;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Services\InventoryService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

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
     *   { item_id:1, quantity:2 },
     *   { item_id:3, quantity:1, unit_price:5000, sheets_used:1 }
     * ]
     * }
     */
    public function store(StoreSaleRequest $request)
    {
        $data = $request->validated();
        $soldAt = isset($data['sold_at']) ? Carbon::parse($data['sold_at']) : now();

        $itemIds = collect($data['items'])->pluck('item_id')->all();
        $catalog = Item::whereIn('id', $itemIds)->get()->keyBy('id');

        $sale = DB::transaction(function () use ($data, $catalog, $soldAt) {
            $total = 0;
            $lines = [];

            foreach ($data['items'] as $line) {
                $item = $catalog->get($line['item_id']);

                if (!$item) {
                    // Si el ítem fue eliminado entre la validación y la transacción, ignoramos la línea.
                    continue;
                }

                $quantity = (int) $line['quantity'];
                $unitPrice = array_key_exists('unit_price', $line) && $line['unit_price'] !== null
                    ? (int) $line['unit_price']
                    : (int) round($item->sale_price);

                $lineTotal = $unitPrice * $quantity;

                $lines[] = [
                    'item' => $item,
                    'sector' => $item->sector,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'line_total' => $lineTotal,
                    'sheets_used' => $line['sheets_used'] ?? null,
                    'description' => $line['description'] ?? null,
                ];

                $total += $lineTotal;
            }

            if ($total <= 0) {
                abort(422, 'La venta debe tener al menos un ítem con valor.');
            }

            $sale = Sale::create([
                'sale_code' => $this->nextSaleCode(),
                'sold_at' => $soldAt,
                'date' => $soldAt->toDateString(),
                'time' => $soldAt->format('H:i:s'),
                'customer_name' => $data['customer_name'] ?? null,
                'customer_email' => $data['customer_email'] ?? null,
                'customer_phone' => $data['customer_phone'] ?? null,
                'user_id' => auth()->id(),
                'payment_method' => $data['payment_method'],
                'amount_received' => (int) $data['amount_received'],
                'total' => $total,
                'change_due' => max(0, (int) $data['amount_received'] - $total),
                'notes' => $data['notes'] ?? null,
            ]);

            foreach ($lines as $lineData) {
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'item_id' => $lineData['item']->id,
                    'description' => $lineData['description'],
                    'quantity' => $lineData['quantity'],
                    'unit_price' => $lineData['unit_price'],
                    'line_total' => $lineData['line_total'],
                    'category' => $lineData['sector'],
                    'sheets_used' => $lineData['sheets_used'],
                ]);

                if ($lineData['sector'] === 'papeleria') {
                    $this->inventory->out($lineData['item']->id, $lineData['quantity'], 'venta', $sale->id);
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

    private function nextSaleCode(): string
    {
        $lastId = (int) optional(Sale::latest('id')->first())->id;

        return 'DW-' . str_pad((string) ($lastId + 1), 6, '0', STR_PAD_LEFT);
    }
}
