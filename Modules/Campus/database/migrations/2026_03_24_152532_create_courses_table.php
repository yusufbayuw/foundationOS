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
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('study_program_id')->nullable();
            $table->string('code');
            $table->string('name');
            $table->unsignedInteger('credits')->default(0);
            $table->unsignedInteger('theory_credits')->default(0);
            $table->unsignedInteger('practicum_credits')->default(0);
            $table->unsignedInteger('semester_level')->nullable();
            $table->string('course_type')->default('mandatory');
            $table->boolean('is_mandatory')->default(true);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['tenant_id', 'code']);
            $table->index('study_program_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
