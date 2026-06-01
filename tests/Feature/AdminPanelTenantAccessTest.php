<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\TenantModule;
use Modules\Core\Models\TenantRole;
use Modules\Core\Models\User;
use Modules\Core\Models\UserTenantRole;
use Modules\Core\Services\ApplicationModuleCatalog;
use Modules\Core\Services\TenantModuleProvisioner;
use Tests\TestCase;

class AdminPanelTenantAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_global_super_admin_cannot_access_tenant_without_membership(): void
    {
        $user = User::factory()->superAdmin()->create();

        $memberTenant = $this->createTenantWithMembership($user, 'member-tenant');
        $foreignTenant = $this->createTenantWithMembership(
            User::factory()->create(),
            'foreign-tenant',
        );

        $this->actingAs($user)
            ->get('/admin/'.$memberTenant->uuid)
            ->assertOk();

        $this->actingAs($user)
            ->get('/admin/'.$foreignTenant->uuid)
            ->assertNotFound();

        $this->assertFalse($user->canAccessTenant($foreignTenant));
    }

    public function test_numeric_tenant_id_in_url_is_not_resolved(): void
    {
        $user = User::factory()->superAdmin()->create();

        $tenant = $this->createTenantWithMembership($user, 'uuid-tenant');

        $this->actingAs($user)
            ->get('/admin/'.$tenant->id)
            ->assertNotFound();
    }

    public function test_provisioner_enables_tenant_modules_for_navigation(): void
    {
        $user = User::factory()->superAdmin()->create();

        $tenant = $this->createTenantWithMembership($user, 'modules-tenant');

        app(ApplicationModuleCatalog::class)->sync();
        app(TenantModuleProvisioner::class)->enableForTenant($tenant, ['school', 'finance']);

        $this->assertSame(
            2,
            TenantModule::query()
                ->where('tenant_id', $tenant->getKey())
                ->where('is_enabled', true)
                ->count(),
        );
    }

    protected function createTenantWithMembership(User $user, string $code): Tenant
    {
        $tenant = Tenant::query()->create([
            'uuid' => (string) Str::uuid(),
            'code' => $code,
            'name' => str($code)->headline()->toString(),
            'status' => 'active',
            'created_by' => $user->getKey(),
        ]);

        $role = TenantRole::query()->create([
            'tenant_id' => $tenant->getKey(),
            'name' => 'Member',
            'slug' => 'member',
            'permissions' => ['*'],
            'is_default' => true,
        ]);

        UserTenantRole::query()->create([
            'user_id' => $user->getKey(),
            'tenant_id' => $tenant->getKey(),
            'tenant_role_id' => $role->getKey(),
            'assigned_by' => $user->getKey(),
            'is_primary' => true,
        ]);

        return $tenant;
    }
}
