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
        // Add indexes to products table for better performance
        Schema::table('products', function (Blueprint $table) {
            $table->index(['store_id', 'is_active'], 'idx_products_store_active');
            $table->index('category_id', 'idx_products_category');
            $table->index('is_active', 'idx_products_active');
        });

        // Add indexes to orders table for better performance
        Schema::table('orders', function (Blueprint $table) {
            $table->index(['user_id', 'status'], 'idx_orders_user_status');
            $table->index('invoice_number', 'idx_orders_invoice');
            $table->index('status', 'idx_orders_status');
        });

        // Add indexes to carts table
        Schema::table('carts', function (Blueprint $table) {
            $table->index(['user_id', 'product_id'], 'idx_carts_user_product');
        });

        // Add index to vouchers table
        Schema::table('vouchers', function (Blueprint $table) {
            $table->index('code', 'idx_vouchers_code');
            $table->index('is_active', 'idx_vouchers_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('idx_products_store_active');
            $table->dropIndex('idx_products_category');
            $table->dropIndex('idx_products_active');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex('idx_orders_user_status');
            $table->dropIndex('idx_orders_invoice');
            $table->dropIndex('idx_orders_status');
        });

        Schema::table('carts', function (Blueprint $table) {
            $table->dropIndex('idx_carts_user_product');
        });

        Schema::table('vouchers', function (Blueprint $table) {
            $table->dropIndex('idx_vouchers_code');
            $table->dropIndex('idx_vouchers_active');
        });
    }
};
