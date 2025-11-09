<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Controladores
use App\Http\Controllers\SaleController;
use App\Http\Controllers\Api\PurchaseController;
use App\Http\Controllers\Api\ItemController;
use App\Http\Controllers\Api\CatalogItemController;
use App\Http\Controllers\Api\ExpenseController;
use App\Http\Controllers\Api\InventoryController;

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
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Agrupar endpoints protegidos (requieren login)
Route::middleware('auth:sanctum')->group(function () {
    // Ventas
    Route::post('/ventas', [SaleController::class, 'store'])->name('api.ventas.store');

    // Compras
    Route::post('/purchases', [PurchaseController::class, 'store'])->name('api.purchases.store');

    // Items POS
    Route::get('/items', [ItemController::class, 'index'])->name('api.items.index');
    Route::post('/items', [ItemController::class, 'store'])->name('api.items.store');
    Route::put('/items/{item}', [ItemController::class, 'update'])->name('api.items.update');
    Route::delete('/items/{item}', [ItemController::class, 'destroy'])->name('api.items.destroy');

    // Welcome / Catálogo público
    Route::get('/ajustes/welcome/items', [CatalogItemController::class, 'index'])->name('api.welcome.items.index');
    Route::post('/ajustes/welcome/items', [CatalogItemController::class, 'store'])->name('api.welcome.items.store');
    Route::post('/ajustes/welcome/items/{item}', [CatalogItemController::class, 'update'])->name('api.welcome.items.update'); // soporta X-HTTP-Method-Override
    Route::post('/ajustes/welcome/items/{item}/delete', [CatalogItemController::class, 'destroy'])->name('api.welcome.items.destroy');
    Route::post('/ajustes/welcome/sort', [CatalogItemController::class, 'sort'])->name('api.welcome.items.sort');

    // Gastos (movida a web.php para usar sesión web)
    //Route::post('/expenses', [ExpenseController::class, 'store'])->name('api.expenses.store');

    // Inventario
    Route::get('/stocks/low', [InventoryController::class, 'lowStock'])->name('api.stocks.low');
});
