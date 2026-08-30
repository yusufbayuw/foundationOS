<?php

namespace Tests\Feature\Exam;

use App\Models\PersonalAccessToken;
use App\Models\Role;
use App\Support\CurrentTenant;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Modules\Core\Models\SubscriptionPlan;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\User;
use Modules\Exam\Enums\ExamAcademicContext;
use Modules\Exam\Enums\ExamPermission;
use Modules\Exam\Enums\ExamRole;
use Modules\Exam\Enums\ExamStatus;
use Modules\Exam\Enums\ParticipantSource;
use Modules\Exam\Enums\ParticipantStatus;
use Modules\Exam\Models\ExamAttemptSync;
use Modules\Exam\Models\ExamDefinition;
use Modules\Exam\Models\ExamParticipant;
use Modules\Exam\Services\ExamShieldProvisioner;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class ExamRuntimeAuthorizationTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected function tearDown(): void
    {
        app(CurrentTenant::class)->forget();
        setPermissionsTeamId(null);

        parent::tearDown();
    }

    public function test_runtime_route_uses_api_throttle_tenant_ability_and_shield_permission(): void
    {
        $route = Route::getRoutes()->getByName('api.exam.runtime.attempts.store');

        $this->assertNotNull($route);
        $middleware = $route->gatherMiddleware();
        $this->assertContains('throttle:api', $middleware);
        $this->assertContains('resolve.api.tenant', $middleware);
        $this->assertContains('abilities:exam:attempts:write', $middleware);
        $this->assertContains('permission:sync_exam_result', $middleware);
    }

    public function test_runtime_token_without_tenant_is_rejected(): void
    {
        $tenant = $this->createTenant('missing-tenant');
        $user = $this->examAdminFor($tenant);
        $token = $this->tokenFor($user, null, ['exam:attempts:write']);

        $this->withToken($token)
            ->postJson('/api/exam/runtime/attempts', $this->payload())
            ->assertForbidden();
    }

    public function test_token_without_runtime_ability_is_rejected(): void
    {
        $tenant = $this->createTenant('wrong-ability');
        $user = $this->examAdminFor($tenant);
        $token = $this->tokenFor($user, $tenant, ['api:write']);

        $this->withToken($token)
            ->postJson('/api/exam/runtime/attempts', $this->payload())
            ->assertForbidden();
    }

    public function test_token_owner_without_shield_permission_is_rejected(): void
    {
        $tenant = $this->createTenant('missing-permission');
        app(ExamShieldProvisioner::class)->provisionForTenant($tenant);
        $user = User::factory()->create();
        $token = $this->tokenFor($user, $tenant, ['exam:attempts:write']);

        $this->withToken($token)
            ->postJson('/api/exam/runtime/attempts', $this->payload())
            ->assertForbidden();
    }

    public function test_permission_from_another_tenant_team_is_rejected(): void
    {
        $tenant = $this->createTenant('active-team');
        $otherTenant = $this->createTenant('permission-team');
        app(ExamShieldProvisioner::class)->provisionForTenant($tenant);
        app(ExamShieldProvisioner::class)->provisionForTenant($otherTenant);
        $user = User::factory()->create();
        setPermissionsTeamId($otherTenant->getKey());
        $user->givePermissionTo(ExamPermission::SyncExamResult->value);
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        [$definition, $participant] = $this->examAndParticipantFor($tenant);
        $token = $this->tokenFor($user, $tenant, ['exam:attempts:write']);

        $this->withToken($token)
            ->postJson('/api/exam/runtime/attempts', $this->payload($definition, $participant))
            ->assertForbidden();

        $this->assertSame(0, ExamAttemptSync::withoutTenantScope()->count());
    }

    public function test_permission_without_contextual_exam_policy_is_rejected(): void
    {
        $tenant = $this->createTenant('policy-denied');
        app(ExamShieldProvisioner::class)->provisionForTenant($tenant);
        $user = User::factory()->create();
        setPermissionsTeamId($tenant->getKey());
        $user->givePermissionTo(ExamPermission::SyncExamResult->value);
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        [$definition, $participant] = $this->examAndParticipantFor($tenant);
        $token = $this->tokenFor($user, $tenant, ['exam:attempts:write']);

        $this->withToken($token)
            ->postJson('/api/exam/runtime/attempts', $this->payload($definition, $participant))
            ->assertForbidden();

        $this->assertSame(0, ExamAttemptSync::withoutTenantScope()->count());
    }

    public function test_foreign_tenant_exam_and_participant_are_rejected(): void
    {
        $tenant = $this->createTenant('local');
        $foreignTenant = $this->createTenant('foreign');
        $user = $this->examAdminFor($tenant);
        [$foreignDefinition, $foreignParticipant] = $this->examAndParticipantFor($foreignTenant);
        $token = $this->tokenFor($user, $tenant, ['exam:attempts:write']);

        $this->withToken($token)
            ->postJson('/api/exam/runtime/attempts', $this->payload($foreignDefinition, $foreignParticipant))
            ->assertUnprocessable()
            ->assertJsonPath('error.code', 'validation_failed')
            ->assertJsonStructure([
                'error' => ['details' => ['exam_definition_id', 'exam_participant_id']],
            ]);

        $this->assertSame(0, ExamAttemptSync::withoutTenantScope()->count());
    }

    public function test_participant_must_belong_to_the_submitted_exam(): void
    {
        $tenant = $this->createTenant('participant-match');
        $user = $this->examAdminFor($tenant);
        [$definition] = $this->examAndParticipantFor($tenant, 'A');
        [, $otherParticipant] = $this->examAndParticipantFor($tenant, 'B');
        $token = $this->tokenFor($user, $tenant, ['exam:attempts:write']);

        $this->withToken($token)
            ->postJson('/api/exam/runtime/attempts', $this->payload($definition, $otherParticipant))
            ->assertUnprocessable()
            ->assertJsonStructure([
                'error' => ['details' => ['exam_participant_id']],
            ]);

        $this->assertSame(0, ExamAttemptSync::withoutTenantScope()->count());
    }

    public function test_soft_deleted_exam_and_participant_are_rejected_by_validation(): void
    {
        $tenant = $this->createTenant('soft-deleted');
        $user = $this->examAdminFor($tenant);
        [$deletedDefinition, $deletedDefinitionParticipant] = $this->examAndParticipantFor($tenant, 'DeletedDefinition');
        [$definition, $deletedParticipant] = $this->examAndParticipantFor($tenant, 'DeletedParticipant');
        $deletedDefinition->delete();
        $deletedParticipant->delete();
        $token = $this->tokenFor($user, $tenant, ['exam:attempts:write']);

        $this->withToken($token)
            ->postJson('/api/exam/runtime/attempts', $this->payload($deletedDefinition, $deletedDefinitionParticipant))
            ->assertUnprocessable()
            ->assertJsonStructure([
                'error' => ['details' => ['exam_definition_id']],
            ]);

        $this->withToken($token)
            ->postJson('/api/exam/runtime/attempts', $this->payload($definition, $deletedParticipant))
            ->assertUnprocessable()
            ->assertJsonStructure([
                'error' => ['details' => ['exam_participant_id']],
            ]);

        $this->assertSame(0, ExamAttemptSync::withoutTenantScope()->count());
    }

    public function test_runtime_attempt_id_is_required_for_idempotency(): void
    {
        $tenant = $this->createTenant('required-id');
        $user = $this->examAdminFor($tenant);
        [$definition, $participant] = $this->examAndParticipantFor($tenant);
        $token = $this->tokenFor($user, $tenant, ['exam:attempts:write']);
        $payload = $this->payload($definition, $participant);
        unset($payload['runtime_attempt_id']);

        $this->withToken($token)
            ->postJson('/api/exam/runtime/attempts', $payload)
            ->assertUnprocessable()
            ->assertJsonStructure([
                'error' => ['details' => ['runtime_attempt_id']],
            ]);
    }

    public function test_runtime_attempt_id_must_be_a_uuid(): void
    {
        $tenant = $this->createTenant('invalid-id');
        $user = $this->examAdminFor($tenant);
        [$definition, $participant] = $this->examAndParticipantFor($tenant);
        $token = $this->tokenFor($user, $tenant, ['exam:attempts:write']);

        $this->withToken($token)
            ->postJson('/api/exam/runtime/attempts', [
                ...$this->payload($definition, $participant),
                'runtime_attempt_id' => 'runtime-attempt-001',
            ])
            ->assertUnprocessable()
            ->assertJsonStructure([
                'error' => ['details' => ['runtime_attempt_id']],
            ]);

        $this->assertSame(0, ExamAttemptSync::withoutTenantScope()->count());
    }

    public function test_authorized_retry_updates_one_attempt_sync_idempotently(): void
    {
        $tenant = $this->createTenant('authorized');
        $user = $this->examAdminFor($tenant);
        [$definition, $participant] = $this->examAndParticipantFor($tenant);
        $token = $this->tokenFor($user, $tenant, ['exam:attempts:write']);
        $payload = $this->payload($definition, $participant);

        $this->withToken($token)
            ->postJson('/api/exam/runtime/attempts', $payload)
            ->assertOk()
            ->assertJsonPath('data.sync_status', 'received');

        $this->withToken($token)
            ->postJson('/api/exam/runtime/attempts', [...$payload, 'score' => 91])
            ->assertOk();

        $this->assertSame(1, ExamAttemptSync::withoutTenantScope()->count());
        $this->assertSame(
            '91.00',
            ExamAttemptSync::withoutTenantScope()->firstOrFail()->score,
        );
    }

    public function test_runtime_attempt_cannot_be_reassigned_to_another_participant(): void
    {
        $tenant = $this->createTenant('reassignment');
        $user = $this->examAdminFor($tenant);
        [$definition, $participant] = $this->examAndParticipantFor($tenant);
        $otherParticipant = ExamParticipant::withoutTenantScope()->create([
            'tenant_id' => $tenant->getKey(),
            'exam_definition_id' => $definition->getKey(),
            'student_name' => 'Other Runtime Participant',
            'student_identifier' => 'OTHER-RUNTIME-PARTICIPANT',
            'participant_source' => ParticipantSource::Manual,
            'status' => ParticipantStatus::Assigned,
        ]);
        $token = $this->tokenFor($user, $tenant, ['exam:attempts:write']);
        $payload = $this->payload($definition, $participant);

        $this->withToken($token)
            ->postJson('/api/exam/runtime/attempts', $payload)
            ->assertOk();

        $this->withToken($token)
            ->postJson('/api/exam/runtime/attempts', [
                ...$payload,
                'exam_participant_id' => $otherParticipant->getKey(),
            ])
            ->assertUnprocessable()
            ->assertJsonStructure([
                'error' => ['details' => ['runtime_attempt_id']],
            ]);

        $attempt = ExamAttemptSync::withoutTenantScope()->sole();
        $this->assertSame($participant->getKey(), $attempt->exam_participant_id);
    }

    public function test_database_rejects_duplicate_runtime_attempt_for_an_exam(): void
    {
        $tenant = $this->createTenant('database-unique');
        [$definition, $participant] = $this->examAndParticipantFor($tenant);
        $otherParticipant = ExamParticipant::withoutTenantScope()->create([
            'tenant_id' => $tenant->getKey(),
            'exam_definition_id' => $definition->getKey(),
            'student_name' => 'Duplicate Runtime Participant',
            'student_identifier' => 'DUPLICATE-RUNTIME-PARTICIPANT',
            'participant_source' => ParticipantSource::Manual,
            'status' => ParticipantStatus::Assigned,
        ]);
        $attributes = [
            'tenant_id' => $tenant->getKey(),
            'exam_definition_id' => $definition->getKey(),
            'runtime_attempt_id' => '00000000-0000-4000-8000-000000000002',
            'sync_status' => 'received',
        ];

        ExamAttemptSync::withoutTenantScope()->create([
            ...$attributes,
            'exam_participant_id' => $participant->getKey(),
        ]);

        $this->expectException(QueryException::class);

        ExamAttemptSync::withoutTenantScope()->create([
            ...$attributes,
            'exam_participant_id' => $otherParticipant->getKey(),
        ]);
    }

    private function createTenant(string $suffix): Tenant
    {
        $plan = SubscriptionPlan::query()->create([
            'code' => "exam-runtime-{$suffix}-plan",
            'name' => "Exam Runtime {$suffix}",
            'included_modules' => ['core', 'exam'],
        ]);

        return Tenant::query()->create([
            'uuid' => (string) Str::uuid(),
            'code' => "exam-runtime-{$suffix}",
            'name' => "Exam Runtime {$suffix}",
            'subscription_plan_id' => $plan->getKey(),
        ]);
    }

    private function examAdminFor(Tenant $tenant): User
    {
        app(ExamShieldProvisioner::class)->provisionForTenant($tenant);
        setPermissionsTeamId($tenant->getKey());
        $user = User::factory()->create();
        $role = Role::query()
            ->where('name', ExamRole::ExamAdmin->value)
            ->where('tenant_id', $tenant->getKey())
            ->firstOrFail();

        $user->roles()->syncWithoutDetaching([
            $role->getKey() => [
                'model_type' => $user->getMorphClass(),
                'tenant_id' => $tenant->getKey(),
            ],
        ]);
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return $user;
    }

    /**
     * @param  list<string>  $abilities
     */
    private function tokenFor(User $user, ?Tenant $tenant, array $abilities): string
    {
        $createdToken = $user->createToken('exam-runtime-test', $abilities);

        if ($tenant !== null) {
            PersonalAccessToken::query()
                ->findOrFail($createdToken->accessToken->getKey())
                ->update(['tenant_id' => $tenant->getKey()]);
        }

        return $createdToken->plainTextToken;
    }

    /** @return array{ExamDefinition, ExamParticipant} */
    private function examAndParticipantFor(Tenant $tenant, string $suffix = 'A'): array
    {
        $definition = ExamDefinition::withoutTenantScope()->create([
            'tenant_id' => $tenant->getKey(),
            'name' => "Runtime Exam {$suffix}",
            'code' => "RUNTIME-{$suffix}",
            'exam_academic_context' => ExamAcademicContext::Standalone,
            'status' => ExamStatus::Published,
        ]);
        $participant = ExamParticipant::withoutTenantScope()->create([
            'tenant_id' => $tenant->getKey(),
            'exam_definition_id' => $definition->getKey(),
            'student_name' => "Runtime Participant {$suffix}",
            'student_identifier' => "PARTICIPANT-{$suffix}",
            'participant_source' => ParticipantSource::Manual,
            'status' => ParticipantStatus::Assigned,
        ]);

        return [$definition, $participant];
    }

    /**
     * @return array<string, mixed>
     */
    private function payload(
        ?ExamDefinition $definition = null,
        ?ExamParticipant $participant = null,
    ): array {
        return [
            'exam_definition_id' => $definition?->getKey() ?? (string) Str::uuid(),
            'exam_participant_id' => $participant?->getKey() ?? (string) Str::uuid(),
            'runtime_attempt_id' => '00000000-0000-4000-8000-000000000001',
            'score' => 85,
            'result_json' => ['correct' => 17, 'total' => 20],
            'submitted_at' => '2026-08-25T10:00:00+07:00',
        ];
    }
}
