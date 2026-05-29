<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exam_attempts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignUuid('exam_definition_id')->constrained('exam_definitions')->cascadeOnDelete();
            $table->foreignUuid('exam_participant_id')->constrained('exam_participants')->cascadeOnDelete();
            $table->uuid('runtime_attempt_id');
            $table->unsignedInteger('attempt_number')->default(1);
            $table->string('status')->default('submitted');
            $table->decimal('score', 10, 2)->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->json('metadata_json')->nullable();
            $table->timestamps();

            $table->unique(['exam_definition_id', 'runtime_attempt_id'], 'exam_attempts_exam_runtime_attempt_unique');
            $table->index(['exam_participant_id', 'submitted_at']);
        });

        Schema::create('exam_answers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignUuid('exam_definition_id')->constrained('exam_definitions')->cascadeOnDelete();
            $table->foreignUuid('exam_attempt_id')->constrained('exam_attempts')->cascadeOnDelete();
            $table->foreignUuid('exam_question_id')->nullable()->constrained('exam_questions')->nullOnDelete();
            $table->uuid('runtime_answer_id')->nullable();
            $table->text('answer_value')->nullable();
            $table->boolean('is_correct')->nullable();
            $table->decimal('score', 10, 2)->nullable();
            $table->json('metadata_json')->nullable();
            $table->timestamps();

            $table->unique(['exam_attempt_id', 'runtime_answer_id'], 'exam_answers_attempt_runtime_answer_unique');
            $table->unique(['exam_attempt_id', 'exam_question_id'], 'exam_answers_attempt_question_unique');
        });

        Schema::create('exam_definition_results', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignUuid('exam_definition_id')->constrained('exam_definitions')->cascadeOnDelete();
            $table->foreignUuid('exam_participant_id')->constrained('exam_participants')->cascadeOnDelete();
            $table->foreignUuid('exam_attempt_id')->nullable()->constrained('exam_attempts')->nullOnDelete();
            $table->uuid('runtime_result_id');
            $table->decimal('score', 10, 2)->nullable();
            $table->decimal('max_score', 10, 2)->nullable();
            $table->boolean('is_passed')->nullable();
            $table->string('grade_letter')->nullable();
            $table->string('status')->default('final');
            $table->json('analytics_json')->nullable();
            $table->timestamps();

            $table->unique(['exam_definition_id', 'runtime_result_id'], 'exam_results_exam_runtime_result_unique');
            $table->index(['exam_participant_id']);
        });

        Schema::create('exam_activity_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignUuid('exam_definition_id')->constrained('exam_definitions')->cascadeOnDelete();
            $table->foreignUuid('exam_attempt_id')->constrained('exam_attempts')->cascadeOnDelete();
            $table->foreignUuid('exam_participant_id')->nullable()->constrained('exam_participants')->nullOnDelete();
            $table->uuid('runtime_activity_id');
            $table->string('event_type');
            $table->timestamp('occurred_at')->nullable();
            $table->json('payload_json')->nullable();
            $table->timestamps();

            $table->unique(['exam_attempt_id', 'runtime_activity_id'], 'exam_activity_logs_attempt_runtime_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_activity_logs');
        Schema::dropIfExists('exam_definition_results');
        Schema::dropIfExists('exam_answers');
        Schema::dropIfExists('exam_attempts');
    }
};
