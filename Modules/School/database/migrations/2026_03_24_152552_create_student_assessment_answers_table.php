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
        Schema::create('student_assessment_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('assessment_id')->constrained('assessments')->cascadeOnDelete();
            $table->foreignId('assessment_item_id')->constrained('assessment_items')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('class_student_id')->nullable()->constrained('class_students')->nullOnDelete();
            $table->foreignId('graded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('answer_text')->nullable();
            $table->string('answer_selected')->nullable();
            $table->string('answer_attachment')->nullable();
            $table->decimal('score', 8, 2)->nullable();
            $table->decimal('max_score', 8, 2)->nullable();
            $table->boolean('is_correct')->nullable();
            $table->text('grader_notes')->nullable();
            $table->timestamp('graded_at')->nullable();
            $table->unsignedInteger('attempt_number')->default(1);
            $table->unsignedInteger('time_spent_seconds')->nullable();
            $table->unique(['assessment_item_id', 'student_id', 'attempt_number'], 'assessment_item_student_attempt_unique');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_assessment_answers');
    }
};
