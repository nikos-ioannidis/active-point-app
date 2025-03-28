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
        Schema::create('work_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_category_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->decimal('price_standard', 10, 2)->default(0);
            $table->decimal('price_gamesa', 10, 2)->default(0);
            $table->decimal('price_gamesa_abroad', 10, 2)->default(0);
            $table->integer('max_hours')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_types');
    }
};
