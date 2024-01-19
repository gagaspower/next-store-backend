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
        Schema::create('product_varian_stock', function (Blueprint $table) {
            $table->id();
            $table->string('product_varian_name');
            $table->integer('product_varian_stock');
            $table->integer('product_varian_price');
            $table->string('product_varian_sku')->nullable();
            $table->unsignedBigInteger('product_id');
            $table->timestamps();

            $table->index(['product_varian_sku', 'product_id']);
            $table->foreign('product_id')->references('id')->on('product');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_varian_stock');
    }
};
