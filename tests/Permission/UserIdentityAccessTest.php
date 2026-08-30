<?php

namespace Tests\Permission;

use App\Models\User;
use Filament\Panel;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Str;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\TenantRole;
use Modules\Core\Models\UserTenantRole;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class UserIdentityAccessTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_user_can_access_only_assigned_tenants(): void
    {
        $user = User::factory()->create();
        $assignedTenant = $this->createTenant('assigned-tenant');
        $foreignTenant = $this->createTenant('foreign-tenant');

        $this->assignUserToTenant($user, $assignedTenant);

        $this->assertTrue($user->canAccessTenant($assignedTenant));
        $this->assertFalse($user->canAccessTenant($foreignTenant));
    }

    public function test_user_get_tenants_returns_distinct_tenants_ordered_by_name(): void
    {
        $user = User::factory()->create();
        $zetaTenant = $this->createTenant('zeta-tenant', 'Zeta Tenant');
        $alphaTenant = $this->createTenant('alpha-tenant', 'Alpha Tenant');

        $this->assignUserToTenant($user, $zetaTenant);
        $this->assignUserToTenant($user, $alphaTenant);

        $tenantNames = $user->getTenants(Panel::make()->id('admin'))->pluck('name')->all();

        $this->assertSame(['Alpha Tenant', 'Zeta Tenant'], $tenantNames);
    }

    public function test_user_get_default_tenant_prefers_primary_assignment(): void
    {
        $user = User::factory()->create();
        $firstTenant = $this->createTenant('first-tenant', 'First Tenant');
        $primaryTenant = $this->createTenant('primary-tenant', 'Primary Tenant');

        $this->assignUserToTenant($user, $firstTenant);
        $this->assignUserToTenant($user, $primaryTenant, isPrimary: true);

        $this->assertTrue($primaryTenant->is($user->getDefaultTenant(Panel::make()->id('admin'))));
    }

    public function test_user_get_default_tenant_falls_back_to_first_ordered_tenant(): void
    {
        $user = User::factory()->create();
        $zetaTenant = $this->createTenant('zeta-fallback', 'Zeta Fallback');
        $alphaTenant = $this->createTenant('alpha-fallback', 'Alpha Fallback');

        $this->assignUserToTenant($user, $zetaTenant);
        $this->assignUserToTenant($user, $alphaTenant);

        $this->assertTrue($alphaTenant->is($user->getDefaultTenant(Panel::make()->id('admin'))));
    }

    public function test_user_panel_access_respects_onboarding_platform_admin_and_unknown_panel_rules(): void
    {
        $onboardingUser = User::factory()->create();
        $formerTenantOwner = User::factory()->create();
        $tenantUser = User::factory()->create();
        $superAdmin = User::factory()->superAdmin()->create();
        $platformOwner = $this->createPlatformOwner();

        $this->createTenant('former-owner-tenant')->update([
            'created_by' => $formerTenantOwner->getKey(),
        ]);
        $this->assignUserToTenant($tenantUser, $this->createTenant('panel-tenant'));

        $this->assertTrue($onboardingUser->canAccessPanel(Panel::make()->id('admin')));
        $this->assertFalse($formerTenantOwner->canAccessPanel(Panel::make()->id('admin')));
        $this->assertTrue($tenantUser->canAccessPanel(Panel::make()->id('admin')));
        $this->assertTrue($superAdmin->canAccessPanel(Panel::make()->id('admin')));

        $this->assertFalse($onboardingUser->canAccessPanel(Panel::make()->id('platform')));
        $this->assertTrue($platformOwner->canAccessPanel(Panel::make()->id('platform')));
        $this->assertFalse($platformOwner->canAccessPanel(Panel::make()->id('unknown')));
    }

    private function createTenant(string $code, ?string $name = null): Tenant
    {
        return Tenant::query()->create([
            'uuid' => (string) Str::uuid(),
            'code' => $code,
            'name' => $name ?? str($code)->headline()->toString(),
            'status' => 'active',
        ]);
    }

    private function assignUserToTenant(User $user, Tenant $tenant, bool $isPrimary = false): void
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
            'is_primary' => $isPrimary,
        ]);
    }

    private function createPlatformOwner(): User
    {
        $user = User::factory()->create();
        $role = Role::firstOrCreate(['name' => 'platform_owner', 'guard_name' => 'web']);

        setPermissionsTeamId(0);

        $user->roles()->syncWithoutDetaching([
            $role->id => [
                'model_type' => $user->getMorphClass(),
                'tenant_id' => 0,
            ],
        ]);

        app(PermissionRegistrar::class)->forgetCachedPermissions();
        setPermissionsTeamId(0);

        return $user->fresh();
    }
}
