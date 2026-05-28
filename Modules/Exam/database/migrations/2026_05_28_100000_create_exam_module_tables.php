<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exam_definitions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('organization_id')->nullable()->constrained('organizations')->nullOnDelete();
            $table->foreignId('academic_period_id')->nullable()->constrained('academic_periods')->nullOnDelete();
            $table->foreignId('owner_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('school_assessment_id')->nullable()->constrained('assessments')->nullOnDelete();
            $table->string('exam_academic_context');
            $table->string('exam_purpose')->nullable();
            $table->string('context_reference_type')->nullable();
            $table->unsignedBigInteger('context_reference_id')->nullable();
            $table->uuid('context_reference_uuid')->nullable();
            $table->json('metadata_json')->nullable();
            $table->string('grade_sync_mode')->default('none');
            $table->string('grade_sync_target')->nullable();
            $table->string('name');
            $table->string('code')->nullable();
            $table->text('description')->nullable();
            $table->string('status')->default('draft');
            $table->decimal('max_score', 10, 2)->nullable();
            $table->decimal('passing_score', 10, 2)->nullable();
            $table->unsignedInteger('duration_minutes')->nullable();
            $table->unsignedInteger('max_attempts')->nullable();
            $table->boolean('shuffle_questions')->default(false);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->string('runtime_exam_id')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamp('last_published_at')->nullable();
            $table->unique(['tenant_id', 'code']);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('exam_question_banks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('organization_id')->nullable()->constrained('organizations')->nullOnDelete();
            $table->string('name');
            $table->string('code')->nullable();
            $table->text('description')->nullable();
            $table->string('status')->default('active');
            $table->unique(['tenant_id', 'code']);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('exam_questions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignUuid('exam_question_bank_id')->constrained('exam_question_banks')->cascadeOnDelete();
            $table->unsignedInteger('question_number')->default(1);
            $table->string('item_type')->default('multiple_choice');
            $table->text('stem')->nullable();
            $table->json('answer_options')->nullable();
            $table->text('correct_answer')->nullable();
            $table->decimal('points', 10, 2)->default(1);
            $table->json('metadata_json')->nullable();
            $table->unique(['exam_question_bank_id', 'question_number']);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('exam_packages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignUuid('exam_definition_id')->constrained('exam_definitions')->cascadeOnDelete();
            $table->foreignUuid('exam_question_bank_id')->nullable()->constrained('exam_question_banks')->nullOnDelete();
            $table->string('name');
            $table->string('code')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->json('settings_json')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('exam_participants', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignUuid('exam_definition_id')->constrained('exam_definitions')->cascadeOnDelete();
            $table->string('display_name');
            $table->string('email')->nullable();
            $table->string('participant_code')->nullable();
            $table->string('context_reference_type')->nullable();
            $table->unsignedBigInteger('context_reference_id')->nullable();
            $table->uuid('context_reference_uuid')->nullable();
            $table->json('metadata_json')->nullable();
            $table->string('status')->default('registered');
            $table->unique(['exam_definition_id', 'participant_code']);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('exam_tokens', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignUuid('exam_participant_id')->constrained('exam_participants')->cascadeOnDelete();
            $table->string('token');
            $table->timestamp('expires_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_used_at')->nullable();
            $table->unique(['exam_participant_id', 'token']);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('exam_publish_snapshots', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignUuid('exam_definition_id')->constrained('exam_definitions')->cascadeOnDelete();
            $table->unsignedInteger('version')->default(1);
            $table->json('payload_json');
            $table->string('runtime_exam_id')->nullable();
            $table->string('publish_status')->default('pending');
            $table->text('error_message')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });

        Schema::create('exam_attempt_syncs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignUuid('exam_definition_id')->constrained('exam_definitions')->cascadeOnDelete();
            $table->foreignUuid('exam_participant_id')->constrained('exam_participants')->cascadeOnDelete();
            $table->string('runtime_attempt_id')->nullable();
            $table->string('sync_status')->default('pending');
            $table->decimal('score', 10, 2)->nullable();
            $table->json('result_json')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('synced_at')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();
        });

        Schema::create('exam_manual_scores', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignUuid('exam_definition_id')->constrained('exam_definitions')->cascadeOnDelete();
            $table->foreignUuid('exam_participant_id')->constrained('exam_participants')->cascadeOnDelete();
            $table->foreignId('graded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->decimal('score', 10, 2)->nullable();
            $table->text('feedback')->nullable();
            $table->string('status')->default('pending');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_manual_scores');
        Schema::dropIfExists('exam_attempt_syncs');
        Schema::dropIfExists('exam_publish_snapshots');
        Schema::dropIfExists('exam_tokens');
        Schema::dropIfExists('exam_participants');
        Schema::dropIfExists('exam_packages');
        Schema::dropIfExists('exam_questions');
        Schema::dropIfExists('exam_question_banks');
        Schema::dropIfExists('exam_definitions');
    }
};
