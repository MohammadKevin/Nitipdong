<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $tables = ['users', 'stores', 'products', 'orders'];

        foreach ($tables as $table) {
            if (Schema::hasTable($table) && !Schema::hasColumn($table, 'uuid')) {
                Schema::table($table, function (Blueprint $t) {
                    $t->uuid('uuid')->nullable()->after('id')->index();
                });

                // Populate existing records with UUIDs
                $records = DB::table($table)->whereNull('uuid')->get();
                foreach ($records as $record) {
                    DB::table($table)->where('id', $record->id)->update([
                        'uuid' => (string) Str::uuid(),
                    ]);
                }

                // Add unique constraint after population
                Schema::table($table, function (Blueprint $t) {
                    $t->unique('uuid');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = ['users', 'stores', 'products', 'orders'];

        foreach ($tables as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'uuid')) {
                Schema::table($table, function (Blueprint $t) {
                    $t->dropColumn('uuid');
                });
            }
        }
    }
};
