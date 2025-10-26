<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PurchaseController;
use App\Http\Controllers\SaleController;

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

    // (Pendiente por agregar): Items, Expenses, Inventory, Catalog...
});