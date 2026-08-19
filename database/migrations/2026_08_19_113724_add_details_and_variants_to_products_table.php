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
        Schema::table('products', function (Blueprint $table) {
            $table->json('specifications')->nullable()->after('description'); // Detail specs produk
            $table->json('variants')->nullable()->after('specifications'); // Variant options (warna, ukuran, dll)
            $table->decimal('weight', 8, 2)->nullable()->after('stock'); // Berat dalam kg
            $table->string('condition')->default('new')->after('weight'); // new atau used
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['specifications', 'variants', 'weight', 'condition']);
        });
    }
};
