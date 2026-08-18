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
            // Menambahkan field untuk multiple images (JSON array)
            $table->json('images')->nullable()->after('image');

            // Menambahkan field untuk featured product
            $table->boolean('is_featured')->default(false)->after('is_active');

            // Menambahkan field untuk badge (new, sale, bestseller, etc)
            $table->string('badge')->nullable()->after('is_featured');

            // Menambahkan field untuk discount percentage
            $table->integer('discount_percentage')->default(0)->after('price');

            // Menambahkan field untuk rating dan sold count
            $table->decimal('rating', 3, 2)->default(0)->after('discount_percentage');
            $table->integer('sold_count')->default(0)->after('rating');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['images', 'is_featured', 'badge', 'discount_percentage', 'rating', 'sold_count']);
        });
    }
};
