<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Modules\Core\Models\AcademicPeriod;
use Modules\Core\Models\AcademicYear;
use Modules\Core\Models\SubscriptionPlan;
use Modules\Core\Models\Tenant;
use Modules\Exam\Enums\ExamAcademicContext;
use Modules\Exam\Enums\ExamStatus;
use Modules\Exam\Enums\GradeSyncMode;
use Modules\Exam\Models\ExamAttemptSync;
use Modules\Exam\Models\ExamDefinition;
use Modules\Exam\Models\ExamParticipant;
use Modules\Exam\Services\SchoolGradeBridgeService;
use Modules\School\Models\Assessment;
use Modules\School\Models\SchoolClass;
use Modules\School\Models\Student;
use Modules\School\Models\StudentGrade;
use Modules\School\Models\Subject;
use Tests\TestCase;

class ExamSchoolBridgeTest extends TestCase
{
    use RefreshDatabase;

    public function test_school_grade_bridge_upserts_student_grade_when_assessment_linked(): void
    {
        $plan = SubscriptionPlan::create([
            'code' => 'exam-bridge',
            'name' => 'Exam Bridge',
            'included_modules' => ['core', 'school', 'exam'],
        ]);

        $tenant = Tenant::create([
            'uuid' => (string) Str::uuid(),
            'code' => 'bridge-tenant',
            'name' => 'Bridge Tenant',
            'subscription_plan_id' => $plan->id,
        ]);

        $year = AcademicYear::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'name' => '2025/2026',
            'code' => '2025',
            'start_date' => '2025-07-01',
            'end_date' => '2026-06-30',
        ]);

        $period = AcademicPeriod::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'academic_year_id' => $year->id,
            'name' => 'Semester 1',
            'code' => 'S1',
            'start_date' => '2025-07-01',
            'end_date' => '2025-12-31',
        ]);

        $subject = Subject::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'name' => 'Mathematics',
            'code' => 'MAT',
        ]);

        $class = SchoolClass::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'academic_period_id' => $period->id,
            'name' => 'X-A',
            'code' => 'X-A',
        ]);

        $assessment = Assessment::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'academic_period_id' => $period->id,
            'subject_id' => $subject->id,
            'class_id' => $class->id,
            'name' => 'UTS Mathematics',
            'code' => 'UTS-MAT',
        ]);

        $student = Student::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'nis' => '99001',
            'nisn' => '9900100001',
        ]);

        $definition = ExamDefinition::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'name' => 'Cloud UTS',
            'exam_academic_context' => ExamAcademicContext::School,
            'school_assessment_id' => $assessment->id,
            'grade_sync_mode' => GradeSyncMode::PushToStudentGrade,
            'passing_score' => 70,
            'status' => ExamStatus::Published,
        ]);

        $participant = ExamParticipant::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'exam_definition_id' => $definition->id,
            'student_name' => 'Student 99001',
            'participant_source' => 'school_student',
            'school_student_reference' => $student->id,
            'context_reference_type' => Student::class,
            'participant_legacy_id' => $student->id,
        ]);

        $attemptSync = ExamAttemptSync::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'exam_definition_id' => $definition->id,
            'exam_participant_id' => $participant->id,
            'score' => 85,
            'sync_status' => 'received',
        ]);

        app(SchoolGradeBridgeService::class)->syncAttempt($definition, $attemptSync);

        $this->assertDatabaseHas('student_grades', [
            'student_id' => $student->id,
            'assessment_id' => $assessment->id,
            'score' => 85,
        ]);

        $grade = StudentGrade::query()->where('student_id', $student->id)->first();
        $this->assertTrue($grade?->is_passed);
        $this->assertSame('grade_synced', $attemptSync->fresh()?->sync_status);
    }
}
