<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Models\CatalogItem;
use Illuminate\Http\Request;


class CatalogItemController extends Controller
{
    public function index(Request $request)
    {
        $q = CatalogItem::query();
        if ($request->filled('category')) {
            $q->where('category', $request->get('category'));
        }
        if ($request->boolean('featured')) {
            $q->where('featured', true);
        }
        if ($request->has('visible')) {
            $q->where('visible', (bool)$request->get('visible'));
        }
        $q->orderBy('sort_order')->orderBy('id', 'desc');
        return response()->json($q->get());
    }
    public function store(Request $request)
    {
        $data = $request->validate([
            'category' => 'required|string|in:Papelería,Impresión,Diseño,Papeleria',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'nullable|integer|min:0',
            'show_price' => 'boolean',
            'visible' => 'boolean',
            'featured' => 'boolean',
            'sort_order' => 'integer',
            'image_path' => 'nullable|string|max:255',
            'item_id' => 'nullable|integer|exists:items,id',
        ]);
        $data['show_price'] = (bool)($data['show_price'] ?? true);
        $data['visible'] = (bool)($data['visible'] ?? true);
        $data['featured'] = (bool)($data['featured'] ?? false);
        $data['sort_order'] = (int)($data['sort_order'] ?? 0);


        $ci = CatalogItem::create($data);
        return response()->json(['ok' => true, 'item' => $ci], 201);
    }


    public function update(Request $request, CatalogItem $item)
    {
        $data = $request->validate([
            'category' => 'sometimes|required|string|in:Papelería,Impresión,Diseño,Papeleria',
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'nullable|integer|min:0',
            'show_price' => 'boolean',
            'visible' => 'boolean',
            'featured' => 'boolean',
            'sort_order' => 'integer',
            'image_path' => 'nullable|string|max:255',
            'item_id' => 'nullable|integer|exists:items,id',
        ]);
        $item->fill($data)->save();
        return response()->json(['ok' => true, 'item' => $item]);
    }


    public function destroy(Request $request, CatalogItem $item)
    {
        $item->delete();
        return response()->json(['ok' => true]);
    }
    /**
     * POST /ajustes/welcome/api/sort
     * payload: { ids: [5,2,9, ...] }
     */
    public function sort(Request $request)
    {
        $data = $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer|exists:catalog_items,id',
        ]);
        foreach ($data['ids'] as $i => $id) {
            CatalogItem::where('id', $id)->update(['sort_order' => $i]);
        }
        return response()->json(['ok' => true]);
    }
}
