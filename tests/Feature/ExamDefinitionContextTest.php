<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Str;
use Modules\Campus\Models\Course;
use Modules\Campus\Models\CourseOffering;
use Modules\Campus\Models\Faculty;
use Modules\Campus\Models\StudyProgram;
use Modules\Core\Models\AcademicPeriod;
use Modules\Core\Models\AcademicYear;
use Modules\Core\Models\SubscriptionPlan;
use Modules\Core\Models\Tenant;
use Modules\Exam\Enums\ExamAcademicContext;
use Modules\Exam\Enums\ExamStatus;
use Modules\Exam\Enums\ExamType;
use Modules\Exam\Models\ExamDefinition;
use Modules\School\Models\SchoolClass;
use Modules\School\Models\Subject;
use Tests\TestCase;

class ExamDefinitionContextTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_school_exam_definition_can_be_created_with_references(): void
    {
        $tenant = $this->createTenant();

        $year = AcademicYear::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'name' => '2025/2026',
            'code' => '2526',
            'start_date' => '2025-07-01',
            'end_date' => '2026-06-30',
        ]);

        $period = AcademicPeriod::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'academic_year_id' => $year->id,
            'name' => 'Semester 1',
            'code' => 'S1',
            'type' => 'semester',
            'start_date' => '2025-07-01',
            'end_date' => '2025-12-31',
        ]);

        $subject = Subject::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'name' => 'Physics',
            'code' => 'PHY',
        ]);

        $class = SchoolClass::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'academic_period_id' => $period->id,
            'name' => 'X-A',
            'code' => 'X-A',
        ]);

        $exam = ExamDefinition::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'name' => 'School Midterm',
            'exam_academic_context' => ExamAcademicContext::School,
            'exam_type' => ExamType::Exam,
            'status' => ExamStatus::Draft,
            'academic_year_reference' => $year->id,
            'school_semester_reference' => $period->id,
            'school_class_reference' => $class->id,
            'school_subject_reference' => $subject->id,
            'school_grade_level_reference' => 'X',
        ]);

        $this->assertTrue($exam->isSchool());
        $this->assertSame($period->id, $exam->academic_period_id);
        $this->assertSame($subject->id, $exam->school_subject_reference);
    }

    public function test_campus_exam_definition_can_be_created_with_references(): void
    {
        $tenant = $this->createTenant();

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

        $offering = CourseOffering::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'course_id' => $course->id,
            'class_code' => 'ALG-A',
        ]);

        $exam = ExamDefinition::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'name' => 'Campus Quiz',
            'exam_academic_context' => ExamAcademicContext::Campus,
            'exam_type' => ExamType::Quiz,
            'status' => ExamStatus::Draft,
            'campus_faculty_reference' => $faculty->id,
            'campus_study_program_reference' => $program->id,
            'campus_course_reference' => $course->id,
            'campus_class_reference' => $offering->id,
        ]);

        $this->assertTrue($exam->isCampus());
        $this->assertSame($offering->id, $exam->campus_class_reference);
    }

    public function test_standalone_exam_definition_can_be_created(): void
    {
        $tenant = $this->createTenant();

        $exam = ExamDefinition::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'name' => 'OSN Tryout',
            'exam_academic_context' => ExamAcademicContext::Standalone,
            'exam_type' => ExamType::OsnPrep,
            'status' => ExamStatus::Draft,
            'standalone_subject' => 'Physics',
            'standalone_level' => 'National',
            'target_description' => 'Top 50 students',
        ]);

        $this->assertTrue($exam->isStandalone());
        $this->assertSame('Physics', $exam->standalone_subject);
    }

    protected function createTenant(): Tenant
    {
        $plan = SubscriptionPlan::create([
            'code' => 'exam-def',
            'name' => 'Exam Def',
            'included_modules' => ['core', 'exam'],
        ]);

        return Tenant::create([
            'uuid' => (string) Str::uuid(),
            'code' => 'exam-def-tenant',
            'name' => 'Exam Def Tenant',
            'subscription_plan_id' => $plan->id,
        ]);
    }
}
