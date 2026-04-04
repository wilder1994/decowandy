<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreItemRequest;
use App\Http\Requests\UpdateItemRequest;
use App\Models\Item;
use App\Models\Stock;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ItemController extends Controller
{
    public function __construct(private readonly InventoryService $inventory) {}

    public function index(Request $request)
    {
        $perPage = (int) $request->integer('per_page', 15);
        if ($perPage < 1) {
            $perPage = 15;
        }
        if ($perPage > 100) {
            $perPage = 100;
        }

        $search = trim((string) $request->query('search', ''));
        $sector = $request->query('sector');
        $type = $request->query('type');

        $query = Item::query()->orderBy('name');
        $query->with('stock');

        if ($search !== '') {
            $query->where(function ($builder) use ($search) {
                $builder->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($sector) {
            $query->where('sector', $sector);
        }

        if ($type) {
            $query->where('type', $type);
        }

        if ($request->filled('active')) {
            $query->where('active', $request->boolean('active'));
        }

        $items = $query->paginate($perPage)->withQueryString();
        $items->setCollection(
            $items->getCollection()->map(fn ($item) => $this->transformItem($item))
        );

        return response()->json([
            'ok' => true,
            'data' => $items->items(),
            'pagination' => [
                'current_page' => $items->currentPage(),
                'last_page' => $items->lastPage(),
                'per_page' => $items->perPage(),
                'total' => $items->total(),
            ],
            'links' => [
                'first' => $items->url(1),
                'last' => $items->url($items->lastPage()),
                'prev' => $items->previousPageUrl(),
                'next' => $items->nextPageUrl(),
            ],
            'filters' => [
                'search' => $search,
                'sector' => $sector,
                'type' => $type,
            ],
        ]);
    }

    public function store(StoreItemRequest $request)
    {
        $data = $request->validated();

        $data['slug'] = Str::slug($data['name']) . '-' . Str::random(6);
        $data['sale_price'] = (float) $request->input('sale_price', 0);
        $data['cost'] = (float) $request->input('cost', 0);
        $data['stock'] = (int) $request->input('stock', 0);
        $data['min_stock'] = (int) $request->input('min_stock', 0);
        $data['featured'] = $request->boolean('featured', false);
        $data['active'] = $request->boolean('active', true);

        $item = DB::transaction(function () use ($data) {
            $item = Item::create($data);
            $this->syncStock($item, $data, true);

            return $item;
        });

        return response()->json(['ok' => true, 'item' => $this->transformItem($item->fresh('stock'))], 201);
    }

    public function update(UpdateItemRequest $request, Item $item)
    {
        $data = $request->validated();

        if (array_key_exists('sale_price', $data)) {
            $data['sale_price'] = (float) $data['sale_price'];
        }
        if (array_key_exists('cost', $data)) {
            $data['cost'] = (float) ($data['cost'] ?? 0);
        }
        if (array_key_exists('stock', $data)) {
            $data['stock'] = (int) ($data['stock'] ?? 0);
        }
        if (array_key_exists('min_stock', $data)) {
            $data['min_stock'] = (int) ($data['min_stock'] ?? 0);
        }
        if ($request->has('featured')) {
            $data['featured'] = $request->boolean('featured');
        }
        if ($request->has('active')) {
            $data['active'] = $request->boolean('active');
        }

        $previousType = $item->type;

        $item = DB::transaction(function () use ($item, $data, $previousType) {
            $item->fill($data);
            $item->save();
            $this->syncStock($item, $data, false, $previousType);

            return $item;
        });

        return response()->json(['ok' => true, 'item' => $this->transformItem($item->refresh()->load('stock'))]);
    }

    public function destroy(Request $request, Item $item)
    {
        if ($request->boolean('force')) {
            if ($item->hasProtectedHistory()) {
                return response()->json([
                    'ok' => false,
                    'message' => 'No se puede eliminar este item porque ya tiene historial asociado. Desactivalo en su lugar.',
                ], 409);
            }

            $item->delete();

            return response()->json(['ok' => true, 'deleted' => true]);
        }

        if (! $item->active) {
            return response()->json(['ok' => true, 'active' => false, 'deleted' => false]);
        }

        $item->active = false;
        $item->save();

        return response()->json(['ok' => true, 'active' => false, 'deleted' => false]);
    }

    private function syncStock(Item $item, array $data, bool $isCreate, ?string $previousType = null): void
    {
        $previousType ??= $item->type;
        $nextType = $data['type'] ?? $item->type;
        $wasProduct = $previousType === 'product';
        $isProduct = $nextType === 'product';

        if (! $wasProduct && ! $isProduct) {
            return;
        }

        $stock = Stock::firstOrCreate(['item_id' => $item->id], ['quantity' => 0]);

        if (! $isProduct) {
            $stock->min_threshold = 0;
            $stock->save();
            $this->inventory->adjustToQuantity(
                $item->id,
                0,
                'Conversion del item a servicio. Inventario ajustado a cero.'
            );

            return;
        }

        $targetQty = array_key_exists('stock', $data)
            ? (int) $data['stock']
            : (int) $stock->quantity;

        $minThreshold = array_key_exists('min_stock', $data)
            ? (int) $data['min_stock']
            : (int) $stock->min_threshold;

        $stock->min_threshold = $minThreshold;
        $stock->save();

        $note = $isCreate
            ? 'Stock inicial registrado desde el editor de items.'
            : 'Ajuste manual registrado desde el editor de items.';

        $this->inventory->adjustToQuantity($item->id, $targetQty, $note);
    }

    private function transformItem(Item $item): array
    {
        $array = $item->toArray();
        $array['stock'] = $item->stock->quantity ?? $item->stock ?? 0;
        $array['min_stock'] = $item->stock->min_threshold ?? $item->min_stock ?? 0;

        return $array;
    }
}
