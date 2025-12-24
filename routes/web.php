<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ShippingController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\OrderHistoryController;
use App\Http\Controllers\RegionController;

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\BahanController;
use App\Http\Controllers\InventoryController;

use App\Models\Category;

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    $categories = Category::orderBy('name')->get();
    return view('home', compact('categories'));
})->name('home');

Route::get('/katalog-produk/{category}', [CatalogController::class, 'byCategory'])
    ->name('catalog.products');

Route::get('/search', [CatalogController::class, 'search'])
    ->name('catalog.search');

require __DIR__ . '/auth.php';

/*
|--------------------------------------------------------------------------
| USER ROUTES (NEED LOGIN)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // 1 Produk
    Route::get('/produk/{product}/pesan', [CheckoutController::class, 'productForm'])->name('product.order');
    Route::post('/produk/{product}/pesan-sekarang', [CheckoutController::class, 'singleStart'])->name('product.single.start');

    Route::get('/checkout/single', [CheckoutController::class, 'single'])->name('checkout.single');
    Route::post('/checkout/single', [CheckoutController::class, 'singleStore'])->name('checkout.single.store');

    // Multi (Keranjang -> Checkout)
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');

    // Payment
    Route::get('/checkout/pay/{order}', [PaymentController::class, 'pay'])->name('checkout.pay');
    Route::get('/payment/finish', [PaymentController::class, 'finish'])->name('payment.finish');
    Route::get('/payment/unfinish', [PaymentController::class, 'unfinish'])->name('payment.unfinish');
    Route::get('/payment/error', [PaymentController::class, 'error'])->name('payment.error');

    // Region (Shopee-like)
    Route::get('/regions/provinces', [RegionController::class, 'provinces'])->name('regions.provinces');
    Route::get('/regions/cities/{province}', [RegionController::class, 'cities'])->name('regions.cities');
    Route::get('/regions/districts/{city}', [RegionController::class, 'districts'])->name('regions.districts');

    // Cek ongkir (AJAX)
    Route::post('/cek-ongkir', [ShippingController::class, 'cekOngkir'])->name('shipping.cek');

    // Cart
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add/{product}', [CartController::class, 'add'])->name('cart.add');
    Route::delete('/cart/remove/{item}', [CartController::class, 'remove'])->name('cart.remove');
    Route::get('/cart/{item}/edit', [CartController::class, 'edit'])->name('cart.edit');
    Route::put('/cart/{item}', [CartController::class, 'update'])->name('cart.update');
    
    // Riwayat
    Route::get('/orders/history', [OrderHistoryController::class, 'index'])->name('orders.history');
});

// Webhook midtrans (NO AUTH)
Route::post('/midtrans/callback', [PaymentController::class, 'callback'])->name('midtrans.callback');

/*
|--------------------------------------------------------------------------
| ADMIN ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // ✅ Bulk destroy routes (WAJIB ADA karena dipanggil di blade admin)
        Route::delete('users/bulk-destroy', [UserController::class, 'bulkDestroy'])->name('users.bulk-destroy');
        Route::delete('orders/bulk-destroy', [OrderController::class, 'bulkDestroy'])->name('orders.bulk-destroy');
        Route::delete('products/bulk-destroy', [ProductController::class, 'bulkDestroy'])->name('products.bulk-destroy');
        Route::delete('categories/bulk-destroy', [CategoryController::class, 'bulkDestroy'])->name('categories.bulk-destroy');
        Route::delete('bahans/bulk-destroy', [BahanController::class, 'bulkDestroy'])->name('bahans.bulk-destroy');

        // Resources
        Route::resource('categories', CategoryController::class);
        Route::resource('products', ProductController::class);
        Route::resource('users', UserController::class)->except(['show']);
        Route::resource('orders', OrderController::class);
        Route::resource('bahans', BahanController::class)->except(['show']);

        // Stock
        Route::get('/stock', [InventoryController::class, 'index'])->name('stock.index');
        Route::post('/stock/pembelian', [InventoryController::class, 'storePembelian'])->name('stock.pembelian');
        Route::post('/stock/pemakaian', [InventoryController::class, 'storePemakaian'])->name('stock.pemakaian');
        Route::get('/stock/detail/{bahan}', [InventoryController::class, 'detail'])->name('stock.detail');
    });
