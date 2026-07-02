<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\Api\PurchaseController;
use App\Http\Controllers\Api\ItemController;
use App\Http\Controllers\Api\InventoryController;
use App\Http\Controllers\CustomerController;

Route::middleware(['web', 'auth'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware(['web', 'auth'])->group(function () {
    Route::middleware('can:operate')->group(function () {
        Route::post('/ventas', [SaleController::class, 'store'])->name('api.ventas.store');
        Route::get('/customers', [CustomerController::class, 'search'])->name('api.customers.search');
        Route::post('/customers', [CustomerController::class, 'store'])->name('api.customers.store');
        Route::get('/items/by-barcode/{barcode}', [ItemController::class, 'showByBarcode'])
            ->where('barcode', '.*')
            ->name('api.items.by-barcode');
    });

    Route::middleware('can:manage-inventory')->group(function () {
        Route::post('/purchases', [PurchaseController::class, 'store'])->name('api.purchases.store');
        Route::get('/items', [ItemController::class, 'index'])->name('api.items.index');
        Route::get('/items/catalog-export', [ItemController::class, 'catalogExport'])->name('api.items.catalog-export');
        Route::post('/items/catalog-list/pdf', [ItemController::class, 'catalogListPdf'])->name('api.items.catalog-list.pdf');
        Route::get('/items/next-barcode', [ItemController::class, 'nextBarcode'])->name('api.items.next-barcode');
        Route::post('/items/papeleria/quick', [ItemController::class, 'storePapeleriaQuick'])->name('api.items.papeleria.quick');
        Route::get('/items/labels/candidates', [ItemController::class, 'labelCandidates'])->name('api.items.labels.candidates');
        Route::post('/items/labels/preview', [ItemController::class, 'labelPreview'])->name('api.items.labels.preview');
        Route::post('/items/labels/sheet', [ItemController::class, 'labelSheet'])->name('api.items.labels.sheet');
        Route::get('/items/{item}/label.png', [ItemController::class, 'label'])->name('api.items.label');
        Route::post('/items', [ItemController::class, 'store'])->name('api.items.store');
        Route::put('/items/{item}', [ItemController::class, 'update'])->name('api.items.update');
        Route::patch('/inventory/items/{item}/stock', [InventoryController::class, 'adjustStock'])->name('api.inventory.adjust-stock');
        Route::delete('/items/{item}', [ItemController::class, 'destroy'])->name('api.items.destroy');
        Route::get('/stocks/low', [InventoryController::class, 'lowStock'])->name('api.stocks.low');
    });
});
