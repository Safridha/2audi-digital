<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;

/*
|--------------------------------------------------------------------------
| Public / Home
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return view('home');
})->name('home');

/*
|--------------------------------------------------------------------------
| Auth routes (login, register, logout, password, etc.)
| kept in routes/auth.php (Breeze/Jetstream default)
|--------------------------------------------------------------------------
*/
require __DIR__ . '/auth.php';

/*
|--------------------------------------------------------------------------
| Protected: Profile (for authenticated users)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| ADMIN: semua route di /admin/* hanya untuk user role=admin
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        // admin dashboard (named admin.dashboard)
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // contoh route admin lain
        Route::get('/categories', fn() => 'Halaman Kelola Kategori')->name('categories.index');
        Route::get('/products', fn() => 'Halaman Kelola Produk')->name('products.index');
        Route::get('/stock', fn() => 'Halaman Kelola Stok Bahan')->name('stock.index');
        Route::get('/orders', fn() => 'Halaman Kelola Pesanan')->name('orders.index');
        Route::get('/users', fn() => 'Halaman Kelola Pengguna')->name('users.index');
    });
