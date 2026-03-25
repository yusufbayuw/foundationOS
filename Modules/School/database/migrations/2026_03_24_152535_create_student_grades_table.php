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
        Schema::create('student_grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('assessment_id')->constrained('assessments')->cascadeOnDelete();
            $table->foreignId('graded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->decimal('score', 8, 2);
            $table->string('score_letter')->nullable();
            $table->decimal('weight', 8, 2)->default(1);
            $table->decimal('final_score', 8, 2)->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_passed')->nullable();
            $table->timestamp('graded_at')->nullable();
            $table->boolean('is_locked')->default(false);
            $table->unique(['student_id', 'assessment_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_grades');
    }
};
