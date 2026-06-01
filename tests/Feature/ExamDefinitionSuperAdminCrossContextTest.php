<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Modules\Core\Models\SubscriptionPlan;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\User;
use Modules\Exam\Enums\ExamAcademicContext;
use Modules\Exam\Enums\ExamStatus;
use Modules\Exam\Enums\ExamType;
use Modules\Exam\Enums\QuestionBankStatus;
use Modules\Exam\Enums\QuestionStatus;
use Modules\Exam\Enums\QuestionType;
use Modules\Exam\Models\ExamDefinition;
use Modules\Exam\Models\ExamQuestion;
use Modules\Exam\Models\ExamQuestionBank;
use Modules\Exam\Services\ExamDefinitionQuestionQuery;
use Tests\TestCase;

class ExamDefinitionSuperAdminCrossContextTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_can_attach_cross_context_when_flag_enabled(): void
    {
        $plan = SubscriptionPlan::create([
            'code' => 'exam-sa',
            'name' => 'Exam SA',
            'included_modules' => ['core', 'exam'],
        ]);

        $tenant = Tenant::create([
            'uuid' => (string) Str::uuid(),
            'code' => 'exam-sa-tenant',
            'name' => 'Exam SA Tenant',
            'subscription_plan_id' => $plan->id,
        ]);

        $schoolExam = ExamDefinition::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'name' => 'School Exam',
            'exam_academic_context' => ExamAcademicContext::School,
            'exam_type' => ExamType::Quiz,
            'status' => ExamStatus::Draft,
        ]);

        $campusBank = ExamQuestionBank::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'academic_context_type' => ExamAcademicContext::Campus,
            'name' => 'Campus Bank',
            'status' => QuestionBankStatus::Active,
        ]);

        $campusQuestion = ExamQuestion::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'exam_question_bank_id' => $campusBank->id,
            'question_number' => 1,
            'type' => QuestionType::SingleChoice,
            'question_text' => 'Campus Q',
            'score' => 5,
            'status' => QuestionStatus::Active,
        ]);

        $superAdmin = User::factory()->superAdmin()->create();

        app(ExamDefinitionQuestionQuery::class)->assertQuestionAttachable(
            $schoolExam,
            $campusQuestion,
            $superAdmin,
            includeCrossContext: true,
        );

        $this->assertTrue(true);
    }
}
