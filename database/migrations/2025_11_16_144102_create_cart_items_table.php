<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('product_id')
                ->constrained()
                ->cascadeOnDelete();

            // Jumlah
            $table->unsignedInteger('quantity')->default(1);

            // Detail cetakan
            $table->decimal('length', 8, 2)->nullable();   
            $table->decimal('width', 8, 2)->nullable();    
            $table->decimal('area', 10, 2)->nullable();    
            $table->string('finishing')->nullable();       
            $table->text('note')->nullable();
            $table->string('design_file')->nullable();     
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};
