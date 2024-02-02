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
        Schema::table('cart_temp', function (Blueprint $table) {
            $table->dropUnique('cart_temp_product_id_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cart_temp', function (Blueprint $table) {
            $table->unique('product_id', 'cart_temp_product_id_unique');
        });
    }
};
