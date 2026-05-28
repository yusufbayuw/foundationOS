<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exam_question_banks', function (Blueprint $table) {
            $table->string('academic_context_type')->default('standalone')->after('organization_id');
            $table->unsignedBigInteger('school_subject_reference')->nullable()->after('academic_context_type');
            $table->string('school_grade_level_reference')->nullable()->after('school_subject_reference');
            $table->unsignedBigInteger('school_curriculum_reference')->nullable()->after('school_grade_level_reference');
            $table->unsignedBigInteger('campus_course_reference')->nullable()->after('school_curriculum_reference');
            $table->unsignedBigInteger('campus_study_program_reference')->nullable()->after('campus_course_reference');
            $table->string('standalone_subject')->nullable()->after('campus_study_program_reference');
            $table->string('standalone_level')->nullable()->after('standalone_subject');
            $table->json('metadata_json')->nullable()->after('standalone_level');
        });

        Schema::table('exam_questions', function (Blueprint $table) {
            $table->renameColumn('stem', 'question_text');
            $table->renameColumn('item_type', 'type');
            $table->renameColumn('points', 'score');
        });

        Schema::table('exam_questions', function (Blueprint $table) {
            $table->string('topic')->nullable()->after('type');
            $table->string('subtopic')->nullable()->after('topic');
            $table->string('difficulty')->nullable()->after('subtopic');
            $table->string('media_path')->nullable()->after('question_text');
            $table->text('explanation')->nullable()->after('correct_answer');
            $table->text('answer_key')->nullable()->after('explanation');
            $table->json('mi_mapping_json')->nullable()->after('metadata_json');
            $table->string('status')->default('draft')->after('mi_mapping_json');
        });

        Schema::create('exam_question_options', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignUuid('exam_question_id')->constrained('exam_questions')->cascadeOnDelete();
            $table->text('option_text');
            $table->boolean('is_correct')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        $this->migrateAnswerOptionsToTable();

        Schema::table('exam_questions', function (Blueprint $table) {
            $table->dropColumn('answer_options');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_question_options');

        Schema::table('exam_questions', function (Blueprint $table) {
            $table->json('answer_options')->nullable();
        });

        Schema::table('exam_questions', function (Blueprint $table) {
            $table->dropColumn([
                'topic',
                'subtopic',
                'difficulty',
                'media_path',
                'explanation',
                'answer_key',
                'mi_mapping_json',
                'status',
            ]);
            $table->renameColumn('question_text', 'stem');
            $table->renameColumn('type', 'item_type');
            $table->renameColumn('score', 'points');
        });

        Schema::table('exam_question_banks', function (Blueprint $table) {
            $table->dropColumn([
                'academic_context_type',
                'school_subject_reference',
                'school_grade_level_reference',
                'school_curriculum_reference',
                'campus_course_reference',
                'campus_study_program_reference',
                'standalone_subject',
                'standalone_level',
                'metadata_json',
            ]);
        });
    }

    protected function migrateAnswerOptionsToTable(): void
    {
        if (! Schema::hasTable('exam_questions') || ! Schema::hasColumn('exam_questions', 'answer_options')) {
            return;
        }

        $questions = DB::table('exam_questions')
            ->whereNotNull('answer_options')
            ->get(['id', 'tenant_id', 'answer_options']);

        foreach ($questions as $question) {
            $options = json_decode($question->answer_options, true);

            if (! is_array($options)) {
                continue;
            }

            $sortOrder = 0;

            foreach ($options as $key => $option) {
                $optionText = is_array($option)
                    ? ($option['text'] ?? $option['label'] ?? json_encode($option))
                    : (string) $option;
                $isCorrect = is_array($option)
                    ? (bool) ($option['is_correct'] ?? $option['correct'] ?? false)
                    : false;

                DB::table('exam_question_options')->insert([
                    'id' => (string) Str::uuid(),
                    'tenant_id' => $question->tenant_id,
                    'exam_question_id' => $question->id,
                    'option_text' => $optionText,
                    'is_correct' => $isCorrect,
                    'sort_order' => is_numeric($key) ? (int) $key : $sortOrder,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $sortOrder++;
            }
        }
    }
};
