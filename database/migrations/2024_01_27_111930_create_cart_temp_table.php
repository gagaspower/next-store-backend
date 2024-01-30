<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cart_temp', function (Blueprint $table) {
            $table->id();
            $table->date('cart_date')->default(now());
            $table->bigInteger('product_id');
            $table->integer('product_qty');
            $table->bigInteger('product_variant_stock_id');
            $table->bigInteger('user_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_temp');
    }
};
