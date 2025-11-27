<?php

namespace App\Providers;

use App\Models\Item;
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

            $catalogItems = Item::select('id', 'name', 'sale_price', 'sector')
                ->orderBy('name')
                ->get();

            $catalogDataset = $catalogItems->groupBy('sector')->map(function ($group) {
                return $group->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'name' => $item->name,
                        'unit' => (int) $item->sale_price,
                    ];
                })->values();
            });

            $view->with('catalogDataset', $catalogDataset);
        });
    }
}
