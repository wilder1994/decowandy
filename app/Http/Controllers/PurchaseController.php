<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Purchase;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    public function index(Request $request)
    {
        $filters = [
            'category' => $request->query('category', 'all'),
            'from' => $request->query('from'),
            'to' => $request->query('to'),
        ];

        $query = Purchase::query();

        if ($filters['category'] && $filters['category'] !== 'all') {
            $query->where('category', $filters['category']);
        }

        if ($filters['from']) {
            $query->whereDate('date', '>=', $filters['from']);
        }

        if ($filters['to']) {
            $query->whereDate('date', '<=', $filters['to']);
        }

        $summaryTotal = (clone $query)->sum('total');

        $purchases = $query
            ->with(['items' => fn ($itemsQuery) => $itemsQuery->orderBy('id')])
            ->withCount('items')
            ->orderBy('date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(15)
            ->withQueryString();

        $existingCategories = Purchase::query()
            ->select('category')
            ->distinct()
            ->pluck('category')
            ->filter()
            ->values();

        $defaultCategories = collect(['Papelería', 'Impresión', 'Diseño']);

        $categoryOptions = $defaultCategories
            ->merge($existingCategories)
            ->unique()
            ->sort()
            ->values();

        $itemsCatalog = Item::orderBy('name')->get(['id', 'name', 'sector']);

        return view('purchases.index', [
            'purchases' => $purchases,
            'summaryTotal' => $summaryTotal,
            'filters' => $filters,
            'categoryOptions' => $categoryOptions,
            'itemsCatalog' => $itemsCatalog,
        ]);
    }

    public function show(Purchase $purchase)
    {
        $purchase->load(['items.item']);

        return view('purchases.show', [
            'purchase' => $purchase,
        ]);
    }
}
