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
        Schema::create('sale_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users'); // user who created the product
            $table->text('description')->nullable();
            $table->decimal('total_price', 10, 2)->default(0);
            $table->string('status')->default('pending'); // pending, confirmed, cancelled
            $table->timestamps();
            $table->softDeletes(); // allow soft deletes so no products are deleted
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sale_orders');
    }
};
