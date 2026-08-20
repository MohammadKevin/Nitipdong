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
        Schema::table('orders', function (Blueprint $table) {
            $table->string('shipping_courier')->nullable()->after('shipping_address'); // JNE, J&T, SICEPAT, POS, GOSEND
            $table->string('shipping_service')->nullable()->after('shipping_courier'); // REG, YES, BEST, etc.
            $table->decimal('shipping_cost', 12, 2)->default(0)->after('shipping_service'); // Biaya ongkir
            $table->decimal('total_weight', 8, 2)->default(1.0)->after('shipping_cost'); // Total berat dalam kg
            $table->string('payment_method')->default('manual_transfer')->after('total_amount'); // qris, va_bca, va_mandiri, va_bni, va_bri, manual_transfer
            $table->string('payment_reference')->nullable()->after('payment_method'); // ID transaksi gateway
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'shipping_courier',
                'shipping_service',
                'shipping_cost',
                'total_weight',
                'payment_method',
                'payment_reference',
            ]);
        });
    }
};
