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
        Schema::table('stores', function (Blueprint $table) {
            if (!Schema::hasColumn('stores', 'city')) {
                $table->string('city')->nullable()->after('address');
            }
            if (!Schema::hasColumn('stores', 'province')) {
                $table->string('province')->nullable()->after('city');
            }
            if (!Schema::hasColumn('stores', 'district')) {
                $table->string('district')->nullable()->after('province');
            }
            if (!Schema::hasColumn('stores', 'postal_code')) {
                $table->string('postal_code', 10)->nullable()->after('district');
            }
            if (!Schema::hasColumn('stores', 'latitude')) {
                $table->decimal('latitude', 10, 7)->nullable()->after('postal_code');
            }
            if (!Schema::hasColumn('stores', 'longitude')) {
                $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->dropColumn([
                'city',
                'province',
                'district',
                'postal_code',
                'latitude',
                'longitude',
            ]);
        });
    }
};
