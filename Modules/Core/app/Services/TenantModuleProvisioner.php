<?php

namespace Modules\Core\Services;

use Illuminate\Support\Facades\Cache;
use Modules\Core\Models\Module;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\TenantModule;

class TenantModuleProvisioner
{
    public function __construct(
        protected ApplicationModuleCatalog $catalog,
    ) {}

    /**
     * Enable modules for a tenant. When $moduleCodes is null, all active catalog modules are enabled.
     *
     * @param  list<string>|null  $moduleCodes  Lowercase module codes (e.g. school, finance).
     */
    public function enableForTenant(Tenant $tenant, ?array $moduleCodes = null): void
    {
        $this->catalog->sync();

        $query = Module::query()->where('is_active', true);

        if ($moduleCodes !== null) {
            $query->whereIn('code', array_map('strtolower', $moduleCodes));
        }

        $modules = $query->get();

        foreach ($modules as $module) {
            TenantModule::query()->updateOrCreate(
                [
                    'tenant_id' => $tenant->getKey(),
                    'module_id' => $module->getKey(),
                ],
                [
                    'is_enabled' => true,
                    'enabled_at' => now(),
                    'disabled_at' => null,
                ],
            );

            Cache::forget("tenant_module_active:{$tenant->getKey()}:".str($module->code)->studly());
        }
    }
}
