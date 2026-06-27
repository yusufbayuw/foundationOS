<?php

namespace Modules\Core\Services;

use App\Models\Role;
use App\Support\TypedValue;
use BezhanSalleh\FilamentShield\Support\Utils as ShieldUtils;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\TenantRole;
use Modules\Core\Models\User;
use Modules\Core\Models\UserTenantRole;
use Spatie\Permission\PermissionRegistrar;

class TenantAdminProvisioner
{
    public function assignShieldSuperAdmin(User $user, Tenant $tenant): void
    {
        setPermissionsTeamId(TypedValue::tenantKey($tenant->getKey()));

        $superAdminRoleName = ShieldUtils::getSuperAdminName();

        $role = Role::firstOrCreate([
            'name' => $superAdminRoleName,
            'guard_name' => 'web',
            'team_id' => TypedValue::int($tenant->getKey()),
        ]);

        $user->roles()->syncWithoutDetaching([
            $role->id => [
                'model_type' => $user->getMorphClass(),
                'tenant_id' => $tenant->getKey(),
            ],
        ]);

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function ensureTenantOwnerRole(User $user, Tenant $tenant, ?int $organizationId = null): TenantRole
    {
        $tenantRole = TenantRole::query()->firstOrCreate(
            [
                'tenant_id' => $tenant->getKey(),
                'slug' => 'owner',
            ],
            [
                'name' => 'Owner',
                'description' => 'Pemilik tenant dan administrator utama.',
                'level' => 100,
                'permissions' => ['*'],
                'is_default' => true,
                'is_super_admin' => true,
                'dashboard_route' => 'filament.admin.pages.dashboard',
            ],
        );

        UserTenantRole::query()->updateOrCreate(
            [
                'user_id' => $user->getKey(),
                'tenant_id' => $tenant->getKey(),
                'tenant_role_id' => $tenantRole->getKey(),
            ],
            [
                'organization_id' => $organizationId,
                'assigned_by' => $user->getKey(),
                'assigned_at' => now(),
                'is_primary' => true,
            ],
        );

        return $tenantRole;
    }

    public function provisionDemoAdmin(User $user, Tenant $tenant, ?int $organizationId = null): void
    {
        $this->ensureTenantOwnerRole($user, $tenant, $organizationId);
        $this->assignShieldSuperAdmin($user, $tenant);
        app(TenantModuleProvisioner::class)->enableForTenant($tenant);
    }
}
