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
        Schema::table('carts', function (Blueprint $table) {
            $table->string('variant')->nullable()->after('quantity'); // Misal: "Hitam, XL" atau "64GB / Hitam"
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->string('variant')->nullable()->after('price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('carts', function (Blueprint $table) {
            $table->dropColumn('variant');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn('variant');
        });
    }
};
