<?php

namespace Tests\Permission;

use App\Models\Role;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Str;
use Modules\Alumni\Filament\Resources\AlumniDonations\AlumniDonationResource;
use Modules\Core\Models\SubscriptionPlan;
use Modules\Core\Models\Tenant;
use Modules\Core\Services\TenantAdminProvisioner;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class ShieldTeamPermissionTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_permissions_are_isolated_per_tenant_team(): void
    {
        $tenantA = $this->makeTenant('tenant-a');
        $tenantB = $this->makeTenant('tenant-b');
        $user = User::factory()->create();

        Permission::firstOrCreate(['name' => 'View:Vendor', 'guard_name' => 'web']);

        $role = Role::firstOrCreate([
            'name' => 'vendor_viewer',
            'guard_name' => 'web',
            'tenant_id' => $tenantA->id,
        ]);
        $role->givePermissionTo('View:Vendor');

        setPermissionsTeamId($tenantA->id);
        $user->roles()->syncWithoutDetaching([
            $role->id => [
                'model_type' => $user->getMorphClass(),
                'tenant_id' => $tenantA->id,
            ],
        ]);
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        setPermissionsTeamId($tenantA->id);
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        $this->assertTrue($user->fresh()->can('View:Vendor'));

        setPermissionsTeamId($tenantB->id);
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        $this->assertFalse($user->fresh()->can('View:Vendor'));
    }

    public function test_role_assignment_is_scoped_to_tenant_team(): void
    {
        $tenant = $this->makeTenant('team-scope');
        $user = User::factory()->create();

        $role = Role::firstOrCreate([
            'name' => 'finance_manager',
            'guard_name' => 'web',
            'tenant_id' => $tenant->id,
        ]);

        setPermissionsTeamId($tenant->id);
        $user->roles()->syncWithoutDetaching([
            $role->id => [
                'model_type' => $user->getMorphClass(),
                'tenant_id' => $tenant->id,
            ],
        ]);
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        setPermissionsTeamId($tenant->id);
        $this->assertTrue($user->hasRole('finance_manager'));

        $this->assertDatabaseHas('model_has_roles', [
            'model_id' => $user->id,
            'tenant_id' => $tenant->id,
            'role_id' => $role->id,
        ]);

        $this->assertDatabaseMissing('model_has_roles', [
            'model_id' => $user->id,
            'tenant_id' => 0,
            'role_id' => $role->id,
        ]);
    }

    public function test_super_admin_role_grants_all_permissions_within_tenant(): void
    {
        $tenant = $this->makeTenant('super-admin-team');
        $user = User::factory()->create();

        Permission::firstOrCreate(['name' => 'ViewAny:Workflow', 'guard_name' => 'web']);

        $role = Role::firstOrCreate([
            'name' => 'super_admin',
            'guard_name' => 'web',
            'tenant_id' => $tenant->id,
        ]);
        $role->givePermissionTo('ViewAny:Workflow');

        setPermissionsTeamId($tenant->id);
        $user->roles()->syncWithoutDetaching([
            $role->id => [
                'model_type' => $user->getMorphClass(),
                'tenant_id' => $tenant->id,
            ],
        ]);
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        setPermissionsTeamId($tenant->id);
        $this->assertTrue($user->can('ViewAny:Workflow'));
    }

    public function test_provisioned_super_admin_role_bypasses_permissions_only_within_its_tenant(): void
    {
        $tenantA = $this->makeTenant('provisioned-super-admin-a');
        $tenantB = $this->makeTenant('provisioned-super-admin-b');
        $user = User::factory()->create();

        Permission::firstOrCreate(['name' => 'ViewAny:Budget', 'guard_name' => 'web']);

        app(TenantAdminProvisioner::class)->assignShieldSuperAdmin($user, $tenantA);

        setPermissionsTeamId($tenantA->id);
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        $this->assertTrue($user->fresh()->can('ViewAny:Budget'));

        setPermissionsTeamId($tenantB->id);
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        $this->assertFalse($user->fresh()->can('ViewAny:Budget'));

        setPermissionsTeamId(0);
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        $this->assertFalse($user->fresh()->can('ViewAny:Budget'));
    }

    public function test_generated_resource_policy_denies_by_default_and_honors_tenant_permission(): void
    {
        $tenant = $this->makeTenant('generated-policy');
        $user = User::factory()->create();

        Filament::setCurrentPanel('admin');
        $this->actingAs($user);
        Filament::setTenant($tenant);
        setPermissionsTeamId($tenant->id);

        Permission::firstOrCreate([
            'name' => 'ViewAny:AlumniDonation',
            'guard_name' => 'web',
        ]);

        $this->assertFalse(AlumniDonationResource::canViewAny());

        $role = Role::firstOrCreate([
            'name' => 'alumni_donation_viewer',
            'guard_name' => 'web',
            'tenant_id' => $tenant->id,
        ]);
        $role->givePermissionTo('ViewAny:AlumniDonation');
        $user->roles()->syncWithoutDetaching([
            $role->id => [
                'model_type' => $user->getMorphClass(),
                'tenant_id' => $tenant->id,
            ],
        ]);
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $this->assertTrue($user->fresh()->can('ViewAny:AlumniDonation'));
        $this->actingAs($user->fresh());
        $this->assertTrue(AlumniDonationResource::canViewAny());
    }

    private function makeTenant(string $code): Tenant
    {
        $plan = SubscriptionPlan::firstOrCreate(
            ['code' => $code.'-plan'],
            ['name' => 'Test Plan', 'included_modules' => ['core']],
        );

        return Tenant::create([
            'uuid' => (string) Str::uuid(),
            'code' => $code,
            'name' => strtoupper($code),
            'subscription_plan_id' => $plan->id,
        ]);
    }
}
