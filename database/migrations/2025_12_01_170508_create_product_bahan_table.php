<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('product_bahan', function (Blueprint $table) {
            $table->id();

            $table->foreignId('product_id')
                  ->constrained('products')
                  ->onDelete('cascade');

            $table->foreignId('bahan_id')
                  ->constrained('bahans')
                  ->onDelete('cascade');

            // Berapa banyak bahan ini per 1 unit produk
            $table->decimal('qty_per_unit', 12, 3);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_bahan');
    }
};
