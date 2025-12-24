<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Auth;
use App\Models\Category;
use App\Models\CartItem;
use App\Models\StockBatch;
use App\Observers\StockBatchObserver;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //  Observer untuk FIFO → setiap batch berubah, stok bahan dihitung ulang
        StockBatch::observe(StockBatchObserver::class);
        View::composer('*', function ($view) {

            // Kategori (aman kalau tabel belum dimigrasi)
            $categories = Schema::hasTable('categories')
                ? Category::orderBy('name')->get()
                : collect();

            // Hitung jumlah item keranjang dari DB per user
            $cartCount = 0;
            if (Auth::check() && Schema::hasTable('cart_items')) {
                $cartCount = CartItem::where('user_id', Auth::id())
                    ->sum('quantity'); 
            }

            $view->with([
                'categories' => $categories,
                'cartCount'  => $cartCount,
            ]);
        });
    }
}
