<?php

namespace Tests\Feature;

use App\Support\CurrentTenant;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Modules\Core\Models\SubscriptionPlan;
use Modules\Core\Models\Tenant;
use Modules\Exam\Enums\ExamAcademicContext;
use Modules\Exam\Enums\ExamRuntimeSyncAction;
use Modules\Exam\Enums\ExamStatus;
use Modules\Exam\Enums\ParticipantSource;
use Modules\Exam\Enums\ParticipantStatus;
use Modules\Exam\Enums\QuestionStatus;
use Modules\Exam\Enums\QuestionType;
use Modules\Exam\Models\ExamActivityLog;
use Modules\Exam\Models\ExamAnswer;
use Modules\Exam\Models\ExamAttempt;
use Modules\Exam\Models\ExamDefinition;
use Modules\Exam\Models\ExamParticipant;
use Modules\Exam\Models\ExamQuestion;
use Modules\Exam\Models\ExamQuestionBank;
use Modules\Exam\Models\ExamResult;
use Modules\Exam\Models\ExamRuntimeSyncLog;
use Modules\Exam\Services\ExamResultSyncService;
use Tests\TestCase;

class ExamResultSyncTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'exam.runtime.base_url' => 'https://exam-runtime.test',
            'exam.runtime.api_key' => 'test-api-key',
        ]);
    }

    public function test_sync_pulls_attempts_answers_results_and_logs(): void
    {
        [$exam, $participant, $question] = $this->seedPublishedExam();
        $runtimeAttemptId = (string) Str::uuid();
        $runtimeAnswerId = (string) Str::uuid();
        $runtimeResultId = (string) Str::uuid();
        $runtimeActivityId = (string) Str::uuid();

        Http::fake([
            'exam-runtime.test/api/integration/results/'.$exam->runtime_exam_id => Http::response([
                'attempts' => [[
                    'id' => $runtimeAttemptId,
                    'participant_external_id' => $participant->id,
                    'status' => 'submitted',
                    'score' => 88,
                    'submitted_at' => '2026-05-28T10:00:00Z',
                ]],
                'answers' => [[
                    'id' => $runtimeAnswerId,
                    'attempt_id' => $runtimeAttemptId,
                    'question_external_id' => $question->id,
                    'value' => 'A',
                    'is_correct' => true,
                    'score' => 5,
                ]],
                'results' => [[
                    'id' => $runtimeResultId,
                    'attempt_id' => $runtimeAttemptId,
                    'participant_external_id' => $participant->id,
                    'score' => 88,
                    'max_score' => 100,
                    'passed' => true,
                ]],
                'activity_logs' => [[
                    'id' => $runtimeActivityId,
                    'attempt_id' => $runtimeAttemptId,
                    'event_type' => 'submitted',
                    'occurred_at' => '2026-05-28T10:00:00Z',
                ]],
                'analytics' => ['summary' => ['average_score' => 88]],
            ], 200),
        ]);

        $summary = app(ExamResultSyncService::class)->sync($exam->refresh());

        $this->assertSame(1, $summary['attempts_synced']);
        $this->assertSame(1, $summary['answers_synced']);
        $this->assertSame(1, $summary['results_synced']);
        $this->assertSame(1, $summary['activity_logs_synced']);

        $attempt = ExamAttempt::withoutTenantScope()->first();
        $this->assertTrue(Str::isUuid($attempt->id));
        $this->assertSame($runtimeAttemptId, $attempt->runtime_attempt_id);
        $this->assertSame('88.00', $attempt->score);

        $answer = ExamAnswer::withoutTenantScope()->first();
        $this->assertTrue(Str::isUuid($answer->id));
        $this->assertSame($runtimeAnswerId, $answer->runtime_answer_id);

        $result = ExamResult::withoutTenantScope()->first();
        $this->assertTrue(Str::isUuid($result->id));
        $this->assertTrue($result->is_passed);
        $this->assertSame('88.00', $result->percentage);

        $log = ExamActivityLog::withoutTenantScope()->first();
        $this->assertTrue(Str::isUuid($log->id));

        $syncLog = ExamRuntimeSyncLog::withoutTenantScope()->latest()->first();
        $this->assertSame(ExamRuntimeSyncAction::SyncResults, $syncLog->action);
    }

    public function test_sync_is_idempotent_on_second_run(): void
    {
        [$exam, $participant] = $this->seedPublishedExam();
        $runtimeAttemptId = (string) Str::uuid();

        $payload = [
            'attempts' => [[
                'id' => $runtimeAttemptId,
                'participant_external_id' => $participant->id,
                'score' => 70,
            ]],
            'answers' => [],
            'results' => [],
            'activity_logs' => [],
        ];

        Http::fake([
            'exam-runtime.test/api/integration/results/*' => Http::response($payload, 200),
        ]);

        $service = app(ExamResultSyncService::class);
        $service->sync($exam->refresh());
        $service->sync($exam->refresh());

        $this->assertSame(1, ExamAttempt::withoutTenantScope()->count());
    }

    public function test_ingest_refuses_an_exam_outside_active_tenant_context(): void
    {
        [$exam] = $this->seedPublishedExam();
        $foreignTenant = Tenant::factory()->create();

        $this->expectException(ModelNotFoundException::class);

        app(CurrentTenant::class)->forTenant($foreignTenant, function () use ($exam): void {
            app(ExamResultSyncService::class)->ingestPayload($exam, []);
        });
    }

    public function test_ingest_skips_foreign_tenant_participant_even_when_exam_id_matches(): void
    {
        [$exam] = $this->seedPublishedExam();
        $foreignTenant = Tenant::factory()->create();

        $foreignParticipant = ExamParticipant::withoutTenantScope()->create([
            'tenant_id' => $foreignTenant->id,
            'exam_definition_id' => $exam->id,
            'student_name' => 'Foreign Student',
            'student_identifier' => 'FOREIGN',
            'participant_source' => ParticipantSource::Manual,
            'status' => ParticipantStatus::Assigned,
        ]);

        $summary = app(ExamResultSyncService::class)->ingestPayload($exam, [
            'attempts' => [[
                'id' => (string) Str::uuid(),
                'participant_external_id' => $foreignParticipant->id,
                'score' => 100,
            ]],
        ]);

        $this->assertSame(1, $summary['participants_skipped']);
        $this->assertSame(0, $summary['attempts_synced']);
        $this->assertSame(0, ExamAttempt::withoutTenantScope()->count());
        $this->assertSame($foreignTenant->id, $foreignParticipant->fresh()->tenant_id);
    }

    public function test_ingest_does_not_reparent_foreign_tenant_attempt_with_same_runtime_id(): void
    {
        [$exam, $participant] = $this->seedPublishedExam();
        $foreignTenant = Tenant::factory()->create();
        $runtimeAttemptId = (string) Str::uuid();

        $foreignAttempt = ExamAttempt::withoutTenantScope()->create([
            'tenant_id' => $foreignTenant->id,
            'exam_definition_id' => $exam->id,
            'exam_participant_id' => $participant->id,
            'runtime_attempt_id' => $runtimeAttemptId,
            'attempt_number' => 1,
            'status' => 'submitted',
            'score' => 33,
        ]);

        $summary = app(ExamResultSyncService::class)->ingestPayload($exam, [
            'attempts' => [[
                'id' => $runtimeAttemptId,
                'participant_external_id' => $participant->id,
                'score' => 99,
            ]],
        ]);

        $this->assertSame(0, $summary['attempts_synced']);
        $this->assertNotEmpty($summary['errors']);
        $this->assertSame($foreignTenant->id, $foreignAttempt->fresh()->tenant_id);
        $this->assertSame('33.00', $foreignAttempt->fresh()->score);
        $this->assertSame(1, ExamAttempt::withoutTenantScope()->count());
    }

    public function test_ingest_does_not_overwrite_foreign_tenant_result_with_same_runtime_id(): void
    {
        [$exam, $participant] = $this->seedPublishedExam();
        $foreignTenant = Tenant::factory()->create();
        $runtimeResultId = (string) Str::uuid();

        $foreignResult = ExamResult::withoutTenantScope()->create([
            'tenant_id' => $foreignTenant->id,
            'exam_definition_id' => $exam->id,
            'exam_participant_id' => $participant->id,
            'runtime_result_id' => $runtimeResultId,
            'score' => 44,
            'max_score' => 100,
            'status' => 'final',
        ]);

        $summary = app(ExamResultSyncService::class)->ingestPayload($exam, [
            'results' => [[
                'id' => $runtimeResultId,
                'participant_external_id' => $participant->id,
                'score' => 99,
                'max_score' => 100,
            ]],
        ]);

        $this->assertSame(0, $summary['results_synced']);
        $this->assertNotEmpty($summary['errors']);
        $this->assertSame($foreignTenant->id, $foreignResult->fresh()->tenant_id);
        $this->assertSame('44.00', $foreignResult->fresh()->score);
        $this->assertSame(1, ExamResult::withoutTenantScope()->count());
    }

    public function test_ingest_rejects_result_whose_attempt_belongs_to_another_participant(): void
    {
        [$exam, $participant] = $this->seedPublishedExam();
        $secondParticipant = ExamParticipant::withoutTenantScope()->create([
            'tenant_id' => $exam->tenant_id,
            'exam_definition_id' => $exam->id,
            'student_name' => 'Second Student',
            'student_identifier' => 'SECOND',
            'participant_source' => ParticipantSource::Manual,
            'status' => ParticipantStatus::Assigned,
        ]);

        $runtimeAttemptId = (string) Str::uuid();

        ExamAttempt::withoutTenantScope()->create([
            'tenant_id' => $exam->tenant_id,
            'exam_definition_id' => $exam->id,
            'exam_participant_id' => $participant->id,
            'runtime_attempt_id' => $runtimeAttemptId,
            'attempt_number' => 1,
            'status' => 'submitted',
        ]);

        $summary = app(ExamResultSyncService::class)->ingestPayload($exam, [
            'results' => [[
                'id' => (string) Str::uuid(),
                'attempt_id' => $runtimeAttemptId,
                'participant_external_id' => $secondParticipant->id,
                'score' => 99,
            ]],
        ]);

        $this->assertSame(0, $summary['results_synced']);
        $this->assertNotEmpty($summary['errors']);
        $this->assertSame(0, ExamResult::withoutTenantScope()->count());
    }

    public function test_command_rejects_invalid_exam_uuid(): void
    {
        $this->artisan('exam:sync-results', ['examUuid' => 'not-a-uuid'])
            ->assertFailed();
    }

    /**
     * @return array{0: ExamDefinition, 1: ExamParticipant, 2: ExamQuestion}
     */
    protected function seedPublishedExam(): array
    {
        $plan = SubscriptionPlan::create([
            'code' => 'exam-b7',
            'name' => 'Exam B7',
            'included_modules' => ['core', 'exam'],
        ]);

        $tenant = Tenant::create([
            'uuid' => (string) Str::uuid(),
            'code' => 'exam-b7',
            'name' => 'Exam B7 Tenant',
            'subscription_plan_id' => $plan->id,
        ]);

        $runtimeExamId = (string) Str::uuid();

        $exam = ExamDefinition::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'name' => 'Published Exam',
            'exam_academic_context' => ExamAcademicContext::Standalone,
            'status' => ExamStatus::Published,
            'runtime_exam_id' => $runtimeExamId,
            'max_score' => 100,
        ]);

        $participant = ExamParticipant::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'exam_definition_id' => $exam->id,
            'student_name' => 'Result Student',
            'student_identifier' => 'RS-99',
            'participant_source' => ParticipantSource::Manual,
            'status' => ParticipantStatus::Assigned,
        ]);

        $question = ExamQuestion::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'exam_question_bank_id' => ExamQuestionBank::withoutTenantScope()->create([
                'tenant_id' => $tenant->id,
                'academic_context_type' => ExamAcademicContext::Standalone,
                'name' => 'Bank',
            ])->id,
            'question_number' => 1,
            'type' => QuestionType::SingleChoice,
            'question_text' => 'Q',
            'score' => 5,
            'status' => QuestionStatus::Active,
        ]);

        return [$exam, $participant, $question];
    }
}
