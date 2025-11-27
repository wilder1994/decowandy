<?php

namespace App\Providers;

use App\Models\Item;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('sales.partials.modal-create', function ($view) {
            $viewData = $view->getData();
            if (array_key_exists('catalogDataset', $viewData)) {
                return;
            }

            $catalogItems = Item::select(
                'items.id',
                'items.name',
                'items.sale_price',
                'items.sector',
                DB::raw('COALESCE(stocks.quantity, 0) as stock_qty')
            )
                ->leftJoin('stocks', 'stocks.item_id', '=', 'items.id')
                ->orderBy('items.name')
                ->get();

            $catalogDataset = $catalogItems->groupBy('sector')->map(function ($group) {
                return $group->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'name' => $item->name,
                        'unit' => (int) $item->sale_price,
                        'stock' => $item->sector === 'papeleria'
                            ? (int) $item->stock_qty
                            : null,
                    ];
                })->values();
            });

            $view->with('catalogDataset', $catalogDataset);
        });
    }
}
