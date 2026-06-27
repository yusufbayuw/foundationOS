<?php

namespace Tests\Policy;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Str;
use Modules\Core\Models\Organization;
use Modules\Core\Models\SubscriptionPlan;
use Modules\Core\Models\Tenant;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class CorePolicyAuthorizationTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_tenant_policy_denies_view_without_shield_permission(): void
    {
        $tenant = $this->makeTenant();
        $user = User::factory()->create();

        setPermissionsTeamId(0);
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $this->assertFalse($user->can('view', $tenant));
        $this->assertFalse($user->can('update', $tenant));
    }

    public function test_tenant_policy_allows_view_when_shield_permission_granted(): void
    {
        $tenant = $this->makeTenant();
        $user = User::factory()->create();

        $this->assignPlatformPermission($user, 'View:Tenant');

        $this->assertTrue($user->can('view', $tenant));
        $this->assertFalse($user->can('update', $tenant));
    }

    public function test_organization_policy_allows_view_when_tenant_permission_granted(): void
    {
        $tenant = $this->makeTenant();
        $organization = Organization::create([
            'tenant_id' => $tenant->id,
            'code' => 'campus-a',
            'name' => 'Campus A',
            'is_active' => true,
        ]);

        $user = User::factory()->create();

        Permission::firstOrCreate(['name' => 'View:Organization', 'guard_name' => 'web']);

        $role = Role::firstOrCreate([
            'name' => 'org_viewer',
            'guard_name' => 'web',
            'tenant_id' => $tenant->id,
        ]);
        $role->givePermissionTo('View:Organization');

        setPermissionsTeamId($tenant->id);
        $user->roles()->syncWithoutDetaching([
            $role->id => [
                'model_type' => $user->getMorphClass(),
                'tenant_id' => $tenant->id,
            ],
        ]);
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        setPermissionsTeamId($tenant->id);

        $this->assertTrue($user->can('view', $organization));
        $this->assertFalse($user->can('delete', $organization));
    }

    public function test_user_policy_blocks_update_without_permission(): void
    {
        $tenant = $this->makeTenant();
        $actor = User::factory()->create();
        $target = User::factory()->create();

        setPermissionsTeamId($tenant->id);
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $this->assertFalse($actor->can('update', $target));
    }

    private function makeTenant(): Tenant
    {
        $plan = SubscriptionPlan::create([
            'code' => 'policy-plan-'.Str::lower(Str::random(4)),
            'name' => 'Policy Plan',
            'included_modules' => ['core'],
        ]);

        return Tenant::create([
            'uuid' => (string) Str::uuid(),
            'code' => 'policy-tenant-'.Str::lower(Str::random(4)),
            'name' => 'Policy Tenant',
            'subscription_plan_id' => $plan->id,
        ]);
    }

    private function assignPlatformPermission(User $user, string $permission): void
    {
        setPermissionsTeamId(0);

        Permission::firstOrCreate([
            'name' => $permission,
            'guard_name' => 'web',
        ]);

        $role = Role::firstOrCreate(
            ['name' => 'platform_policy_tester', 'guard_name' => 'web'],
        );
        $role->givePermissionTo($permission);

        $user->roles()->syncWithoutDetaching([
            $role->id => [
                'model_type' => $user->getMorphClass(),
                'tenant_id' => 0,
            ],
        ]);

        app(PermissionRegistrar::class)->forgetCachedPermissions();
        setPermissionsTeamId(0);
    }
}
