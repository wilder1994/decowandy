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
    });

    Route::middleware('can:manage-inventory')->group(function () {
        Route::post('/purchases', [PurchaseController::class, 'store'])->name('api.purchases.store');
        Route::get('/items', [ItemController::class, 'index'])->name('api.items.index');
        Route::post('/items', [ItemController::class, 'store'])->name('api.items.store');
        Route::put('/items/{item}', [ItemController::class, 'update'])->name('api.items.update');
        Route::delete('/items/{item}', [ItemController::class, 'destroy'])->name('api.items.destroy');
        Route::get('/stocks/low', [InventoryController::class, 'lowStock'])->name('api.stocks.low');
    });
});
