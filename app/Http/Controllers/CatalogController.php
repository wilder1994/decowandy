<?php

namespace App\Http\Controllers;

use App\Support\CatalogView;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CatalogController extends Controller
{
    // Página pública
    public function welcome()
    {
        [$categories, $destacados] = $this->composeCatalogData();

        return view('welcome', compact('categories', 'destacados'));
    }

    // Vista del editor (staff)
    public function settings()
    {
        [$categories, $destacados] = $this->composeCatalogData();

        return view('settings.public', compact('categories', 'destacados'));
    }

    public function preview(Request $request)
    {
        $categoryName = $request->query('category');

        if (!$categoryName) {
            abort(422, 'category parameter is required');
        }

        [$categories] = $this->composeCatalogData();

        $category = $categories->firstWhere('key', $categoryName);

        if (!$category) {
            abort(404);
        }

        $card = view('welcome.partials.category-card', compact('category'))->render();
        $list = view('welcome.partials.category-list', compact('category'))->render();

        return response()->json([
            'card' => $card,
            'list' => $list,
        ]);
    }

    // === API DEL EDITOR ===
    public function index(Request $request)
    {
        $q = DB::table('catalog_items');

        if ($request->boolean('featured')) {
            $q->where('featured', 1)->where('visible', 1);
        } else {
            if ($cat = $request->query('category')) {
                $q->where('category', $cat);
            }
        }

        $items = $q->orderBy('sort_order')->orderBy('id')->get()->map(function ($x) {
            $x->price = (int) ($x->price ?? 0);
            if ($x->image_path && !Str::startsWith($x->image_path, ['http://', 'https://', '/storage'])) {
                $x->image_path = Storage::url($x->image_path);
            }
            return $x;
        });

        return response()->json([
            'items' => $items,
        ]);
    }

    // Crear
    public function store(Request $request)
    {
        $data = $this->validateData($request);

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('catalog', 'public');
        }

        // sort_order al final de esa categoría
        $max = DB::table('catalog_items')
            ->where('category', $data['category'])
            ->max('sort_order');

        $data['sort_order'] = (int) $max + 1;

        $id = DB::table('catalog_items')->insertGetId($data);

        return response()->json(['ok' => true, 'id' => $id]);
    }

    // Actualizar
    public function update(Request $request, $id)
    {
        $data = $this->validateData($request);

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('catalog', 'public');
        }

        DB::table('catalog_items')->where('id', $id)->update($data);

        return response()->json(['ok' => true]);
    }

    // Eliminar definitivo (porque tu tabla no tiene deleted_at)
    public function destroy($id)
    {
        DB::table('catalog_items')->where('id', $id)->delete();

        return response()->json(['ok' => true]);
    }

    // Reordenar
    public function sort(Request $request)
    {
        $request->validate([
            'category' => 'required|string',
            'ids'      => 'required|array',
        ]);

        foreach ($request->ids as $i => $id) {
            DB::table('catalog_items')
                ->where('id', $id)
                ->where('category', $request->category)
                ->update(['sort_order' => $i + 1]);
        }

        return response()->json(['ok' => true]);
    }

    private function validateData(Request $request): array
    {
        $v = $request->validate([
            'category'   => 'required|string|in:Papelería,Impresión,Diseño',
            'title'      => 'required|string|max:200',
            'description'=> 'nullable|string|max:1000',
            'price'      => 'nullable|integer|min:0',
            'show_price' => 'nullable|in:0,1',
            'visible'    => 'nullable|in:0,1',
            'featured'   => 'nullable|in:0,1',
        ]);

        $v['show_price'] = (int) ($v['show_price'] ?? 0);
        $v['visible']    = (int) ($v['visible'] ?? 1);
        $v['featured']   = (int) ($v['featured'] ?? 0);

        return $v;
    }

    protected function composeCatalogData(): array
    {
        $items = $this->fetchVisibleItems();
        $categories = CatalogView::compose($items);
        $destacados = $items->where('featured', 1)->take(6)->values();

        return [$categories, $destacados];
    }

    protected function fetchVisibleItems(): Collection
    {
        return DB::table('catalog_items')
            ->where('visible', 1)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(function ($x) {
                if ($x->image_path && !Str::startsWith($x->image_path, ['http://', 'https://', '/storage'])) {
                    $x->image_path = Storage::url($x->image_path);
                }
                return $x;
            });
    }
}
