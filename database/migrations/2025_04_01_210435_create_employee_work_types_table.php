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
        Schema::create('employee_work_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->foreignId('work_category_id')->constrained()->onDelete('cascade');
            $table->foreignId('work_type_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamps();

            // Ensure each employee can have only one work type per category
            $table->unique(['employee_id', 'work_category_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_work_types');
    }
};
