<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->nullable()->constrained('stores')->onDelete('cascade');
            $table->string('code')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('type', ['percent', 'fixed'])->default('percent');
            $table->decimal('amount', 12, 2);
            $table->decimal('min_spend', 12, 2)->default(0);
            $table->decimal('max_discount', 12, 2)->nullable();
            $table->integer('quota')->default(100);
            $table->boolean('is_active')->default(true);
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vouchers');
    }
};
