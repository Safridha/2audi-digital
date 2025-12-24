<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bahan_usages', function (Blueprint $table) {
            $table->id();

            $table->foreignId('bahan_id')
                  ->constrained('bahans')
                  ->onDelete('cascade');

            $table->date('tanggal');
            $table->decimal('qty', 12, 2);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bahan_usages');
    }
};
