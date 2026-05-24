<?php

namespace Tests\Feature;

use App\Support\CurrentTenant;
use Filament\Panel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\TenantRole;
use Modules\Core\Models\User;
use Modules\Core\Models\UserTenantRole;
use Modules\School\Models\Student;
use Tests\TestCase;

class TenantIsolationStudentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        app(CurrentTenant::class)->forget();
    }

    protected function tearDown(): void
    {
        app(CurrentTenant::class)->forget();

        parent::tearDown();
    }

    public function test_student_receives_current_tenant_id_when_created(): void
    {
        $tenant = Tenant::factory()->create();

        app(CurrentTenant::class)->set($tenant);

        $student = Student::factory()->create([
            'tenant_id' => null,
            'nis' => 'AUTO-TENANT',
        ]);

        $this->assertSame($tenant->getKey(), $student->tenant_id);
    }

    public function test_student_queries_are_scoped_to_current_tenant(): void
    {
        $tenantA = Tenant::factory()->create(['code' => 'school-a', 'name' => 'School A']);
        $tenantB = Tenant::factory()->create(['code' => 'school-b', 'name' => 'School B']);

        Student::withoutTenantScope()->create([
            'tenant_id' => $tenantA->getKey(),
            'nis' => 'A-001',
            'status' => 'active',
        ]);

        Student::withoutTenantScope()->create([
            'tenant_id' => $tenantB->getKey(),
            'nis' => 'B-001',
            'status' => 'active',
        ]);

        app(CurrentTenant::class)->set($tenantA);

        $this->assertSame(['A-001'], Student::query()->pluck('nis')->all());
        $this->assertNull(Student::query()->where('nis', 'B-001')->first());
    }

    public function test_without_tenant_scope_explicitly_accesses_students_from_all_tenants(): void
    {
        $tenantA = Tenant::factory()->create(['code' => 'scope-a']);
        $tenantB = Tenant::factory()->create(['code' => 'scope-b']);

        Student::withoutTenantScope()->create([
            'tenant_id' => $tenantA->getKey(),
            'nis' => 'A-ALL',
            'status' => 'active',
        ]);

        Student::withoutTenantScope()->create([
            'tenant_id' => $tenantB->getKey(),
            'nis' => 'B-ALL',
            'status' => 'active',
        ]);

        app(CurrentTenant::class)->set($tenantA);

        $this->assertSame(1, Student::query()->count());
        $this->assertSame(['A-ALL', 'B-ALL'], Student::withoutTenantScope()->orderBy('nis')->pluck('nis')->all());
        $this->assertSame(2, Student::allTenants()->count());
    }

    public function test_user_tenant_and_admin_panel_access_require_assignment_or_global_admin(): void
    {
        $assignedTenant = Tenant::factory()->create(['code' => 'assigned']);
        $foreignTenant = Tenant::factory()->create(['code' => 'foreign']);
        $tenantUser = User::factory()->create(['is_super_admin' => false]);
        $plainUser = User::factory()->create(['is_super_admin' => false]);
        $superAdmin = User::factory()->create(['is_super_admin' => true]);

        $this->assignUserToTenant($tenantUser, $assignedTenant);

        $adminPanel = Panel::make()->id('admin');

        $this->assertTrue($tenantUser->canAccessTenant($assignedTenant));
        $this->assertFalse($tenantUser->canAccessTenant($foreignTenant));
        $this->assertTrue($tenantUser->canAccessPanel($adminPanel));

        $this->assertFalse($plainUser->canAccessTenant($assignedTenant));
        $this->assertFalse($plainUser->canAccessPanel($adminPanel));
        $this->assertTrue($superAdmin->canAccessPanel($adminPanel));
    }

    private function assignUserToTenant(User $user, Tenant $tenant): void
    {
        $role = TenantRole::query()->create([
            'tenant_id' => $tenant->getKey(),
            'name' => 'Member '.$tenant->code,
            'slug' => 'member-'.$tenant->code,
            'permissions' => ['dashboard.view'],
        ]);

        UserTenantRole::query()->create([
            'user_id' => $user->getKey(),
            'tenant_id' => $tenant->getKey(),
            'tenant_role_id' => $role->getKey(),
            'assigned_by' => $user->getKey(),
            'is_primary' => true,
        ]);
    }
}
