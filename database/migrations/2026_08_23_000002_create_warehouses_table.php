<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('warehouses')) {
            Schema::create('warehouses', function (Blueprint $table) {
                $table->id();
                $table->string('code')->unique(); // e.g. DC-SBY-01, DC-JKT-01
                $table->string('name'); // e.g. NitipDong Hub DC Surabaya
                $table->string('city'); // e.g. Surabaya
                $table->string('province'); // e.g. Jawa Timur
                $table->text('address');
                $table->decimal('lat', 10, 7)->default(-7.2575);
                $table->decimal('lng', 10, 7)->default(112.7521);
                $table->string('phone')->nullable();
                $table->string('pic_name')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // Add warehouse_id to orders table if not present
        if (Schema::hasTable('orders') && !Schema::hasColumn('orders', 'warehouse_id')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->foreignId('warehouse_id')->nullable()->after('courier_id')->constrained('warehouses')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('orders') && Schema::hasColumn('orders', 'warehouse_id')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropForeign(['warehouse_id']);
                $table->dropColumn('warehouse_id');
            });
        }
        Schema::dropIfExists('warehouses');
    }
};
