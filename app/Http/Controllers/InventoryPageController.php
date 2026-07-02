<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;

class InventoryPageController extends Controller
{
    public function index(Request $request)
    {
        $sectors = config('decowandy.sectors');
        $search = trim((string) $request->query('search', ''));
        $sector = (string) $request->query('sector', '');

        if ($sector !== '' && ! array_key_exists($sector, $sectors)) {
            $sector = '';
        }

        $stats = [
            'products' => Item::where('type', 'product')->where('active', true)->count(),
            'units' => (int) \App\Models\Stock::sum('quantity'),
            'low_stock' => (int) \App\Models\Stock::whereRaw('quantity <= COALESCE(min_threshold, 0)')->count(),
            'sectors' => Item::where('type', 'product')->where('active', true)->distinct()->count('sector'),
        ];

        $query = Item::query()
            ->select(
                'items.id',
                'items.name',
                'items.sector',
                'items.barcode',
                'items.sale_price',
                'items.color',
                'items.active',
                'stocks.quantity as stock',
                'stocks.min_threshold as min_stock'
            )
            ->join('stocks', 'stocks.item_id', '=', 'items.id')
            ->where('items.type', 'product')
            ->where('items.active', true);

        if ($search !== '') {
            $query->where(function ($builder) use ($search) {
                $builder->where('items.name', 'like', "%{$search}%")
                    ->orWhere('items.barcode', 'like', "%{$search}%");
            });
        }

        if ($sector !== '') {
            $query->where('items.sector', $sector);
        }

        $items = $query->orderBy('items.name')->paginate(15)->withQueryString();

        $lowStockItems = Item::query()
            ->select(
                'items.id',
                'items.name',
                'items.sector',
                'items.barcode',
                'items.sale_price',
                'items.color',
                'stocks.quantity as stock',
                'stocks.min_threshold as min_stock'
            )
            ->join('stocks', 'stocks.item_id', '=', 'items.id')
            ->where('items.type', 'product')
            ->where('items.active', true)
            ->whereRaw('stocks.quantity <= COALESCE(stocks.min_threshold, 0)')
            ->orderBy('stocks.quantity')
            ->limit(8)
            ->get();

        return view('inventory.index', [
            'items' => $items,
            'stats' => $stats,
            'lowStockItems' => $lowStockItems,
            'sectors' => $sectors,
            'filters' => [
                'search' => $search,
                'sector' => $sector,
            ],
            'inventoryConfig' => [
                'colors' => config('decowandy.inventory.colors', ['N/A']),
                'markup_percent' => (int) config('decowandy.inventory.markup_percent', 40),
            ],
            'categoryOptions' => collect(array_keys(config('decowandy.catalog_categories', []))),
            'itemsCatalog' => collect(),
            'sectorLabels' => $sectors,
        ]);
    }
}
