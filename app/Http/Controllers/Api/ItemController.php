<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Http\Requests\StoreItemRequest;
use App\Http\Requests\UpdateItemRequest;
use App\Models\Item;
use Illuminate\Http\Request;


class ItemController extends Controller
{
    public function store(StoreItemRequest $request)
    {
        $item = Item::create([
            'name' => $request->string('name'),
            'category' => $request->string('category'),
            'price' => (int)$request->integer('price'),
            'sku' => $request->input('sku'),
            'unit' => $request->input('unit'),
            'is_active' => $request->boolean('is_active', true),
        ]);


        return response()->json(['ok' => true, 'item' => $item], 201);
    }


    public function update(UpdateItemRequest $request, Item $item)
    {
        $item->fill($request->validated());
        $item->save();
        return response()->json(['ok' => true, 'item' => $item]);
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
        $item->is_active = false;
        $item->save();
        return response()->json(['ok' => true, 'is_active' => false]);
    }
}
