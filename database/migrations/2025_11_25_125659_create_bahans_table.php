<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bahans', function (Blueprint $table) {
            $table->id();
            $table->string('nama_bahan');
            $table->string('satuan')->default('meter');

            // stok total bahan 
            $table->decimal('stok', 12, 2)->default(0);

            $table->integer('minimal_stock')->default(0);

            // penanda supaya WA tidak spam
            $table->boolean('low_stock_notified')->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bahans');
    }
};
