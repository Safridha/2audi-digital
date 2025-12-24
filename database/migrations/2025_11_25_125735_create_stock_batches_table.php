<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_batches', function (Blueprint $table) {
            $table->id();

            $table->foreignId('bahan_id')
                  ->constrained('bahans')
                  ->onDelete('cascade');

            $table->date('tanggal_masuk');
            $table->decimal('qty_awal', 12, 2);
            $table->decimal('qty_sisa', 12, 2);
            $table->decimal('harga_satuan', 15, 2);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_batches');
    }
};
