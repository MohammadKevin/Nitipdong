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
        $table->string('invoice_number')->unique();
        $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
        $table->foreignId('store_id')->constrained('stores')->onDelete('cascade');
        $table->decimal('total_amount', 12, 2);
        $table->enum('status', ['pending', 'processing', 'shipped', 'completed', 'cancelled'])->default('pending');
        $table->text('shipping_address');
        $table->string('payment_receipt')->nullable();
        $table->string('tracking_number')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
