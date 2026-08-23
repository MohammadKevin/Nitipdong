<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Modify role column to VARCHAR(30) so it accepts 'courier', 'super_admin', 'admin', 'seller', 'customer'
        try {
            DB::statement("ALTER TABLE users MODIFY COLUMN role VARCHAR(30) NOT NULL DEFAULT 'customer'");
        } catch (\Throwable $e) {
            // Fallback for non-MySQL or SQLite testing
            if (Schema::hasTable('users')) {
                Schema::table('users', function (Blueprint $table) {
                    $table->string('role', 30)->default('customer')->change();
                });
            }
        }
    }

    public function down(): void
    {
        try {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('super_admin', 'admin', 'seller', 'customer', 'courier') NOT NULL DEFAULT 'customer'");
        } catch (\Throwable $e) {
            //
        }
    }
};
