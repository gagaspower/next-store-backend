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
        Schema::create('users_address', function (Blueprint $table) {
            $table->id();
            $table->text('address');
            $table->integer('user_address_prov_id');
            $table->integer('user_address_kab_id');
            $table->string('user_address_kodepos');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();

            $table->index(['user_address_prov_id', 'user_address_kab_id']);
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users_address');
    }
};
