<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Str;
use Modules\Core\Models\Organization;
use Modules\Core\Models\SubscriptionPlan;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\TenantRole;
use Modules\Core\Models\User;
use Modules\Core\Models\UserTenantRole;
use Modules\Core\Policies\Concerns\AuthorizesTenantScopedRecord;
use Modules\Core\Support\Tenancy\CurrentTenant;
use Tests\TestCase;

class CorePolicyTenantAuthorizationTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_organization_policy_denies_cross_tenant_record_for_member_user(): void
    {
        [$tenantA, $user] = $this->createTenantMembership('tenant-a');
        $tenantB = $this->createBareTenant('tenant-b');

        $foreignOrganization = Organization::withoutTenantScope()->create([
            'tenant_id' => $tenantB->id,
            'code' => 'foreign',
            'name' => 'Foreign Campus',
        ]);

        app(CurrentTenant::class)->set($tenantA);

        $policy = new class
        {
            use AuthorizesTenantScopedRecord;

            public function allows(User $user, Organization $organization): bool
            {
                return $this->belongsToActiveTenant($user, $organization);
            }
        };

        $this->assertFalse($policy->allows($user, $foreignOrganization));
    }

    public function test_organization_policy_allows_record_from_active_tenant_for_member_user(): void
    {
        [$tenant, $user, $organization] = $this->createTenantMembership('tenant-allowed');

        app(CurrentTenant::class)->set($tenant);

        $policy = new class
        {
            use AuthorizesTenantScopedRecord;

            public function allows(User $user, Organization $organization): bool
            {
                return $this->belongsToActiveTenant($user, $organization);
            }
        };

        $this->assertTrue($policy->allows($user, $organization));
    }

    public function test_tenant_role_policy_denies_foreign_tenant_role_for_member_user(): void
    {
        [$tenantA, $user] = $this->createTenantMembership('role-a');
        $tenantB = $this->createBareTenant('role-b');

        $foreignRole = TenantRole::withoutTenantScope()->create([
            'tenant_id' => $tenantB->id,
            'name' => 'Foreign Role',
            'slug' => 'foreign-role',
            'permissions' => ['*'],
        ]);

        app(CurrentTenant::class)->set($tenantA);

        $policy = new class
        {
            use AuthorizesTenantScopedRecord;

            public function allows(User $user, TenantRole $tenantRole): bool
            {
                return $this->belongsToActiveTenant($user, $tenantRole);
            }
        };

        $this->assertFalse($policy->allows($user, $foreignRole));
    }

    public function test_global_super_admin_bypasses_tenant_record_check(): void
    {
        [$tenantA, $user] = $this->createTenantMembership('super-admin');
        $tenantB = $this->createBareTenant('foreign-super');

        $user->promoteToGlobalSuperAdmin();

        $foreignOrganization = Organization::withoutTenantScope()->create([
            'tenant_id' => $tenantB->id,
            'code' => 'foreign',
            'name' => 'Foreign Campus',
        ]);

        app(CurrentTenant::class)->set($tenantA);

        $policy = new class
        {
            use AuthorizesTenantScopedRecord;

            public function allows(User $user, Organization $organization): bool
            {
                return $this->belongsToActiveTenant($user, $organization);
            }
        };

        $this->assertTrue($policy->allows($user, $foreignOrganization));
    }

    /**
     * @return array{0: Tenant, 1: User, 2?: Organization}
     */
    private function createTenantMembership(string $code): array
    {
        $plan = SubscriptionPlan::create([
            'code' => 'starter',
            'name' => 'Starter',
            'included_modules' => ['core'],
        ]);

        $user = User::create([
            'name' => 'Policy User',
            'email' => $code.'-'.Str::random(5).'@example.com',
            'password' => 'password',
        ]);

        $tenant = Tenant::create([
            'uuid' => (string) Str::uuid(),
            'code' => $code,
            'name' => strtoupper($code),
            'subscription_plan_id' => $plan->id,
            'created_by' => $user->id,
        ]);

        $organization = Organization::create([
            'tenant_id' => $tenant->id,
            'code' => 'main',
            'name' => 'Main Campus',
        ]);

        $role = TenantRole::create([
            'tenant_id' => $tenant->id,
            'name' => 'Admin',
            'slug' => 'admin',
            'permissions' => ['*'],
            'is_default' => true,
            'is_super_admin' => true,
        ]);

        UserTenantRole::create([
            'user_id' => $user->id,
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'tenant_role_id' => $role->id,
            'assigned_by' => $user->id,
            'is_primary' => true,
        ]);

        return [$tenant, $user, $organization];
    }

    private function createBareTenant(string $code): Tenant
    {
        $plan = SubscriptionPlan::query()->first() ?? SubscriptionPlan::create([
            'code' => 'starter',
            'name' => 'Starter',
            'included_modules' => ['core'],
        ]);

        return Tenant::create([
            'uuid' => (string) Str::uuid(),
            'code' => $code,
            'name' => strtoupper($code),
            'subscription_plan_id' => $plan->id,
        ]);
    }
}
