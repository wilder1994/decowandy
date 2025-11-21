<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\Api\ExpenseController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\InvestmentController;
use App\Http\Controllers\ReportController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

//
// PÚBLICO
//
Route::get('/', [CatalogController::class, 'welcome'])->name('welcome');
Route::get('/catalogo/categoria/{category}', [CatalogController::class, 'category'])
    ->name('catalog.category');

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
    Route::get('/reportes', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/finanzas', [FinanceController::class, 'index'])->name('finance.index');
    Route::get('/finanzas/inversiones', [InvestmentController::class, 'index'])->name('finance.investments');
    Route::get('/gastos', [ExpenseController::class, 'index'])->name('expenses.index');

        
    // API gastos (usa sesión web, pero conserva el nombre api.expenses.store)
    Route::post('/api/expenses', [ExpenseController::class, 'store'])->name('api.expenses.store');
    
    // API ítems (usa sesión web, responde JSON)
    Route::get('/api/items', [ItemController::class, 'apiIndex'])->name('api.items.index');
    Route::post('/api/items', [ItemController::class, 'apiStore'])->name('api.items.store');
    Route::put('/api/items/{item}', [ItemController::class, 'apiUpdate'])->name('api.items.update');
    Route::delete('/api/items/{item}', [ItemController::class, 'apiDestroy'])->name('api.items.destroy');

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

    // API inversiones
    Route::get('/api/investments', [InvestmentController::class, 'apiIndex'])->name('api.investments.index');
    Route::post('/api/investments', [InvestmentController::class, 'store'])->name('api.investments.store');
    Route::put('/api/investments/{investment}', [InvestmentController::class, 'update'])->name('api.investments.update');
    Route::delete('/api/investments/{investment}', [InvestmentController::class, 'destroy'])->name('api.investments.destroy');
});

require __DIR__ . '/auth.php';
