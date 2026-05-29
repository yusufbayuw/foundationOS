<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Support\CurrentTenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Modules\Core\Models\SubscriptionPlan;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\User;
use Modules\Exam\Enums\ExamAcademicContext;
use Modules\Exam\Enums\ExamPermission;
use Modules\Exam\Enums\ExamStatus;
use Modules\Exam\Enums\ExamType;
use Modules\Exam\Enums\ParticipantSource;
use Modules\Exam\Enums\ParticipantStatus;
use Modules\Exam\Enums\QuestionBankStatus;
use Modules\Exam\Enums\QuestionStatus;
use Modules\Exam\Enums\QuestionType;
use Modules\Exam\Models\ExamAnswer;
use Modules\Exam\Models\ExamAttempt;
use Modules\Exam\Models\ExamDefinition;
use Modules\Exam\Models\ExamDefinitionQuestion;
use Modules\Exam\Models\ExamParticipant;
use Modules\Exam\Models\ExamQuestion;
use Modules\Exam\Models\ExamQuestionBank;
use Modules\Exam\Models\ExamQuestionOption;
use Modules\Exam\Models\ExamResult;
use Modules\Exam\Services\ExamAcademicContextService;
use Modules\Exam\Services\ExamLifecycleService;
use Modules\Exam\Services\ExamManualGradingService;
use Modules\Exam\Services\ExamResultSyncService;
use Modules\Exam\Services\ExamRuntimePayloadBuilder;
use Modules\Exam\Services\ExamShieldProvisioner;
use Modules\Exam\Services\ExamTokenService;
use Tests\TestCase;

/**
 * MVP acceptance suite for the Exam module (Phase B12).
 */
class ExamMvpFoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_model_relationships_are_wired(): void
    {
        $exam = $this->seedExamWithQuestionAndParticipant();

        $exam->load(['examParticipants.activeToken', 'examQuestions', 'examDefinitionQuestions']);

        $this->assertCount(1, $exam->examParticipants);
        $this->assertCount(1, $exam->examQuestions);
        $this->assertNotNull($exam->examParticipants->first()?->activeToken);
        $this->assertInstanceOf(ExamDefinitionQuestion::class, $exam->examDefinitionQuestions->first());
    }

    public function test_exam_models_use_uuid_primary_keys_on_create(): void
    {
        $tenant = $this->createTenant();
        $bank = ExamQuestionBank::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'academic_context_type' => ExamAcademicContext::Standalone,
            'name' => 'UUID Bank',
            'status' => QuestionBankStatus::Active,
        ]);

        $models = [
            ExamDefinition::withoutTenantScope()->create([
                'tenant_id' => $tenant->id,
                'name' => 'UUID Exam',
                'exam_academic_context' => ExamAcademicContext::Standalone,
                'status' => ExamStatus::Draft,
            ]),
            ExamQuestion::withoutTenantScope()->create([
                'tenant_id' => $tenant->id,
                'exam_question_bank_id' => $bank->id,
                'question_number' => 1,
                'type' => QuestionType::SingleChoice,
                'question_text' => 'Q',
                'score' => 1,
                'status' => QuestionStatus::Active,
            ]),
        ];

        foreach ($models as $model) {
            $this->assertFalse($model->getIncrementing());
            $this->assertSame('string', $model->getKeyType());
            $this->assertTrue(Str::isUuid((string) $model->getKey()));
        }
    }

    public function test_tenant_scope_limits_exam_definitions(): void
    {
        $tenantA = $this->createTenant('tenant-a-mvp');
        $tenantB = $this->createTenant('tenant-b-mvp');

        ExamDefinition::withoutTenantScope()->create([
            'tenant_id' => $tenantA->id,
            'name' => 'A',
            'exam_academic_context' => ExamAcademicContext::Standalone,
            'status' => ExamStatus::Draft,
        ]);

        ExamDefinition::withoutTenantScope()->create([
            'tenant_id' => $tenantB->id,
            'name' => 'B',
            'exam_academic_context' => ExamAcademicContext::Standalone,
            'status' => ExamStatus::Draft,
        ]);

        app(CurrentTenant::class)->set($tenantA);

        $this->assertCount(1, ExamDefinition::query()->get());
    }

    public function test_question_creation_with_options(): void
    {
        $tenant = $this->createTenant();
        $bank = ExamQuestionBank::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'academic_context_type' => ExamAcademicContext::School,
            'name' => 'School Bank',
            'status' => QuestionBankStatus::Active,
        ]);

        $question = ExamQuestion::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'exam_question_bank_id' => $bank->id,
            'question_number' => 1,
            'type' => QuestionType::SingleChoice,
            'question_text' => '2 + 2 = ?',
            'score' => 10,
            'status' => QuestionStatus::Active,
        ]);

        ExamQuestionOption::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'exam_question_id' => $question->id,
            'option_text' => '4',
            'is_correct' => true,
            'sort_order' => 1,
        ]);

        $this->assertTrue(Str::isUuid($question->id));
        $this->assertCount(1, $question->examQuestionOptions);
    }

    public function test_exam_creation_and_mark_ready(): void
    {
        $exam = $this->seedExamWithQuestionAndParticipant();

        app(ExamLifecycleService::class)->markReady($exam->refresh());

        $this->assertSame(ExamStatus::Ready, $exam->fresh()->status);
    }

    public function test_participant_token_generation_is_unique_per_exam(): void
    {
        $exam = $this->seedExamWithQuestionAndParticipant();
        $participant = $exam->examParticipants()->first();
        $service = app(ExamTokenService::class);

        $token = $service->generateToken($participant);

        $this->assertTrue(Str::isUuid($token->id));
        $this->assertTrue($service->isUniqueForExam($exam, $token->token, $participant));
    }

    public function test_publish_payload_generation_includes_foundation_uuid(): void
    {
        config([
            'exam.runtime.base_url' => 'https://exam-runtime.test',
            'exam.runtime.api_key' => 'key',
        ]);

        $exam = $this->seedExamWithQuestionAndParticipant();
        app(ExamLifecycleService::class)->markReady($exam->refresh());

        $payload = app(ExamRuntimePayloadBuilder::class)->buildFull($exam->refresh());

        $this->assertSame($exam->id, $payload['foundation_id'] ?? null);
        $this->assertNotEmpty($payload['questions'] ?? []);
        $this->assertArrayHasKey('academic_context', $payload);
    }

    public function test_result_sync_is_idempotent(): void
    {
        config([
            'exam.runtime.base_url' => 'https://exam-runtime.test',
            'exam.runtime.api_key' => 'key',
        ]);

        $exam = $this->seedPublishedExamForSync();
        $participant = $exam->examParticipants()->first();
        $runtimeAttemptId = (string) Str::uuid();

        Http::fake([
            'exam-runtime.test/api/integration/results/*' => Http::response([
                'attempts' => [[
                    'id' => $runtimeAttemptId,
                    'participant_external_id' => $participant->id,
                    'score' => 80,
                ]],
                'answers' => [],
                'results' => [],
                'activity_logs' => [],
            ], 200),
        ]);

        $service = app(ExamResultSyncService::class);
        $service->sync($exam->refresh());
        $service->sync($exam->refresh());

        $this->assertSame(1, ExamAttempt::withoutTenantScope()->count());
    }

    public function test_manual_grading_updates_result_score(): void
    {
        $tenant = $this->createTenant();
        $exam = $this->seedExamWithQuestionAndParticipant($tenant);
        $question = ExamQuestion::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'exam_question_bank_id' => $exam->examQuestions()->first()->exam_question_bank_id,
            'question_number' => 2,
            'type' => QuestionType::Essay,
            'question_text' => 'Essay',
            'score' => 20,
            'status' => QuestionStatus::Active,
        ]);

        $participant = $exam->examParticipants()->first();
        $attempt = ExamAttempt::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'exam_definition_id' => $exam->id,
            'exam_participant_id' => $participant->id,
            'runtime_attempt_id' => (string) Str::uuid(),
            'status' => 'submitted',
            'score' => 0,
        ]);

        $answer = ExamAnswer::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'exam_definition_id' => $exam->id,
            'exam_attempt_id' => $attempt->id,
            'exam_question_id' => $question->id,
            'runtime_answer_id' => (string) Str::uuid(),
            'answer_value' => 'Answer text',
        ]);

        ExamResult::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'exam_definition_id' => $exam->id,
            'exam_participant_id' => $participant->id,
            'exam_attempt_id' => $attempt->id,
            'runtime_result_id' => (string) Str::uuid(),
            'score' => 0,
            'max_score' => 100,
            'status' => 'final',
        ]);

        $grader = User::factory()->create();
        app(ExamManualGradingService::class)->gradeEssay($answer, $grader, 18);

        $this->assertSame('18.00', $attempt->fresh()->score);
    }

    public function test_permission_policy_blocks_cross_tenant_view(): void
    {
        $tenantA = $this->createTenant('perm-a');
        $tenantB = $this->createTenant('perm-b');
        app(ExamShieldProvisioner::class)->provisionForTenant($tenantA);

        $examB = ExamDefinition::withoutTenantScope()->create([
            'tenant_id' => $tenantB->id,
            'name' => 'Other',
            'exam_academic_context' => ExamAcademicContext::Standalone,
            'status' => ExamStatus::Draft,
        ]);

        $user = User::factory()->create(['is_super_admin' => false]);
        $this->assignExamAdmin($user, $tenantA);

        app(CurrentTenant::class)->set($tenantA);
        $this->actingAs($user, 'web');

        $this->assertTrue($user->can(ExamPermission::ViewExam->value));
        $this->assertFalse($user->can('view', $examB));
    }

    public function test_school_context_mapping_payload(): void
    {
        $tenant = $this->createTenant();
        $exam = ExamDefinition::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'name' => 'School Exam',
            'exam_academic_context' => ExamAcademicContext::School,
            'standalone_subject' => null,
            'status' => ExamStatus::Draft,
        ]);

        $payload = app(ExamAcademicContextService::class)->build($exam);

        $this->assertSame('school', $payload['academic_context_type']);
        $this->assertArrayHasKey('school_context', $payload);
    }

    public function test_campus_context_mapping_payload(): void
    {
        $tenant = $this->createTenant();
        $exam = ExamDefinition::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'name' => 'Campus Exam',
            'exam_academic_context' => ExamAcademicContext::Campus,
            'status' => ExamStatus::Draft,
        ]);

        $payload = app(ExamAcademicContextService::class)->build($exam);

        $this->assertSame('campus', $payload['academic_context_type']);
        $this->assertArrayHasKey('campus_context', $payload);
    }

    public function test_standalone_osn_context_mapping_payload(): void
    {
        $tenant = $this->createTenant();
        $exam = ExamDefinition::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'name' => 'OSN Tryout',
            'exam_academic_context' => ExamAcademicContext::Standalone,
            'standalone_subject' => 'Physics',
            'standalone_level' => 'OSN',
            'exam_type' => ExamType::OsnPrep,
            'status' => ExamStatus::Draft,
        ]);

        $payload = app(ExamAcademicContextService::class)->build($exam);

        $this->assertSame('standalone', $payload['academic_context_type']);
        $this->assertSame('Physics', $payload['standalone_context']['subject'] ?? null);
        $this->assertSame('OSN', $payload['standalone_context']['level'] ?? null);
    }

    protected function seedExamWithQuestionAndParticipant(?Tenant $tenant = null): ExamDefinition
    {
        $tenant ??= $this->createTenant();
        $exam = ExamDefinition::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'name' => 'MVP Exam',
            'code' => 'MVP-'.Str::random(4),
            'exam_academic_context' => ExamAcademicContext::Standalone,
            'exam_type' => ExamType::Practice,
            'status' => ExamStatus::Draft,
            'max_score' => 10,
        ]);

        $bank = ExamQuestionBank::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'academic_context_type' => ExamAcademicContext::Standalone,
            'name' => 'MVP Bank',
            'status' => QuestionBankStatus::Active,
        ]);

        $question = ExamQuestion::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'exam_question_bank_id' => $bank->id,
            'question_number' => 1,
            'type' => QuestionType::SingleChoice,
            'question_text' => 'Pick one',
            'score' => 10,
            'status' => QuestionStatus::Active,
        ]);

        ExamDefinitionQuestion::query()->create([
            'tenant_id' => $tenant->id,
            'exam_definition_id' => $exam->id,
            'exam_question_id' => $question->id,
            'sort_order' => 0,
        ]);

        $participant = ExamParticipant::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'exam_definition_id' => $exam->id,
            'student_name' => 'MVP Student',
            'participant_source' => ParticipantSource::Manual,
            'status' => ParticipantStatus::Assigned,
        ]);

        app(ExamTokenService::class)->generateToken($participant);

        return $exam->refresh();
    }

    protected function seedPublishedExamForSync(): ExamDefinition
    {
        $exam = $this->seedExamWithQuestionAndParticipant();
        $exam->forceFill([
            'status' => ExamStatus::Published,
            'runtime_exam_id' => (string) Str::uuid(),
        ])->save();

        return $exam->refresh();
    }

    protected function createTenant(string $code = 'exam-mvp'): Tenant
    {
        $plan = SubscriptionPlan::create([
            'code' => $code.'-plan',
            'name' => 'Exam MVP',
            'included_modules' => ['core', 'exam'],
        ]);

        return Tenant::create([
            'uuid' => (string) Str::uuid(),
            'code' => $code,
            'name' => 'Exam MVP Tenant',
            'subscription_plan_id' => $plan->id,
        ]);
    }

    protected function assignExamAdmin(User $user, Tenant $tenant): void
    {
        app(ExamShieldProvisioner::class)->provisionForTenant($tenant);

        $role = Role::query()
            ->where('name', 'exam_admin')
            ->where('tenant_id', $tenant->id)
            ->firstOrFail();

        setPermissionsTeamId($tenant->id);
        $user->roles()->syncWithoutDetaching([
            $role->id => [
                'model_type' => $user->getMorphClass(),
                'tenant_id' => $tenant->id,
            ],
        ]);
    }
}
