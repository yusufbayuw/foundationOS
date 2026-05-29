<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exam_definitions', function (Blueprint $table) {
            $table->string('exam_type')->nullable()->after('exam_purpose');
            $table->boolean('shuffle_options')->default(false)->after('shuffle_questions');
            $table->boolean('show_result')->default(false)->after('shuffle_options');
            $table->boolean('show_explanation')->default(false)->after('show_result');

            $table->unsignedBigInteger('academic_year_reference')->nullable()->after('exam_academic_context');
            $table->unsignedBigInteger('school_semester_reference')->nullable()->after('academic_year_reference');
            $table->unsignedBigInteger('school_class_reference')->nullable()->after('school_semester_reference');
            $table->unsignedBigInteger('school_subject_reference')->nullable()->after('school_class_reference');
            $table->string('school_grade_level_reference')->nullable()->after('school_subject_reference');
            $table->unsignedBigInteger('school_teacher_reference')->nullable()->after('school_grade_level_reference');

            $table->unsignedBigInteger('campus_academic_year_reference')->nullable()->after('school_teacher_reference');
            $table->unsignedBigInteger('campus_academic_term_reference')->nullable()->after('campus_academic_year_reference');
            $table->unsignedBigInteger('campus_faculty_reference')->nullable()->after('campus_academic_term_reference');
            $table->unsignedBigInteger('campus_study_program_reference')->nullable()->after('campus_faculty_reference');
            $table->unsignedBigInteger('campus_course_reference')->nullable()->after('campus_study_program_reference');
            $table->unsignedBigInteger('campus_class_reference')->nullable()->after('campus_course_reference');
            $table->unsignedBigInteger('campus_lecturer_reference')->nullable()->after('campus_class_reference');

            $table->string('standalone_subject')->nullable()->after('campus_lecturer_reference');
            $table->string('standalone_level')->nullable()->after('standalone_subject');
            $table->text('target_description')->nullable()->after('standalone_level');
        });

        $this->backfillExamTypeAndReadyStatus();

        Schema::create('exam_definition_questions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignUuid('exam_definition_id')->constrained('exam_definitions')->cascadeOnDelete();
            $table->foreignUuid('exam_question_id')->constrained('exam_questions')->cascadeOnDelete();
            $table->unsignedInteger('sort_order')->default(0);
            $table->decimal('score_override', 10, 2)->nullable();
            $table->timestamps();

            $table->unique(['exam_definition_id', 'exam_question_id'], 'exam_definition_questions_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_definition_questions');

        Schema::table('exam_definitions', function (Blueprint $table) {
            $table->dropColumn([
                'exam_type',
                'shuffle_options',
                'show_result',
                'show_explanation',
                'academic_year_reference',
                'school_semester_reference',
                'school_class_reference',
                'school_subject_reference',
                'school_grade_level_reference',
                'school_teacher_reference',
                'campus_academic_year_reference',
                'campus_academic_term_reference',
                'campus_faculty_reference',
                'campus_study_program_reference',
                'campus_course_reference',
                'campus_class_reference',
                'campus_lecturer_reference',
                'standalone_subject',
                'standalone_level',
                'target_description',
            ]);
        });
    }

    protected function backfillExamTypeAndReadyStatus(): void
    {
        $purposeToType = [
            'quiz' => 'quiz',
            'test' => 'practice',
            'exam' => 'exam',
            'try_out' => 'tryout',
            'placement_test' => 'placement',
            'mi_assessment' => 'mi_assessment',
            'osn_prep' => 'osn_prep',
            'assessment' => 'practice',
            'internal_selection' => 'practice',
            'survey' => 'practice',
        ];

        $definitions = DB::table('exam_definitions')->get(['id', 'exam_purpose', 'status']);

        foreach ($definitions as $definition) {
            $purpose = $definition->exam_purpose;
            $examType = $purpose !== null ? ($purposeToType[$purpose] ?? 'practice') : 'practice';
            $status = $definition->status === 'scheduled' ? 'ready' : $definition->status;

            DB::table('exam_definitions')
                ->where('id', $definition->id)
                ->update([
                    'exam_type' => $examType,
                    'status' => $status,
                ]);
        }
    }
};
