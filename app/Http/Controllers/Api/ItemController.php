<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreItemRequest;
use App\Http\Requests\UpdateItemRequest;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ItemController extends Controller
{
    public function store(StoreItemRequest $request)
    {
        $data = $request->validated();

        $data['slug'] = Str::slug($data['name']).'-'.Str::random(6);
        $data['sale_price'] = (float) $request->input('sale_price', 0);
        $data['cost'] = (float) $request->input('cost', 0);
        $data['stock'] = (int) $request->input('stock', 0);
        $data['min_stock'] = (int) $request->input('min_stock', 0);
        $data['featured'] = $request->boolean('featured', false);
        $data['active'] = $request->boolean('active', true);

        $item = Item::create($data);

        return response()->json(['ok' => true, 'item' => $item->fresh()], 201);
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

        $item->fill($data);
        $item->save();

        return response()->json(['ok' => true, 'item' => $item->refresh()]);
    }

    /**
     * Inactivar por defecto; eliminar duro si ?force=1
     */
    public function destroy(Request $request, Item $item)
    {
        if ($request->boolean('force')) {
            $item->delete();
            return response()->json(['ok' => true, 'deleted' => true]);
        }
        $item->active = false;
        $item->save();
        return response()->json(['ok' => true, 'active' => false]);
    }
}
