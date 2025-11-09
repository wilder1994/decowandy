<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $sectors = [
            'diseno' => 'Diseño',
            'impresion' => 'Impresión',
            'papeleria' => 'Papelería',
        ];

        $filters = [
            'search' => trim((string) $request->query('search', '')),
            'sector' => (string) $request->query('sector', ''),
            'type' => (string) $request->query('type', ''),
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

        return view('items.index', [
            'items' => $items,
            'filters' => $filters,
            'sectors' => $sectors,
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
