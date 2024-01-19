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
        Schema::table('product', function (Blueprint $table) {
            $table->string('product_meta_title')->nullable();
            $table->text('product_meta_desc')->nullable();
            $table->string('product_meta_tag')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product', function (Blueprint $table) {
            $table->dropColumn('product_meta_title');
            $table->dropColumn('product_meta_desc');
            $table->dropColumn('product_meta_tag');
        });
    }
};
