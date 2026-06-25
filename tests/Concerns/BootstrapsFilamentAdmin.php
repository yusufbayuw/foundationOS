<?php

namespace Tests\Concerns;

use App\Support\CurrentTenant;
use Filament\Facades\Filament;
use Modules\Core\Models\Organization;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\TenantModule;
use Modules\Core\Models\TenantRole;
use Modules\Core\Models\User;
use Modules\Core\Models\UserTenantRole;
use Modules\Core\Services\ApplicationModuleCatalog;
use Modules\Core\Services\TenantModuleProvisioner;
use Spatie\Permission\PermissionRegistrar;

trait BootstrapsFilamentAdmin
{
    protected function tearDownFilamentAdmin(): void
    {
        Filament::setTenant(null);
        app(CurrentTenant::class)->forget();
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    /**
     * @param  list<string>  $modules
     * @return array{tenant: Tenant, organization: Organization, user: User, role: TenantRole}
     */
    protected function bootstrapFilamentAdmin(array $modules): array
    {
        return $this->bootstrapFilamentActor($modules, superAdmin: true);
    }

    /**
     * Tenant member without global super-admin privileges or Shield permissions.
     *
     * @param  list<string>  $modules
     * @return array{tenant: Tenant, organization: Organization, user: User, role: TenantRole}
     */
    protected function bootstrapFilamentTenantMember(array $modules): array
    {
        return $this->bootstrapFilamentActor($modules, superAdmin: false);
    }

    /**
     * Switch the current Filament actor to a tenant member without super-admin or Shield permissions.
     *
     * @param  array{tenant: Tenant, organization: Organization, role: TenantRole}  $context
     */
    protected function actAsFilamentTenantMember(array $context): User
    {
        $member = User::factory()->create();

        UserTenantRole::create([
            'user_id' => $member->id,
            'tenant_id' => $context['tenant']->id,
            'organization_id' => $context['organization']->id,
            'tenant_role_id' => $context['role']->id,
            'assigned_by' => $member->id,
            'is_primary' => true,
        ]);

        $this->actingAs($member);
        app(CurrentTenant::class)->set($context['tenant']);
        Filament::setTenant($context['tenant']);
        setPermissionsTeamId($context['tenant']->id);

        return $member;
    }

    /**
     * @param  list<string>  $modules
     * @return array{tenant: Tenant, organization: Organization, user: User, role: TenantRole}
     */
    protected function bootstrapFilamentActor(array $modules, bool $superAdmin): array
    {
        $context = $this->makeTenantContext($modules);
        $actor = $superAdmin
            ? User::factory()->superAdmin()->create()
            : User::factory()->create();

        UserTenantRole::create([
            'user_id' => $actor->id,
            'tenant_id' => $context['tenant']->id,
            'organization_id' => $context['organization']->id,
            'tenant_role_id' => $context['role']->id,
            'assigned_by' => $actor->id,
            'is_primary' => true,
        ]);

        app(ApplicationModuleCatalog::class)->sync();
        app(TenantModuleProvisioner::class)->enableForTenant($context['tenant'], $modules);

        TenantModule::query()
            ->where('tenant_id', $context['tenant']->id)
            ->update(['is_enabled' => true]);

        Filament::setCurrentPanel('admin');
        $this->actingAs($actor);
        app(CurrentTenant::class)->set($context['tenant']);
        Filament::setTenant($context['tenant']);
        setPermissionsTeamId($context['tenant']->id);

        return array_merge($context, ['user' => $actor]);
    }
}
