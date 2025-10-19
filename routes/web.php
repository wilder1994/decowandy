<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SaleController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/ventas/nueva', [SaleController::class, 'create'])->name('sales.create');
    Route::post('/ventas', [SaleController::class, 'store'])->name('sales.store');
});

// Índice de ventas (solo vista / diseño)
Route::get('/ventas', fn () => view('sales.index'))->name('sales.index');
Route::get('/compras', fn () => view('purchases.index'))->name('purchases.index');

// Gestor de productos (solo vista / UI)
Route::get('/items', fn () => view('items.index'))->name('items.index');

// Reportes (solo UI)
Route::get('/reportes', fn () => view('reports.index'))->name('reports.index');

// Finanzas (solo UI)
Route::get('/finanzas', fn () => view('finance.index'))->name('finance.index');
Route::get('/finanzas/inversiones', fn () => view('finance.investments'))->name('finance.investments');

require __DIR__.'/auth.php';
