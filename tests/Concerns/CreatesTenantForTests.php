<?php

namespace Tests\Concerns;

use Illuminate\Support\Str;
use Modules\Core\Models\Organization;
use Modules\Core\Models\SubscriptionPlan;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\TenantRole;
use Modules\Core\Models\User;
use Modules\Core\Models\UserTenantRole;

trait CreatesTenantForTests
{
    /**
     * @param  list<string>  $modules
     * @return array{tenant: Tenant, organization: Organization, user: User, role: TenantRole}
     */
    protected function makeTenantContext(
        array $modules = ['core'],
        ?string $planCode = null,
    ): array {
        $planCode ??= 'test-plan-'.Str::lower(Str::random(6));

        $plan = SubscriptionPlan::firstOrCreate(
            ['code' => $planCode],
            ['name' => 'Test Plan', 'included_modules' => $modules],
        );

        $user = User::factory()->create();

        $tenant = Tenant::create([
            'uuid' => (string) Str::uuid(),
            'code' => 'tenant-'.Str::lower(Str::random(6)),
            'name' => 'Test Tenant',
            'subscription_plan_id' => $plan->id,
            'created_by' => $user->id,
        ]);

        $organization = Organization::create([
            'tenant_id' => $tenant->id,
            'code' => 'main',
            'name' => 'Main Organization',
            'is_active' => true,
            'is_main' => true,
        ]);

        $role = TenantRole::create([
            'tenant_id' => $tenant->id,
            'name' => 'Admin',
            'slug' => 'admin-'.Str::lower(Str::random(4)),
            'permissions' => ['*'],
            'is_default' => true,
        ]);

        UserTenantRole::create([
            'user_id' => $user->id,
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'tenant_role_id' => $role->id,
            'assigned_by' => $user->id,
            'is_primary' => true,
        ]);

        return [
            'tenant' => $tenant,
            'organization' => $organization,
            'user' => $user,
            'role' => $role,
        ];
    }
}
