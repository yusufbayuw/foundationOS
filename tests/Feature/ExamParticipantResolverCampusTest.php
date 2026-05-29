<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Modules\Campus\Models\CollageStudent;
use Modules\Campus\Models\Course;
use Modules\Campus\Models\CourseOffering;
use Modules\Campus\Models\Faculty;
use Modules\Campus\Models\StudyPlan;
use Modules\Campus\Models\StudyPlanItem;
use Modules\Campus\Models\StudyProgram;
use Modules\Core\Models\AcademicPeriod;
use Modules\Core\Models\AcademicYear;
use Modules\Core\Models\SubscriptionPlan;
use Modules\Core\Models\Tenant;
use Modules\Exam\Enums\ExamAcademicContext;
use Modules\Exam\Enums\ExamStatus;
use Modules\Exam\Enums\ParticipantSource;
use Modules\Exam\Models\ExamDefinition;
use Modules\Exam\Models\ExamParticipant;
use Modules\Exam\Services\ExamParticipantResolver;
use Tests\TestCase;

class ExamParticipantResolverCampusTest extends TestCase
{
    use RefreshDatabase;

    public function test_resolve_from_campus_class_creates_participants_from_study_plan_items(): void
    {
        $tenant = $this->createTenant();
        $offering = $this->createCourseOffering($tenant);
        $campusStudent = CollageStudent::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'full_name' => 'Ani Campus',
            'student_number' => 'CS-9001',
            'email' => 'ani@campus.test',
        ]);

        $period = AcademicPeriod::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'academic_year_id' => AcademicYear::withoutTenantScope()->create([
                'tenant_id' => $tenant->id,
                'name' => '2025/2026',
                'code' => '2526',
                'start_date' => '2025-07-01',
                'end_date' => '2026-06-30',
            ])->id,
            'name' => 'Semester 1',
            'code' => 'S1',
            'start_date' => '2025-07-01',
            'end_date' => '2025-12-31',
        ]);

        $plan = StudyPlan::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'collage_student_id' => $campusStudent->id,
            'academic_period_id' => $period->id,
            'plan_number' => 'SP-001',
            'status' => 'approved',
        ]);

        StudyPlanItem::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'study_plan_id' => $plan->id,
            'course_offering_id' => $offering->id,
            'course_id' => $offering->course_id,
            'credits' => 3,
            'status' => 'enrolled',
        ]);

        $exam = ExamDefinition::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'name' => 'Campus Quiz',
            'exam_academic_context' => ExamAcademicContext::Campus,
            'status' => ExamStatus::Draft,
            'campus_class_reference' => $offering->id,
        ]);

        $result = app(ExamParticipantResolver::class)->resolveFromCampusClass($exam);

        $this->assertSame(1, $result['created']);

        $participant = ExamParticipant::withoutTenantScope()->first();
        $this->assertNotNull($participant);
        $this->assertSame('Ani Campus', $participant->student_name);
        $this->assertSame('CS-9001', $participant->student_identifier);
        $this->assertSame(ParticipantSource::CampusStudent, $participant->participant_source);
        $this->assertSame($campusStudent->id, $participant->campus_student_reference);
    }

    protected function createTenant(): Tenant
    {
        $plan = SubscriptionPlan::create([
            'code' => 'exam-b5-campus',
            'name' => 'Exam B5 Campus',
            'included_modules' => ['core', 'campus', 'exam'],
        ]);

        return Tenant::create([
            'uuid' => (string) Str::uuid(),
            'code' => 'b5-campus',
            'name' => 'B5 Campus Tenant',
            'subscription_plan_id' => $plan->id,
        ]);
    }

    protected function createCourseOffering(Tenant $tenant): CourseOffering
    {
        $faculty = Faculty::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'name' => 'Engineering',
            'code' => 'ENG',
        ]);

        $program = StudyProgram::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'faculty_id' => $faculty->id,
            'name' => 'Informatics',
            'code' => 'IF',
        ]);

        $course = Course::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'study_program_id' => $program->id,
            'name' => 'Algorithms',
            'code' => 'ALG',
        ]);

        return CourseOffering::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'course_id' => $course->id,
            'class_code' => 'ALG-A',
        ]);
    }
}
