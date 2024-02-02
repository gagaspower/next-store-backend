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
        Schema::create('order_payment', function (Blueprint $table) {
            $table->id();
            $table->string('order_id');
            $table->string('order_bank');
            $table->string('payment_transaction_id');
            $table->string('payment_provider');
            $table->string('payment_merchant_id');
            $table->bigInteger('payment_gross_amount');
            $table->string('payment_type');
            $table->dateTime('payment_datetime', $precision = 0);
            $table->string('payment_status');
            $table->string('payment_va_numbers');
            $table->timestamps();

            $table->index('order_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_payment');
    }
};
