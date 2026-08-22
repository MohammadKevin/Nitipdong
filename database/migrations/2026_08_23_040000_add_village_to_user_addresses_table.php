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
        Schema::table('user_addresses', function (Blueprint $table) {
            if (!Schema::hasColumn('user_addresses', 'village')) {
                $table->string('village')->nullable()->after('district'); // Kelurahan / Desa
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_addresses', function (Blueprint $table) {
            if (Schema::hasColumn('user_addresses', 'village')) {
                $table->dropColumn('village');
            }
        });
    }
};
