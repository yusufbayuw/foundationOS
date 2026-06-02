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
use Tests\TestCase;

class CoreTenancyFoundationTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_a_tenant_can_have_multiple_organizations(): void
    {
        $plan = SubscriptionPlan::create([
            'code' => 'starter',
            'name' => 'Starter',
            'included_modules' => ['core'],
        ]);

        $tenant = Tenant::create([
            'uuid' => (string) Str::uuid(),
            'code' => 'alpha-school',
            'name' => 'Alpha School',
            'subscription_plan_id' => $plan->id,
        ]);

        Organization::create([
            'tenant_id' => $tenant->id,
            'code' => 'main-campus',
            'name' => 'Main Campus',
        ]);

        Organization::create([
            'tenant_id' => $tenant->id,
            'code' => 'branch-campus',
            'name' => 'Branch Campus',
        ]);

        $this->assertCount(2, $tenant->organizations);
    }

    public function test_a_user_can_have_multiple_tenant_assignments_through_pivot_roles(): void
    {
        $plan = SubscriptionPlan::create([
            'code' => 'growth',
            'name' => 'Growth',
            'included_modules' => ['core', 'school'],
        ]);

        $user = User::create([
            'name' => 'Tenant Admin',
            'email' => 'admin@example.com',
            'password' => 'password',
        ]);

        $tenantA = Tenant::create([
            'uuid' => (string) Str::uuid(),
            'code' => 'tenant-a',
            'name' => 'Tenant A',
            'subscription_plan_id' => $plan->id,
            'created_by' => $user->id,
        ]);

        $tenantB = Tenant::create([
            'uuid' => (string) Str::uuid(),
            'code' => 'tenant-b',
            'name' => 'Tenant B',
            'subscription_plan_id' => $plan->id,
            'created_by' => $user->id,
        ]);

        $roleA = TenantRole::create([
            'tenant_id' => $tenantA->id,
            'name' => 'Admin',
            'slug' => 'admin',
            'permissions' => ['*'],
            'is_default' => true,
            'is_super_admin' => true,
        ]);

        $roleB = TenantRole::create([
            'tenant_id' => $tenantB->id,
            'name' => 'Manager',
            'slug' => 'manager',
            'permissions' => ['dashboard.view'],
        ]);

        UserTenantRole::create([
            'user_id' => $user->id,
            'tenant_id' => $tenantA->id,
            'tenant_role_id' => $roleA->id,
            'assigned_by' => $user->id,
            'is_primary' => true,
        ]);

        UserTenantRole::create([
            'user_id' => $user->id,
            'tenant_id' => $tenantB->id,
            'tenant_role_id' => $roleB->id,
            'assigned_by' => $user->id,
        ]);

        $this->assertCount(2, $user->userTenantRoles);
        $this->assertSame(
            [$tenantA->id, $tenantB->id],
            $user->tenants()->pluck('tenants.id')->sort()->values()->all()
        );
    }

    public function test_subscription_plan_casts_module_configuration_as_array(): void
    {
        $plan = SubscriptionPlan::create([
            'code' => 'enterprise',
            'name' => 'Enterprise',
            'included_modules' => ['core', 'finance', 'library'],
            'features' => ['sso' => true],
        ]);

        $this->assertIsArray($plan->fresh()->included_modules);
        $this->assertSame(['core', 'finance', 'library'], $plan->fresh()->included_modules);
        $this->assertSame(['sso' => true], $plan->fresh()->features);
    }
}
