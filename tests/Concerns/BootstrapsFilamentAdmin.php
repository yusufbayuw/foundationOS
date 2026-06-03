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

trait BootstrapsFilamentAdmin
{
    protected function tearDownFilamentAdmin(): void
    {
        Filament::setTenant(null);
        app(CurrentTenant::class)->forget();
    }

    /**
     * @param  list<string>  $modules
     * @return array{tenant: Tenant, organization: Organization, user: User, role: TenantRole}
     */
    protected function bootstrapFilamentAdmin(array $modules): array
    {
        $context = $this->makeTenantContext($modules);
        $superAdmin = User::factory()->superAdmin()->create();

        UserTenantRole::create([
            'user_id' => $superAdmin->id,
            'tenant_id' => $context['tenant']->id,
            'organization_id' => $context['organization']->id,
            'tenant_role_id' => $context['role']->id,
            'assigned_by' => $superAdmin->id,
            'is_primary' => true,
        ]);

        app(ApplicationModuleCatalog::class)->sync();
        app(TenantModuleProvisioner::class)->enableForTenant($context['tenant'], $modules);

        TenantModule::query()
            ->where('tenant_id', $context['tenant']->id)
            ->update(['is_enabled' => true]);

        Filament::setCurrentPanel('admin');
        $this->actingAs($superAdmin);
        app(CurrentTenant::class)->set($context['tenant']);
        Filament::setTenant($context['tenant']);

        return array_merge($context, ['user' => $superAdmin]);
    }
}
