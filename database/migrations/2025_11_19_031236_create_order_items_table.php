<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('order_id')
                ->constrained('orders')
                ->cascadeOnDelete();

            $table->foreignId('product_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->decimal('length', 8, 2)->nullable();
            $table->decimal('width', 8, 2)->nullable();
            $table->decimal('area', 10, 2)->nullable();
            $table->unsignedInteger('quantity')->default(1);

            $table->string('finishing')->default('tanpa');

            $table->integer('product_price')->default(0);
            $table->integer('finishing_rate')->default(0);

            $table->bigInteger('printing_cost')->default(0);
            $table->bigInteger('finishing_cost')->default(0);
            $table->bigInteger('line_total')->default(0);

            $table->string('design_file')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
