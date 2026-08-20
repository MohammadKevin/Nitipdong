<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_addresses', function (Blueprint $table) {
            $table->string('district')->nullable()->after('city'); // Kecamatan
            $table->string('latitude')->nullable()->after('postal_code');
            $table->string('longitude')->nullable()->after('latitude');
            $table->string('notes')->nullable()->after('longitude'); // Patokan / Catatan kurir
        });
    }

    public function down(): void
    {
        Schema::table('user_addresses', function (Blueprint $table) {
            $table->dropColumn(['district', 'latitude', 'longitude', 'notes']);
        });
    }
};
