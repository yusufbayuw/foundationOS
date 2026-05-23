<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_absences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('requested_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('absence_type')->default('izin');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->text('reason')->nullable();
            $table->string('status')->default('pending');
            $table->foreignId('file_upload_id')->nullable()->constrained('file_uploads')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('student_risk_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('student_id')->unique()->constrained('students')->cascadeOnDelete();
            $table->unsignedTinyInteger('composite_score')->default(0);
            $table->unsignedTinyInteger('academic_score')->default(0);
            $table->unsignedTinyInteger('financial_score')->default(0);
            $table->unsignedTinyInteger('behavioral_score')->default(0);
            $table->unsignedTinyInteger('health_score')->default(0);
            $table->unsignedTinyInteger('attendance_score')->default(0);
            $table->boolean('is_at_risk')->default(false);
            $table->json('meta')->nullable();
            $table->timestamp('computed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('extracurriculars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('organization_id')->nullable()->constrained('organizations')->nullOnDelete();
            $table->string('code')->nullable();
            $table->string('name');
            $table->foreignId('advisor_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status')->default('active');
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('extracurricular_enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('extracurricular_id')->constrained('extracurriculars')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->string('status')->default('active');
            $table->date('enrolled_at')->nullable();
            $table->timestamps();

            $table->unique(['extracurricular_id', 'student_id']);
        });

        Schema::create('competitions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('name');
            $table->string('level')->nullable();
            $table->date('held_at')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('yudisiums', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('organization_id')->nullable()->constrained('organizations')->nullOnDelete();
            $table->foreignId('academic_year_id')->nullable()->constrained('academic_years')->nullOnDelete();
            $table->string('name');
            $table->date('held_at')->nullable();
            $table->string('status')->default('planned');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('wisudas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('yudisium_id')->nullable()->constrained('yudisiums')->nullOnDelete();
            $table->string('name');
            $table->date('held_at')->nullable();
            $table->string('status')->default('planned');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('mbkm_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('collage_student_id')->nullable()->constrained('collage_students')->nullOnDelete();
            $table->string('activity_type');
            $table->string('title');
            $table->unsignedSmallInteger('sks_credited')->default(0);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('lecturer_evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('lecturer_id')->constrained('lecturers')->cascadeOnDelete();
            $table->foreignId('course_offering_id')->nullable()->constrained('course_offerings')->nullOnDelete();
            $table->unsignedTinyInteger('score')->nullable();
            $table->text('feedback')->nullable();
            $table->timestamps();
        });

        $this->enhanceCounselingTables();
    }

    public function down(): void
    {
        Schema::dropIfExists('lecturer_evaluations');
        Schema::dropIfExists('mbkm_activities');
        Schema::dropIfExists('wisudas');
        Schema::dropIfExists('yudisiums');
        Schema::dropIfExists('competitions');
        Schema::dropIfExists('extracurricular_enrollments');
        Schema::dropIfExists('extracurriculars');
        Schema::dropIfExists('student_risk_scores');
        Schema::dropIfExists('student_absences');
    }

    protected function enhanceCounselingTables(): void
    {
        if (! Schema::hasTable('counseling_cases')) {
            return;
        }

        Schema::table('counseling_cases', function (Blueprint $table) {
            if (! Schema::hasColumn('counseling_cases', 'student_id')) {
                $table->foreignId('student_id')->nullable()->after('organization_id')->constrained('students')->nullOnDelete();
            }
            if (! Schema::hasColumn('counseling_cases', 'counselor_id')) {
                $table->foreignId('counselor_id')->nullable()->after('student_id')->constrained('counselors')->nullOnDelete();
            }
            if (! Schema::hasColumn('counseling_cases', 'risk_level')) {
                $table->string('risk_level')->default('low')->after('status');
            }
            if (! Schema::hasColumn('counseling_cases', 'case_target_type')) {
                $table->nullableMorphs('case_target', 'counseling_cases_case_target_index');
            }
        });

        Schema::table('counseling_notes', function (Blueprint $table) {
            if (! Schema::hasColumn('counseling_notes', 'counseling_case_id')) {
                $table->foreignId('counseling_case_id')->nullable()->after('organization_id')->constrained('counseling_cases')->cascadeOnDelete();
            }
            if (! Schema::hasColumn('counseling_notes', 'is_confidential')) {
                $table->boolean('is_confidential')->default(false)->after('status');
            }
            if (! Schema::hasColumn('counseling_notes', 'body')) {
                $table->text('body')->nullable()->after('is_confidential');
            }
        });

        Schema::table('counseling_sessions', function (Blueprint $table) {
            if (! Schema::hasColumn('counseling_sessions', 'counseling_case_id')) {
                $table->foreignId('counseling_case_id')->nullable()->after('organization_id')->constrained('counseling_cases')->cascadeOnDelete();
            }
            if (! Schema::hasColumn('counseling_sessions', 'scheduled_at')) {
                $table->timestamp('scheduled_at')->nullable()->after('status');
            }
        });
    }
};
