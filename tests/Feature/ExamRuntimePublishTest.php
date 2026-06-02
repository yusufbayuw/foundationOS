<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Modules\Core\Models\SubscriptionPlan;
use Modules\Core\Models\Tenant;
use Modules\Exam\Enums\ExamAcademicContext;
use Modules\Exam\Enums\ExamRuntimeSyncAction;
use Modules\Exam\Enums\ExamRuntimeSyncStatus;
use Modules\Exam\Enums\ExamStatus;
use Modules\Exam\Enums\ExamType;
use Modules\Exam\Enums\ParticipantSource;
use Modules\Exam\Enums\ParticipantStatus;
use Modules\Exam\Enums\QuestionBankStatus;
use Modules\Exam\Enums\QuestionStatus;
use Modules\Exam\Enums\QuestionType;
use Modules\Exam\Exceptions\ExamRuntimeException;
use Modules\Exam\Models\ExamDefinition;
use Modules\Exam\Models\ExamDefinitionQuestion;
use Modules\Exam\Models\ExamParticipant;
use Modules\Exam\Models\ExamQuestion;
use Modules\Exam\Models\ExamQuestionBank;
use Modules\Exam\Models\ExamQuestionOption;
use Modules\Exam\Models\ExamRuntimeSyncLog;
use Modules\Exam\Models\ExamToken;
use Modules\Exam\Services\ExamAcademicContextService;
use Modules\Exam\Services\ExamLifecycleService;
use Modules\Exam\Services\ExamPublishService;
use Modules\Exam\Services\ExamRuntimePayloadBuilder;
use Tests\TestCase;

class ExamRuntimePublishTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'exam.runtime.base_url' => 'https://exam-runtime.test',
            'exam.runtime.api_key' => 'test-api-key',
            'exam.runtime.hmac_secret' => 'test-hmac-secret',
        ]);
    }

    public function test_publish_sends_payload_and_stores_runtime_id_and_sync_log(): void
    {
        $runtimeId = (string) Str::uuid();
        $exam = $this->createReadyExam();

        Http::fake([
            'exam-runtime.test/api/v1/integration/exams' => Http::response([
                'runtime_id' => $runtimeId,
                'external_id' => $exam->id,
                'status' => 'published',
            ], 201),
        ]);

        $snapshot = app(ExamPublishService::class)->publish($exam->refresh());

        $this->assertSame($runtimeId, $snapshot->runtime_exam_id);
        $this->assertSame($runtimeId, $exam->fresh()->runtime_exam_id);
        $this->assertSame(ExamStatus::Published, $exam->fresh()->status);

        Http::assertSent(function ($request) use ($exam): bool {
            $payload = json_decode($request->body(), true);

            return $request->url() === 'https://exam-runtime.test/api/v1/integration/exams'
                && $request->hasHeader('Authorization', 'Bearer test-api-key')
                && $request->hasHeader('X-FOS-Idempotency-Key')
                && ($payload['foundation_id'] ?? null) === $exam->id
                && count($payload['questions'] ?? []) === 1
                && count($payload['options'] ?? []) === 2
                && isset($payload['academic_context']['standalone_context']);
        });

        $log = ExamRuntimeSyncLog::withoutTenantScope()->first();
        $this->assertNotNull($log);
        $this->assertTrue(Str::isUuid($log->id));
        $this->assertSame(ExamRuntimeSyncAction::Publish, $log->action);
        $this->assertSame(ExamRuntimeSyncStatus::Success, $log->status);
        $this->assertSame($runtimeId, $log->runtime_id);
    }

    public function test_republish_uses_put_and_is_idempotent_with_existing_runtime_id(): void
    {
        $runtimeId = (string) Str::uuid();
        $exam = $this->createReadyExam();
        $exam->forceFill([
            'status' => ExamStatus::Published,
            'runtime_exam_id' => $runtimeId,
        ])->save();

        Http::fake([
            'exam-runtime.test/api/v1/integration/exams/'.$runtimeId => Http::response([
                'runtime_id' => $runtimeId,
                'status' => 'published',
            ], 200),
        ]);

        app(ExamPublishService::class)->republish($exam->refresh());

        Http::assertSent(fn ($request): bool => $request->method() === 'PUT'
            && str_contains($request->url(), '/api/v1/integration/exams/'.$runtimeId));
    }

    public function test_sync_participants_only_posts_partial_payload(): void
    {
        $runtimeId = (string) Str::uuid();
        $exam = $this->createReadyExam();
        $exam->forceFill(['runtime_exam_id' => $runtimeId, 'status' => ExamStatus::Published])->save();

        $participant = ExamParticipant::withoutTenantScope()->create([
            'tenant_id' => $exam->tenant_id,
            'exam_definition_id' => $exam->id,
            'student_name' => 'Runtime Student',
            'student_identifier' => 'RS-1',
            'participant_source' => ParticipantSource::Manual,
            'status' => ParticipantStatus::Assigned,
        ]);

        ExamToken::withoutTenantScope()->create([
            'tenant_id' => $exam->tenant_id,
            'exam_participant_id' => $participant->id,
            'token' => 'ABC12345',
            'is_active' => true,
        ]);

        Http::fake([
            'exam-runtime.test/api/v1/integration/exams/'.$runtimeId.'/participants' => Http::response([
                'runtime_id' => $runtimeId,
                'status' => 'synced',
            ], 200),
        ]);

        $log = app(ExamPublishService::class)->syncParticipantsOnly($exam->refresh());

        $this->assertSame(ExamRuntimeSyncAction::SyncParticipants, $log->action);
        $this->assertSame(1, $log->request_summary['participant_count'] ?? 0);
    }

    public function test_academic_context_service_builds_school_context_payload(): void
    {
        $exam = $this->createReadyExam();
        $exam->forceFill([
            'exam_academic_context' => ExamAcademicContext::School,
            'standalone_subject' => null,
        ])->save();

        $context = app(ExamAcademicContextService::class)->build($exam->refresh());

        $this->assertSame('school', $context['academic_context_type']);
        $this->assertArrayHasKey('school_context', $context);
    }

    public function test_publish_failure_stores_sync_log_with_error_message(): void
    {
        $exam = $this->createReadyExam();

        Http::fake([
            'exam-runtime.test/api/v1/integration/exams' => Http::response([
                'message' => 'Invalid payload',
                'errors' => ['questions' => ['required']],
            ], 422),
        ]);

        try {
            app(ExamPublishService::class)->publish($exam->refresh());
            $this->fail('Expected ExamRuntimeException was not thrown.');
        } catch (ExamRuntimeException $exception) {
            $this->assertStringContainsString('Invalid payload', $exception->getMessage());
        }

        $log = ExamRuntimeSyncLog::withoutTenantScope()->latest()->first();
        $this->assertSame(ExamRuntimeSyncStatus::Failed, $log?->status);
        $this->assertNotNull($log?->error_message);
    }

    public function test_payload_builder_uses_uuid_for_exam_module_entities(): void
    {
        $exam = $this->createReadyExam();
        $payload = app(ExamRuntimePayloadBuilder::class)->buildFull($exam->refresh());

        $this->assertTrue(Str::isUuid($payload['foundation_id']));
        $this->assertTrue(Str::isUuid($payload['questions'][0]['foundation_id']));
        $this->assertTrue(Str::isUuid($payload['options'][0]['foundation_id']));
    }

    protected function createReadyExam(): ExamDefinition
    {
        $plan = SubscriptionPlan::create([
            'code' => 'exam-b6',
            'name' => 'Exam B6',
            'included_modules' => ['core', 'exam'],
        ]);

        $tenant = Tenant::create([
            'uuid' => (string) Str::uuid(),
            'code' => 'exam-b6',
            'name' => 'Exam B6 Tenant',
            'subscription_plan_id' => $plan->id,
        ]);

        $exam = ExamDefinition::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'name' => 'Runtime Exam',
            'exam_academic_context' => ExamAcademicContext::Standalone,
            'exam_type' => ExamType::Practice,
            'status' => ExamStatus::Draft,
            'standalone_subject' => 'Physics',
            'standalone_level' => 'National',
        ]);

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

        ExamQuestionOption::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'exam_question_id' => $question->id,
            'option_text' => 'A',
            'is_correct' => true,
            'sort_order' => 1,
        ]);

        ExamQuestionOption::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'exam_question_id' => $question->id,
            'option_text' => 'B',
            'is_correct' => false,
            'sort_order' => 2,
        ]);

        ExamDefinitionQuestion::query()->create([
            'tenant_id' => $tenant->id,
            'exam_definition_id' => $exam->id,
            'exam_question_id' => $question->id,
            'sort_order' => 0,
        ]);

        app(ExamLifecycleService::class)->markReady($exam->refresh());

        return $exam->refresh();
    }
}
