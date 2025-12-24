<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            // DATA PEMESAN
            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('customer_phone', 20);
            $table->text('address');
            $table->string('district');
            $table->string('city');
            $table->string('postal_code');
            $table->text('note')->nullable();

            // PENGIRIMAN
            $table->string('shipping_option');              
            $table->string('shipping_courier')->nullable();  
            $table->string('shipping_service')->nullable();  
            $table->string('shipping_etd')->nullable();      
            $table->bigInteger('shipping_cost')->default(0);

            // PEMBAYARAN
            $table->string('payment_option');                
            $table->string('payment_method')->nullable();    
            $table->string('snap_token')->nullable();

            // TOTAL
            $table->bigInteger('total_payment')->default(0); // total barang (cetak + finishing)
            $table->bigInteger('grand_total')->default(0);   // total_payment + shipping

            // STATUS
            $table->string('status')->default('menunggu_pembayaran');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
