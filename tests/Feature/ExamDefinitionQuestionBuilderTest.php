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
use Modules\Exam\Models\ExamDefinitionQuestion;
use Modules\Exam\Models\ExamQuestion;
use Modules\Exam\Models\ExamQuestionBank;
use Modules\Exam\Services\ExamDefinitionQuestionQuery;
use Modules\Exam\Services\ExamDefinitionScoreCalculator;
use Tests\TestCase;

class ExamDefinitionQuestionBuilderTest extends TestCase
{
    use RefreshDatabase;

    public function test_context_scoped_picker_excludes_other_context_banks(): void
    {
        [$tenant, $schoolExam, $schoolQuestion, $campusQuestion] = $this->seedQuestions();

        $user = User::factory()->create(['is_super_admin' => false]);

        $pickerIds = app(ExamDefinitionQuestionQuery::class)
            ->forPicker($schoolExam, $user)
            ->pluck('id')
            ->all();

        $this->assertContains($schoolQuestion->id, $pickerIds);
        $this->assertNotContains($campusQuestion->id, $pickerIds);
    }

    public function test_attach_rejects_mismatched_context(): void
    {
        [$tenant, $schoolExam, $schoolQuestion, $campusQuestion] = $this->seedQuestions();
        $user = User::factory()->create(['is_super_admin' => false]);

        $this->expectException(\InvalidArgumentException::class);

        app(ExamDefinitionQuestionQuery::class)->assertQuestionAttachable(
            $schoolExam,
            $campusQuestion,
            $user,
        );
    }

    public function test_pivot_items_support_sort_order_score_override_and_totals(): void
    {
        [$tenant, $schoolExam, $schoolQuestion] = array_slice($this->seedQuestions(), 0, 3);

        ExamDefinitionQuestion::query()->create([
            'tenant_id' => $tenant->id,
            'exam_definition_id' => $schoolExam->id,
            'exam_question_id' => $schoolQuestion->id,
            'sort_order' => 1,
            'score_override' => 10,
        ]);

        $calculator = app(ExamDefinitionScoreCalculator::class);

        $this->assertSame(1, $calculator->questionCount($schoolExam));
        $this->assertEqualsWithDelta(10.0, $calculator->totalScore($schoolExam), 0.001);
        $this->assertTrue(Str::isUuid($schoolExam->examDefinitionQuestions()->first()->id));
    }

    /**
     * @return array{0: Tenant, 1: ExamDefinition, 2: ExamQuestion, 3: ExamQuestion}
     */
    protected function seedQuestions(): array
    {
        $plan = SubscriptionPlan::create([
            'code' => 'exam-builder',
            'name' => 'Exam Builder',
            'included_modules' => ['core', 'exam'],
        ]);

        $tenant = Tenant::create([
            'uuid' => (string) Str::uuid(),
            'code' => 'exam-builder-tenant',
            'name' => 'Exam Builder Tenant',
            'subscription_plan_id' => $plan->id,
        ]);

        $schoolBank = ExamQuestionBank::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'academic_context_type' => ExamAcademicContext::School,
            'name' => 'School Bank',
            'status' => QuestionBankStatus::Active,
        ]);

        $campusBank = ExamQuestionBank::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'academic_context_type' => ExamAcademicContext::Campus,
            'name' => 'Campus Bank',
            'status' => QuestionBankStatus::Active,
        ]);

        $schoolQuestion = ExamQuestion::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'exam_question_bank_id' => $schoolBank->id,
            'question_number' => 1,
            'type' => QuestionType::SingleChoice,
            'question_text' => 'School Q1',
            'score' => 5,
            'status' => QuestionStatus::Active,
        ]);

        $campusQuestion = ExamQuestion::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'exam_question_bank_id' => $campusBank->id,
            'question_number' => 1,
            'type' => QuestionType::SingleChoice,
            'question_text' => 'Campus Q1',
            'score' => 5,
            'status' => QuestionStatus::Active,
        ]);

        $schoolExam = ExamDefinition::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'name' => 'School Exam',
            'exam_academic_context' => ExamAcademicContext::School,
            'exam_type' => ExamType::Quiz,
            'status' => ExamStatus::Draft,
        ]);

        return [$tenant, $schoolExam, $schoolQuestion, $campusQuestion];
    }
}
