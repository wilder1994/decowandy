<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\Api\ExpenseController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\ItemController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

//
// PÚBLICO
//
Route::get('/', [CatalogController::class, 'welcome'])->name('welcome');

//
// DASHBOARD (autenticado)
//
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {

    // Perfil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Ventas: crear/guardar (lógica)
    Route::get('/ventas/nueva', [SaleController::class, 'create'])->name('sales.create');
    Route::post('/ventas', [SaleController::class, 'store'])->name('sales.store');

    // Vistas solo UI (panel)
    Route::get('/ventas', [SalesController::class, 'index'])->name('sales.index');
    Route::get('/compras', [PurchaseController::class, 'index'])->name('purchases.index');
    Route::get('/compras/{purchase}', [PurchaseController::class, 'show'])->name('purchases.show');
    Route::get('/items', [ItemController::class, 'index'])->name('items.index');
    Route::get('/items/create', [ItemController::class, 'create'])->name('items.create');
    Route::get('/items/{item}/edit', [ItemController::class, 'edit'])->name('items.edit');
    Route::get('/items/{item}/destroy', [ItemController::class, 'destroy'])->name('items.destroy');
    Route::get('/reportes', fn () => view('reports.index'))->name('reports.index');
    Route::get('/finanzas', fn () => view('finance.index'))->name('finance.index');
    Route::get('/finanzas/inversiones', fn () => view('finance.investments'))->name('finance.investments');
    Route::get('/gastos', [ExpenseController::class, 'index'])->name('expenses.index');

        
    // API gastos (usa sesión web, pero conserva el nombre api.expenses.store)
    Route::post('/api/expenses', [ExpenseController::class, 'store'])->name('api.expenses.store');


    // Ajustes → Editor de página pública (vista)
    Route::get('/ajustes/welcome', [CatalogController::class, 'settings'])->name('settings.public');

    // Ajustes → Welcome (API)
    Route::prefix('ajustes/welcome')->group(function () {
        Route::get('/api/items', [CatalogController::class, 'index'])->name('catalog.index');
        Route::post('/api/items', [CatalogController::class, 'store'])->name('catalog.store');
        Route::post('/api/items/{catalogItem}', [CatalogController::class, 'update'])->name('catalog.update'); // con X-HTTP-Method-Override: PUT
        Route::post('/api/items/{catalogItem}/delete', [CatalogController::class, 'destroy'])->name('catalog.destroy'); // con X-HTTP-Method-Override: DELETE
        Route::post('/api/sort', [CatalogController::class, 'sort'])->name('catalog.sort');
        Route::get('/api/preview', [CatalogController::class, 'preview'])->name('catalog.preview');
    });
});

require __DIR__ . '/auth.php';
