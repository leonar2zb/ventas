<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sale_order_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_order_id')->constrained('sale_orders')->onDelete('cascade'); // sale order id
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade'); // product id
            $table->decimal('quantity', 10, 3)->default(1); // quantity of the product
            $table->decimal('unit_price', 10, 2)->default(0); // price of the product at the time of sale
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sale_order_details');
    }
};
