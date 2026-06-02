<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Support\CurrentTenant;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Str;
use Modules\Core\Models\SubscriptionPlan;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\User;
use Modules\Exam\Enums\ExamAcademicContext;
use Modules\Exam\Enums\ExamAuditAction;
use Modules\Exam\Enums\ExamPermission;
use Modules\Exam\Enums\ExamRole;
use Modules\Exam\Enums\ExamStatus;
use Modules\Exam\Models\ExamDefinition;
use Modules\Exam\Services\ExamAuditLogger;
use Modules\Exam\Services\ExamAuthorizationService;
use Modules\Exam\Services\ExamShieldProvisioner;
use Modules\Monitoring\Models\AuditLog;
use Modules\School\Models\Teacher;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class ExamSecurityTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_user_cannot_view_exam_from_another_tenant(): void
    {
        [$tenantA, $tenantB, $examB] = $this->seedTwoTenantsWithExam();
        $userA = $this->makeTenantUser($tenantA, ExamRole::ExamAdmin);

        app(CurrentTenant::class)->set($tenantA);
        setPermissionsTeamId($tenantA->id);

        $this->actingAs($userA, 'web');

        $this->assertFalse($userA->can('view', $examB));
    }

    public function test_teacher_only_accesses_assigned_school_exam(): void
    {
        $tenant = $this->createTenant();
        $teacherUser = $this->makeTenantUser($tenant, ExamRole::Teacher);
        $teacher = Teacher::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'user_id' => $teacherUser->id,
            'nip' => 'T-001',
        ]);

        $ownedExam = ExamDefinition::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'name' => 'Owned',
            'exam_academic_context' => ExamAcademicContext::School,
            'school_teacher_reference' => $teacher->id,
            'status' => ExamStatus::Draft,
        ]);

        $otherTeacher = Teacher::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'user_id' => User::factory()->create()->id,
            'nip' => 'T-002',
        ]);

        $otherExam = ExamDefinition::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'name' => 'Other',
            'exam_academic_context' => ExamAcademicContext::School,
            'school_teacher_reference' => $otherTeacher->id,
            'status' => ExamStatus::Draft,
        ]);

        app(CurrentTenant::class)->set($tenant);
        setPermissionsTeamId($tenant->id);

        $this->assertTrue($teacherUser->can('view', $ownedExam));
        $this->assertFalse($teacherUser->can('view', $otherExam));

        $visible = app(ExamAuthorizationService::class)
            ->scopeAccessibleExams(ExamDefinition::withoutTenantScope()->where('tenant_id', $tenant->id), $teacherUser)
            ->pluck('id')
            ->all();

        $this->assertContains($ownedExam->id, $visible);
        $this->assertNotContains($otherExam->id, $visible);
    }

    public function test_proctor_can_open_control_room_only_when_assigned(): void
    {
        $tenant = $this->createTenant();
        $proctor = $this->makeTenantUser($tenant, ExamRole::Proctor);

        $assigned = ExamDefinition::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'name' => 'Assigned',
            'exam_academic_context' => ExamAcademicContext::Standalone,
            'status' => ExamStatus::Published,
            'metadata_json' => ['proctor_user_ids' => [$proctor->id]],
        ]);

        $unassigned = ExamDefinition::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'name' => 'Unassigned',
            'exam_academic_context' => ExamAcademicContext::Standalone,
            'status' => ExamStatus::Published,
            'metadata_json' => [],
        ]);

        app(CurrentTenant::class)->set($tenant);
        setPermissionsTeamId($tenant->id);

        $this->assertTrue($proctor->can('openControlRoom', $assigned));
        $this->assertFalse($proctor->can('openControlRoom', $unassigned));
    }

    public function test_publish_writes_uuid_auditable_security_audit_log(): void
    {
        $tenant = $this->createTenant();
        $admin = $this->makeTenantUser($tenant, ExamRole::ExamAdmin);

        $exam = ExamDefinition::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'name' => 'Audit exam',
            'exam_academic_context' => ExamAcademicContext::Standalone,
            'status' => ExamStatus::Published,
            'runtime_exam_id' => (string) Str::uuid(),
        ]);

        app(CurrentTenant::class)->set($tenant);

        $this->actingAs($admin);

        app(ExamAuditLogger::class)->log(
            ExamAuditAction::PublishExam,
            $exam,
            'Exam published to runtime.',
            newValues: ['runtime_exam_id' => $exam->runtime_exam_id],
        );

        $log = AuditLog::withoutTenantScope()->latest('id')->first();

        $this->assertNotNull($log);
        $this->assertSame(ExamAuditAction::PublishExam->value, $log->action);
        $this->assertTrue(Str::isUuid((string) $log->auditable_id));
        $this->assertSame(ExamDefinition::class, $log->auditable_type);
    }

    public function test_exam_admin_has_custom_permissions_after_provision(): void
    {
        $tenant = $this->createTenant();
        app(ExamShieldProvisioner::class)->provisionForTenant($tenant);

        $admin = $this->makeTenantUser($tenant, ExamRole::ExamAdmin);

        setPermissionsTeamId($tenant->id);
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $this->assertTrue($admin->can(ExamPermission::PublishExam->value));
        $this->assertTrue($admin->can(ExamPermission::OpenControlRoom->value));
    }

    /**
     * @return array{0: Tenant, 1: Tenant, 2: ExamDefinition}
     */
    protected function seedTwoTenantsWithExam(): array
    {
        $tenantA = $this->createTenant('tenant-a-sec');
        $tenantB = $this->createTenant('tenant-b-sec');

        $examB = ExamDefinition::withoutTenantScope()->create([
            'tenant_id' => $tenantB->id,
            'name' => 'Tenant B exam',
            'exam_academic_context' => ExamAcademicContext::Standalone,
            'status' => ExamStatus::Draft,
        ]);

        app(ExamShieldProvisioner::class)->provisionForTenant($tenantA);
        app(ExamShieldProvisioner::class)->provisionForTenant($tenantB);

        return [$tenantA, $tenantB, $examB];
    }

    protected function createTenant(string $code = 'exam-sec'): Tenant
    {
        $plan = SubscriptionPlan::create([
            'code' => $code.'-plan',
            'name' => 'Exam Security',
            'included_modules' => ['core', 'exam'],
        ]);

        return Tenant::create([
            'uuid' => (string) Str::uuid(),
            'code' => $code,
            'name' => 'Exam Security Tenant',
            'subscription_plan_id' => $plan->id,
        ]);
    }

    protected function makeTenantUser(Tenant $tenant, ExamRole $role): User
    {
        $user = User::factory()->create();

        app(ExamShieldProvisioner::class)->provisionForTenant($tenant);

        setPermissionsTeamId($tenant->id);

        $roleModel = Role::query()
            ->where('name', $role->value)
            ->where('tenant_id', $tenant->id)
            ->firstOrFail();

        $user->roles()->syncWithoutDetaching([
            $roleModel->id => [
                'model_type' => $user->getMorphClass(),
                'tenant_id' => $tenant->id,
            ],
        ]);

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $this->actingAs($user, 'web');

        return $user;
    }
}
