<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Controladores
use App\Http\Controllers\SaleController;
use App\Http\Controllers\Api\PurchaseController;
use App\Http\Controllers\Api\ItemController;
use App\Http\Controllers\Api\InventoryController;
use App\Http\Controllers\CustomerController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| Aquí se registran las rutas de la API del sistema.
| Todas las rutas están bajo el middleware 'api' y, por seguridad,
| se recomienda protegerlas con 'auth:sanctum' donde aplique.
|--------------------------------------------------------------------------
*/

// Información del usuario autenticado
Route::middleware(['web', 'auth'])->get('/user', function (Request $request) {
    return $request->user();
});

// Agrupar endpoints protegidos (requieren login)
Route::middleware(['web', 'auth'])->group(function () {
    // Ventas
    Route::post('/ventas', [SaleController::class, 'store'])->name('api.ventas.store');

    // Compras
    Route::post('/purchases', [PurchaseController::class, 'store'])->name('api.purchases.store');

    // Items POS
    Route::get('/items', [ItemController::class, 'index'])->name('api.items.index');
    Route::post('/items', [ItemController::class, 'store'])->name('api.items.store');
    Route::put('/items/{item}', [ItemController::class, 'update'])->name('api.items.update');
    Route::delete('/items/{item}', [ItemController::class, 'destroy'])->name('api.items.destroy');

    // Clientes
    Route::get('/customers', [CustomerController::class, 'search'])->name('api.customers.search');
    Route::post('/customers', [CustomerController::class, 'store'])->name('api.customers.store');

    // Inventario
    Route::get('/stocks/low', [InventoryController::class, 'lowStock'])->name('api.stocks.low');
});
