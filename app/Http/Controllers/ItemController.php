<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function index(Request $request): RedirectResponse
    {
        return redirect()->route('inventory.index', $request->query());
    }

    public function buildCatalogPageData(Request $request): array
    {
        [$sectors, $filters, $items] = $this->getItemsListing($request);

        $paginated = $items instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator
            ? $items
            : $items;

        return [
            'items' => $paginated,
            'filters' => $filters,
            'sectors' => $sectors,
            'createSectors' => config('decowandy.item_create_sectors', []),
            'initialPayload' => [
                'items' => $paginated->items(),
                'pagination' => [
                    'current_page' => $paginated->currentPage(),
                    'last_page' => $paginated->lastPage(),
                    'per_page' => $paginated->perPage(),
                    'total' => $paginated->total(),
                ],
                'filters' => $filters,
            ],
            'inventoryConfig' => [
                'colors' => config('decowandy.inventory.colors', ['N/A']),
                'markup_percent' => (int) config('decowandy.inventory.markup_percent', 40),
            ],
        ];
    }

    /**
    * Lógica común para listar ítems con filtros y paginación.
    */
    protected function getItemsListing(Request $request): array
    {
        $sectors = config('decowandy.sectors');

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

        $query = Item::query()
            ->select('items.*', 'stocks.quantity as stock', 'stocks.min_threshold as min_stock')
            ->leftJoin('stocks', 'stocks.item_id', '=', 'items.id');

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

    public function confirmDelete(Item $item)
    {
        return view('items.partials.confirm-delete', compact('item'));
    }

    public function destroy(Item $item)
    {
        $item->active = false;
        $item->save();

        return redirect()
            ->route('inventory.index')
            ->with('status', 'Ítem desactivado correctamente.');
    }
}
