<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Modules\Campus\Models\Course;
use Modules\Campus\Models\Faculty;
use Modules\Campus\Models\StudyProgram;
use Modules\Core\Models\SubscriptionPlan;
use Modules\Core\Models\Tenant;
use Modules\Exam\Enums\ExamAcademicContext;
use Modules\Exam\Enums\QuestionBankStatus;
use Modules\Exam\Models\ExamQuestionBank;
use Modules\School\Models\Curriculum;
use Modules\School\Models\Subject;
use Tests\TestCase;

class ExamQuestionBankContextTest extends TestCase
{
    use RefreshDatabase;

    public function test_school_question_bank_can_be_created(): void
    {
        $tenant = $this->createTenant();

        $subject = Subject::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'name' => 'Physics',
            'code' => 'PHY',
        ]);

        $curriculum = Curriculum::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'name' => 'K13',
            'code' => 'K13',
        ]);

        $bank = ExamQuestionBank::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'academic_context_type' => ExamAcademicContext::School,
            'school_subject_reference' => $subject->id,
            'school_grade_level_reference' => 'X',
            'school_curriculum_reference' => $curriculum->id,
            'name' => 'School Physics Bank',
            'status' => QuestionBankStatus::Active,
        ]);

        $this->assertTrue($bank->isSchool());
        $this->assertSame($subject->id, $bank->school_subject_reference);
        $this->assertSame('X', $bank->school_grade_level_reference);
    }

    public function test_campus_question_bank_can_be_created(): void
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

        $bank = ExamQuestionBank::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'academic_context_type' => ExamAcademicContext::Campus,
            'campus_course_reference' => $course->id,
            'campus_study_program_reference' => $program->id,
            'name' => 'Campus Algorithms Bank',
            'status' => QuestionBankStatus::Active,
        ]);

        $this->assertTrue($bank->isCampus());
        $this->assertSame($course->id, $bank->campus_course_reference);
    }

    public function test_standalone_question_bank_can_be_created(): void
    {
        $tenant = $this->createTenant();

        $bank = ExamQuestionBank::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'academic_context_type' => ExamAcademicContext::Standalone,
            'standalone_subject' => 'OSN Physics',
            'standalone_level' => 'National',
            'name' => 'OSN Prep Bank',
            'status' => QuestionBankStatus::Active,
        ]);

        $this->assertTrue($bank->isStandalone());
        $this->assertSame('OSN Physics', $bank->standalone_subject);
    }

    protected function createTenant(): Tenant
    {
        $plan = SubscriptionPlan::create([
            'code' => 'exam-b2',
            'name' => 'Exam B2',
            'included_modules' => ['core', 'school', 'campus', 'exam'],
        ]);

        return Tenant::create([
            'uuid' => (string) Str::uuid(),
            'code' => 'exam-b2-'.Str::random(4),
            'name' => 'Exam B2 Tenant',
            'subscription_plan_id' => $plan->id,
        ]);
    }
}
