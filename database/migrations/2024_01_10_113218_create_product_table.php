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
        Schema::create('product', function (Blueprint $table) {
            $table->id();
            $table->string('product_name');
            $table->string('product_slug');
            $table->string('product_sku');
            $table->unsignedBigInteger('product_category_id');
            $table->longText('product_desc');
            $table->unsignedInteger('product_stock');
            $table->unsignedInteger('product_price');
            $table->integer('product_weight');
            $table->string('product_image');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();
            $table->unique('product_sku');
            $table->index(['product_category_id', 'product_name']);
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('product_category_id')->references('id')->on('category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product');
    }
};
