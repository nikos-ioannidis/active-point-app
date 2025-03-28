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
        Schema::table('work_types', function (Blueprint $table) {
            $table->decimal('price_gamesa', 10, 2)->nullable()->default(null)->change();
            $table->decimal('price_gamesa_abroad', 10, 2)->nullable()->default(null)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('work_types', function (Blueprint $table) {
            $table->decimal('price_gamesa', 10, 2)->nullable(false)->default(0)->change();
            $table->decimal('price_gamesa_abroad', 10, 2)->nullable(false)->default(0)->change();
        });
    }
};
