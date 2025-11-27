<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        [$sectors, $filters, $items] = $this->getItemsListing($request);

        $inventoryStats = [
            'stockable' => Item::where('type', 'product')->count(),
            'services' => Item::where('type', 'service')->count(),
            'units' => (int) Item::where('type', 'product')->sum('stock'),
            'low_stock' => Item::where('type', 'product')->whereColumn('stock', '<', 'min_stock')->count(),
        ];

        $lowStockItems = Item::where('type', 'product')
            ->whereColumn('stock', '<', 'min_stock')
            ->orderBy('stock')
            ->limit(5)
            ->get(['id', 'name', 'stock', 'min_stock', 'sector']);

        return view('items.index', [
            'items'   => $items,
            'filters' => $filters,
            'sectors' => $sectors,
            'inventoryStats' => $inventoryStats,
            'lowStockItems' => $lowStockItems,
        ]);
    }

    /**
    * Lógica común para listar ítems con filtros y paginación.
    */
    protected function getItemsListing(Request $request): array
    {
        $sectors = [
            'diseno'     => 'Diseño',
            'impresion'  => 'Impresión',
            'papeleria'  => 'Papelería',
        ];

        $filters = [
            'search' => trim((string) $request->query('search', '')),
            'sector' => (string) $request->query('sector', ''),
            'type'   => (string) $request->query('type', ''),
        ];

        if (!array_key_exists($filters['sector'], $sectors)) {
            $filters['sector'] = array_key_first($sectors);
        }

        $perPage = (int) $request->integer('per_page', 10);
        if ($perPage < 1) {
            $perPage = 10;
        }

        $query = Item::query();

        if ($filters['search'] !== '') {
            $query->where(function ($builder) use ($filters) {
                $builder->where('name', 'like', '%' . $filters['search'] . '%')
                        ->orWhere('description', 'like', '%' . $filters['search'] . '%');
            });
        }

        if ($filters['sector'] !== '') {
            $query->where('sector', $filters['sector']);
        }

        if ($filters['type'] !== '') {
            $query->where('type', $filters['type']);
        }

        $items = $query->orderBy('name')->paginate($perPage)->withQueryString();

        return [$sectors, $filters, $items];
    }

    /**
     * API: GET /api/items
     * Listado con filtros y paginación (usado por Axios en la vista).
     */
    public function apiIndex(Request $request)
    {
        [$sectors, $filters, $items] = $this->getItemsListing($request);

        return response()->json([
            'data' => $items->items(),
            'pagination' => [
                'current_page' => $items->currentPage(),
                'last_page'    => $items->lastPage(),
                'per_page'     => $items->perPage(),
                'total'        => $items->total(),
            ],
        ]);
    }

    public function create()
    {
        return view('items.partials.form', ['item' => null]);
    }

    public function edit(Item $item)
    {
        return view('items.partials.form', compact('item'));
    }

    public function destroy(Item $item)
    {
        return view('items.partials.confirm-delete', compact('item'));
    }
}
