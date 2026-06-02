<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Modules\Core\Models\SubscriptionPlan;
use Modules\Core\Models\Tenant;
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
use Modules\Exam\Services\ExamLifecycleService;
use Modules\Exam\Services\ExamPublishService;
use Tests\TestCase;

class ExamDefinitionLifecycleTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_mark_ready_requires_questions(): void
    {
        $exam = $this->createExam();

        $this->expectException(\InvalidArgumentException::class);

        app(ExamLifecycleService::class)->markReady($exam);
    }

    public function test_mark_ready_and_publish_use_curated_questions_in_payload(): void
    {
        config([
            'exam.runtime.base_url' => 'https://exam-runtime.test',
            'exam.runtime.api_key' => 'test-api-key',
        ]);

        $runtimeId = (string) Str::uuid();
        Http::fake([
            'exam-runtime.test/api/v1/integration/exams' => Http::response([
                'runtime_id' => $runtimeId,
                'status' => 'published',
            ], 201),
        ]);

        $exam = $this->createExamWithQuestion();

        app(ExamLifecycleService::class)->markReady($exam->refresh());
        $this->assertSame(ExamStatus::Ready, $exam->refresh()->status);

        $snapshot = app(ExamPublishService::class)->publish($exam->refresh());

        $this->assertSame(ExamStatus::Published, $exam->refresh()->status);
        $this->assertCount(1, $snapshot->payload_json['questions'] ?? []);
        $this->assertSame(7.5, (float) ($snapshot->payload_json['questions'][0]['score'] ?? 0));
        $this->assertSame($runtimeId, $exam->fresh()->runtime_exam_id);
    }

    public function test_duplicate_creates_new_uuids_for_exam_and_pivot(): void
    {
        $exam = $this->createExamWithQuestion();
        $originalPivotId = $exam->examDefinitionQuestions()->first()->id;

        $copy = app(ExamLifecycleService::class)->duplicate($exam->refresh());

        $this->assertNotSame($exam->id, $copy->id);
        $this->assertTrue(Str::isUuid($copy->id));
        $this->assertSame(ExamStatus::Draft, $copy->status);
        $this->assertSame(1, $copy->examDefinitionQuestions()->count());

        $newPivotId = $copy->examDefinitionQuestions()->first()->id;
        $this->assertNotSame($originalPivotId, $newPivotId);
        $this->assertTrue(Str::isUuid($newPivotId));
    }

    protected function createExam(): ExamDefinition
    {
        $tenant = $this->createTenant();

        return ExamDefinition::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'name' => 'Lifecycle Exam',
            'exam_academic_context' => ExamAcademicContext::Standalone,
            'exam_type' => ExamType::Practice,
            'status' => ExamStatus::Draft,
        ]);
    }

    protected function createExamWithQuestion(): ExamDefinition
    {
        $exam = $this->createExam();
        $tenant = Tenant::find($exam->tenant_id);

        $bank = ExamQuestionBank::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'academic_context_type' => ExamAcademicContext::Standalone,
            'name' => 'Bank',
            'status' => QuestionBankStatus::Active,
        ]);

        $question = ExamQuestion::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'exam_question_bank_id' => $bank->id,
            'question_number' => 1,
            'type' => QuestionType::SingleChoice,
            'question_text' => 'Q1',
            'score' => 5,
            'status' => QuestionStatus::Active,
        ]);

        ExamDefinitionQuestion::query()->create([
            'tenant_id' => $tenant->id,
            'exam_definition_id' => $exam->id,
            'exam_question_id' => $question->id,
            'sort_order' => 0,
            'score_override' => 7.5,
        ]);

        return $exam->refresh();
    }

    protected function createTenant(): Tenant
    {
        $plan = SubscriptionPlan::create([
            'code' => 'exam-life',
            'name' => 'Exam Life',
            'included_modules' => ['core', 'exam'],
        ]);

        return Tenant::create([
            'uuid' => (string) Str::uuid(),
            'code' => 'exam-life-tenant',
            'name' => 'Exam Life Tenant',
            'subscription_plan_id' => $plan->id,
        ]);
    }
}
