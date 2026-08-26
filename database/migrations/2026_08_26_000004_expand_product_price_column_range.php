<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE `products` MODIFY `price` DECIMAL(16, 2) NOT NULL DEFAULT 0');
            DB::statement('ALTER TABLE `products` MODIFY `weight` DECIMAL(10, 2) NULL DEFAULT 0');

            if (Schema::hasTable('order_items')) {
                DB::statement('ALTER TABLE `order_items` MODIFY `price` DECIMAL(16, 2) NOT NULL DEFAULT 0');
            }

            if (Schema::hasTable('orders')) {
                DB::statement('ALTER TABLE `orders` MODIFY `total_amount` DECIMAL(16, 2) NOT NULL DEFAULT 0');
            }
        } else {
            Schema::table('products', function (Blueprint $table) {
                $table->decimal('price', 16, 2)->default(0)->change();
                $table->decimal('weight', 10, 2)->nullable()->change();
            });

            if (Schema::hasTable('order_items')) {
                Schema::table('order_items', function (Blueprint $table) {
                    $table->decimal('price', 16, 2)->default(0)->change();
                });
            }

            if (Schema::hasTable('orders')) {
                Schema::table('orders', function (Blueprint $table) {
                    $table->decimal('total_amount', 16, 2)->default(0)->change();
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE `products` MODIFY `price` DECIMAL(12, 2) NOT NULL DEFAULT 0');
        }
    }
};
