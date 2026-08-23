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
            if (!Schema::hasColumn('orders', 'courier_id')) {
                $table->foreignId('courier_id')->nullable()->constrained('users')->nullOnDelete()->after('store_id');
            }
            if (!Schema::hasColumn('orders', 'courier_lat')) {
                $table->decimal('courier_lat', 10, 7)->nullable()->after('shipping_service');
            }
            if (!Schema::hasColumn('orders', 'courier_lng')) {
                $table->decimal('courier_lng', 10, 7)->nullable()->after('courier_lat');
            }
            if (!Schema::hasColumn('orders', 'courier_location_updated_at')) {
                $table->timestamp('courier_location_updated_at')->nullable()->after('courier_lng');
            }
            if (!Schema::hasColumn('orders', 'delivery_proof_image')) {
                $table->string('delivery_proof_image')->nullable()->after('payment_proof');
            }
            if (!Schema::hasColumn('orders', 'delivery_notes')) {
                $table->text('delivery_notes')->nullable()->after('delivery_proof_image');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('courier_id');
            $table->dropColumn([
                'courier_lat',
                'courier_lng',
                'courier_location_updated_at',
                'delivery_proof_image',
                'delivery_notes',
            ]);
        });
    }
};
