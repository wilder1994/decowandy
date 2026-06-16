<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LabelSheetRequest;
use App\Http\Requests\StoreItemRequest;
use App\Http\Requests\UpdateItemRequest;
use App\Models\Item;
use App\Models\Stock;
use App\Services\InventoryService;
use App\Services\ItemBarcodeService;
use App\Services\ItemLabelService;
use App\Services\PurchasePapeleriaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ItemController extends Controller
{
    public function __construct(
        private readonly InventoryService $inventory,
        private readonly ItemBarcodeService $barcodes,
        private readonly ItemLabelService $labels,
        private readonly PurchasePapeleriaService $papeleria,
    ) {}

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
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('barcode', 'like', "%{$search}%")
                    ->orWhere('internal_sku', 'like', "%{$search}%");
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

    public function showByBarcode(string $barcode)
    {
        $item = $this->barcodes->findActiveByBarcode($barcode);

        if (! $item) {
            return response()->json([
                'ok' => false,
                'message' => 'No se encontró un producto activo con ese código.',
            ], 404);
        }

        return response()->json([
            'ok' => true,
            'item' => $this->transformItem($item),
        ]);
    }

    public function nextBarcode()
    {
        return response()->json([
            'ok' => true,
            'barcode' => $this->barcodes->nextInternalCode(),
        ]);
    }

    public function storePapeleriaQuick(Request $request)
    {
        if (! $request->user()?->isAdmin()) {
            abort(403);
        }

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'barcode' => 'nullable|string|max:64',
            'cost' => 'nullable|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'stock' => 'nullable|integer|min:0',
            'color' => 'nullable|string|max:40',
        ]);

        $cost = (int) round((float) ($data['cost'] ?? 0));
        $itemId = $this->papeleria->resolveItemId([
            'product_name' => $data['name'],
            'barcode' => $data['barcode'] ?? null,
            'sale_price' => $data['sale_price'] ?? null,
            'color' => $data['color'] ?? 'N/A',
            'scan_mode' => 'unit',
        ], max(0, $cost));

        $stock = (int) ($data['stock'] ?? 0);
        if ($stock > 0) {
            $this->inventory->adjustToQuantity(
                $itemId,
                $stock,
                'Stock inicial desde alta rápida en POS (admin).'
            );
        }

        $item = Item::with('stock')->findOrFail($itemId);

        return response()->json(['ok' => true, 'item' => $this->transformItem($item)], 201);
    }

    public function label(Item $item)
    {
        if ($item->sector !== 'papeleria' || empty($item->barcode)) {
            abort(404);
        }

        return $this->labels->renderPng($item);
    }

    public function labelCandidates(Request $request)
    {
        $search = trim((string) $request->query('search', ''));
        $limit = (int) $request->integer('limit', 0);

        $query = Item::query()
            ->where('sector', 'papeleria')
            ->whereNotNull('barcode')
            ->where('barcode', '!=', '')
            ->where('active', true)
            ->orderBy('name');

        if ($search !== '') {
            $query->where(function ($builder) use ($search) {
                $builder->where('name', 'like', "%{$search}%")
                    ->orWhere('barcode', 'like', "%{$search}%");
            });
        }

        if ($limit > 0) {
            $query->limit(min(500, $limit));
        } else {
            $query->limit(500);
        }

        $items = $query->get(['id', 'name', 'barcode']);

        return response()->json([
            'ok' => true,
            'items' => $items->map(fn (Item $item) => [
                'id' => $item->id,
                'name' => $item->name,
                'barcode' => $item->barcode,
            ])->values(),
        ]);
    }

    public function labelPreview(LabelSheetRequest $request)
    {
        return $this->labels->renderSheetPdf($request->normalizedLines(), inline: true);
    }

    public function labelSheet(LabelSheetRequest $request)
    {
        return $this->labels->renderSheetPdf($request->normalizedLines(), inline: false);
    }

    public function store(StoreItemRequest $request)
    {
        $data = $this->prepareItemData($request->validated(), true);
        $stockData = [
            'stock' => $data['stock'] ?? 0,
            'min_stock' => $data['min_stock'] ?? 0,
            'type' => $data['type'],
        ];
        unset($data['stock'], $data['min_stock']);

        $item = DB::transaction(function () use ($data, $stockData) {
            $item = Item::create($data);
            $this->syncStock($item, $stockData, true);

            return $item;
        });

        return response()->json(['ok' => true, 'item' => $this->transformItem($item->fresh('stock'))], 201);
    }

    public function update(UpdateItemRequest $request, Item $item)
    {
        $data = $this->prepareItemData($request->validated(), false, $item);
        $stockData = array_filter([
            'stock' => $data['stock'] ?? null,
            'min_stock' => $data['min_stock'] ?? null,
            'type' => $data['type'] ?? $item->type,
        ], fn ($value) => $value !== null);
        unset($data['stock'], $data['min_stock']);

        $previousType = $item->type;

        $item = DB::transaction(function () use ($item, $data, $stockData, $previousType) {
            $item->fill($data);
            $item->save();
            $this->syncStock($item, $stockData, false, $previousType);

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

    private function prepareItemData(array $data, bool $isCreate, ?Item $item = null): array
    {
        if ($isCreate) {
            $data['slug'] = Str::slug($data['name']) . '-' . Str::random(6);
        }

        $data['sale_price'] = (float) ($data['sale_price'] ?? $item?->sale_price ?? 0);
        $data['cost'] = (float) ($data['cost'] ?? $item?->cost ?? 0);
        if ($isCreate || array_key_exists('stock', $data)) {
            $data['stock'] = (int) ($data['stock'] ?? 0);
        }
        if ($isCreate || array_key_exists('min_stock', $data)) {
            $data['min_stock'] = (int) ($data['min_stock'] ?? 0);
        }
        $data['featured'] = (bool) ($data['featured'] ?? $item?->featured ?? false);
        $data['active'] = (bool) ($data['active'] ?? $item?->active ?? true);

        $data = $this->barcodes->applyPapeleriaDefaults($data, $isCreate);

        if (! empty($data['barcode'])) {
            $this->barcodes->assertUniqueBarcode($data['barcode'], $item?->id);
        }

        return $data;
    }

    private function syncStock(Item $item, array $data, bool $isCreate, ?string $previousType = null): void
    {
        $hasStock = array_key_exists('stock', $data);
        $hasMinStock = array_key_exists('min_stock', $data);
        $stockQty = $hasStock ? (int) $data['stock'] : null;
        $minStock = $hasMinStock ? (int) $data['min_stock'] : null;

        unset($data['stock'], $data['min_stock']);
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

        $targetQty = $stockQty !== null
            ? $stockQty
            : (int) $stock->quantity;

        $minThreshold = $minStock !== null
            ? $minStock
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
        $cost = (float) ($item->cost ?? 0);
        $array['suggested_sale_price'] = $this->barcodes->suggestedSalePrice(
            $cost,
            (float) config('decowandy.inventory.markup_percent', 40)
        );

        return $array;
    }
}
