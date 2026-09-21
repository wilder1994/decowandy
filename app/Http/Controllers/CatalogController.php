<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Services\CatalogCoverService;
use App\Support\CatalogView;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CatalogController extends Controller
{
    public function __construct(
        private readonly CatalogCoverService $covers
    ) {}

    public function welcome()
    {
        [$categories, $destacados] = $this->composeCatalogData();

        return view('welcome', compact('categories', 'destacados'));
    }

    public function settings()
    {
        [$categories, $destacados] = $this->composeCatalogData();
        $covers = CatalogView::coversBySlug();

        return view('settings.public', compact('categories', 'destacados', 'covers'));
    }

    public function preview(Request $request)
    {
        $categoryName = $request->query('category');

        if (! $categoryName) {
            abort(422, 'category parameter is required');
        }

        [$categories] = $this->composeCatalogData();

        $category = $categories->firstWhere('key', $categoryName);

        if (! $category) {
            abort(404);
        }

        $card = view('welcome.partials.category-card', compact('category'))->render();
        $list = view('welcome.partials.category-list', compact('category'))->render();

        return response()->json([
            'card' => $card,
            'list' => $list,
            'cover_image' => $category['cover_image'] ?? null,
        ]);
    }

    public function index(Request $request)
    {
        $q = DB::table('catalog_items')
            ->leftJoin('items', 'items.id', '=', 'catalog_items.item_id')
            ->leftJoin('stocks', 'stocks.item_id', '=', 'items.id')
            ->select([
                'catalog_items.*',
                'items.name as item_name',
                'items.sale_price as item_sale_price',
                'items.active as item_active',
                'stocks.quantity as stock_quantity',
            ]);

        if ($request->boolean('featured')) {
            $q->where('catalog_items.featured', 1)->where('catalog_items.visible', 1);
        } elseif ($cat = $request->query('category')) {
            $q->where('catalog_items.category', $cat);
        }

        $items = $q->orderBy('catalog_items.sort_order')->orderBy('catalog_items.id')->get()->map(function ($x) {
            return $this->presentCatalogRow($x);
        });

        return response()->json([
            'items' => $items,
        ]);
    }

    public function inventoryOptions(Request $request)
    {
        $category = $request->query('category');
        $sector = CatalogView::sectorForCategory((string) $category);

        if (! $sector) {
            abort(422, 'category parameter is required');
        }

        $search = trim((string) $request->query('q', ''));
        $excludeIds = DB::table('catalog_items')
            ->where('category', $category)
            ->whereNotNull('item_id')
            ->when($request->filled('except_catalog_id'), function ($q) use ($request) {
                $q->where('id', '!=', (int) $request->query('except_catalog_id'));
            })
            ->pluck('item_id')
            ->all();

        $items = Item::query()
            ->with('stock')
            ->where('sector', $sector)
            ->where('active', true)
            ->when($excludeIds !== [], fn ($q) => $q->whereNotIn('id', $excludeIds))
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($inner) use ($search) {
                    $inner->where('name', 'like', '%'.$search.'%')
                        ->orWhere('barcode', 'like', '%'.$search.'%');
                });
            })
            ->orderBy('name')
            ->limit($search !== '' ? 40 : 200)
            ->get()
            ->map(fn (Item $item) => [
                'id' => $item->id,
                'name' => $item->name,
                'sale_price' => (int) $item->sale_price,
                'stock' => (int) ($item->stock?->quantity ?? 0),
                'barcode' => $item->barcode,
            ]);

        return response()->json(['items' => $items]);
    }

    public function store(Request $request)
    {
        $data = $this->validateAndHydrate($request);

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('catalog', 'public');
        }

        $max = DB::table('catalog_items')
            ->where('category', $data['category'])
            ->max('sort_order');

        $data['sort_order'] = (int) $max + 1;

        $id = DB::table('catalog_items')->insertGetId($data);

        return response()->json(['ok' => true, 'id' => $id]);
    }

    public function update(Request $request, $id)
    {
        $existing = DB::table('catalog_items')->where('id', $id)->first();
        if (! $existing) {
            abort(404);
        }

        $data = $this->validateAndHydrate($request, (int) $id);

        if ($request->boolean('clear_image')) {
            if ($existing->image_path) {
                Storage::disk('public')->delete($existing->image_path);
            }
            $data['image_path'] = null;
        } elseif ($request->hasFile('image')) {
            if ($existing->image_path) {
                Storage::disk('public')->delete($existing->image_path);
            }
            $data['image_path'] = $request->file('image')->store('catalog', 'public');
        }

        DB::table('catalog_items')->where('id', $id)->update($data);

        return response()->json(['ok' => true]);
    }

    public function destroy($id)
    {
        $row = DB::table('catalog_items')->where('id', $id)->first();
        if ($row?->image_path) {
            Storage::disk('public')->delete($row->image_path);
        }

        DB::table('catalog_items')->where('id', $id)->delete();

        return response()->json(['ok' => true]);
    }

    public function sort(Request $request)
    {
        $request->validate([
            'category' => 'required|string',
            'ids' => 'required|array',
        ]);

        foreach ($request->ids as $i => $id) {
            DB::table('catalog_items')
                ->where('id', $id)
                ->where('category', $request->category)
                ->update(['sort_order' => $i + 1]);
        }

        return response()->json(['ok' => true]);
    }

    public function updateCover(Request $request, string $slug)
    {
        $request->validate([
            'cover' => 'required|file|image|max:2048',
        ]);

        $setting = $this->covers->upsertCover($slug, $request->file('cover'));

        return response()->json([
            'ok' => true,
            'cover_image' => $setting->coverUrl(),
        ]);
    }

    public function destroyCover(string $slug)
    {
        $setting = $this->covers->clearCover($slug);

        return response()->json([
            'ok' => true,
            'cover_image' => $setting->coverUrl(),
        ]);
    }

    private function validateAndHydrate(Request $request, ?int $catalogId = null): array
    {
        $allowedCategories = array_keys(config('decowandy.catalog_categories'));

        $v = $request->validate([
            'category' => 'required|string|in:'.implode(',', $allowedCategories),
            'item_id' => [
                'required',
                'integer',
                Rule::exists('items', 'id')->where(fn ($q) => $q->where('active', true)),
                Rule::unique('catalog_items', 'item_id')
                    ->where(fn ($q) => $q->where('category', $request->input('category')))
                    ->ignore($catalogId),
            ],
            'description' => 'nullable|string|max:1000',
            'show_price' => 'nullable|in:0,1',
            'visible' => 'nullable|in:0,1',
            'featured' => 'nullable|in:0,1',
            'image' => 'nullable|file|image|max:2048',
            'clear_image' => 'nullable|boolean',
        ]);

        $item = Item::query()->findOrFail((int) $v['item_id']);
        $sector = CatalogView::sectorForCategory($v['category']);

        if ($item->sector !== $sector) {
            abort(422, 'El producto no pertenece a esta categoría.');
        }

        return [
            'category' => $v['category'],
            'item_id' => $item->id,
            'title' => $item->name,
            'description' => $v['description'] ?? $item->description,
            'price' => (int) $item->sale_price,
            'show_price' => (int) ($v['show_price'] ?? 1),
            'visible' => (int) ($v['visible'] ?? 1),
            'featured' => (int) ($v['featured'] ?? 0),
        ];
    }

    protected function composeCatalogData(): array
    {
        $items = $this->fetchVisibleItems();
        $categories = CatalogView::compose($items);
        $destacados = $items->where('featured', 1)->take(8)->values();

        return [$categories, $destacados];
    }

    protected function fetchVisibleItems(): Collection
    {
        return DB::table('catalog_items')
            ->leftJoin('items', 'items.id', '=', 'catalog_items.item_id')
            ->leftJoin('stocks', 'stocks.item_id', '=', 'items.id')
            ->where('catalog_items.visible', 1)
            ->orderBy('catalog_items.sort_order')
            ->orderBy('catalog_items.id')
            ->select([
                'catalog_items.*',
                'items.name as item_name',
                'items.sale_price as item_sale_price',
                'items.active as item_active',
                'stocks.quantity as stock_quantity',
            ])
            ->get()
            ->map(fn ($x) => $this->presentCatalogRow($x));
    }

    private function presentCatalogRow(object $x): object
    {
        if ($x->item_id) {
            $x->title = $x->item_name ?: $x->title;
            $x->price = (int) ($x->item_sale_price ?? $x->price ?? 0);
        } else {
            $x->price = (int) ($x->price ?? 0);
        }

        $x->stock_quantity = (int) ($x->stock_quantity ?? 0);
        $x->show_price = (int) ($x->show_price ?? 0);
        $x->visible = (int) ($x->visible ?? 0);
        $x->featured = (int) ($x->featured ?? 0);

        if ($x->image_path && ! Str::startsWith($x->image_path, ['http://', 'https://', '/storage'])) {
            $x->image_path = Storage::url($x->image_path);
        }

        return $x;
    }

    public function category($category)
    {
        $map = collect(config('decowandy.catalog_categories'))
            ->mapWithKeys(fn ($meta) => [strtolower($meta['slug']) => $meta]);

        $meta = $map[strtolower($category)] ?? null;
        $categoryName = $meta['name'] ?? ucfirst($category);
        $slug = $meta['slug'] ?? strtolower($category);

        $covers = CatalogView::coversBySlug();
        $coverImage = $covers->get($slug);

        $items = DB::table('catalog_items')
            ->leftJoin('items', 'items.id', '=', 'catalog_items.item_id')
            ->leftJoin('stocks', 'stocks.item_id', '=', 'items.id')
            ->where('catalog_items.visible', 1)
            ->where('catalog_items.category', $categoryName)
            ->orderBy('catalog_items.sort_order')
            ->orderBy('catalog_items.id')
            ->select([
                'catalog_items.*',
                'items.name as item_name',
                'items.sale_price as item_sale_price',
                'items.active as item_active',
                'stocks.quantity as stock_quantity',
            ])
            ->get()
            ->map(fn ($x) => $this->presentCatalogRow($x));

        return view('catalog.category', [
            'categoryName' => $categoryName,
            'items' => $items,
            'coverImage' => $coverImage,
        ]);
    }
}
