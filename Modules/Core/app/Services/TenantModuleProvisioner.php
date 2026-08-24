<?php

namespace Modules\Core\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Modules\Core\Models\Module;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\TenantModule;

class TenantModuleProvisioner
{
    /**
     * Default modules enabled for new K-12 school tenants (excludes marketplace, printing, campus, etc.).
     *
     * @var list<string>
     */
    public const K12_DEFAULT_MODULE_CODES = [
        'core',
        'global',
        'school',
        'enrollment',
        'finance',
        'employee',
        'library',
        'monitoring',
        'workflow',
        'messaging',
        'exam',
        'counseling',
    ];

    public function __construct(
        protected ApplicationModuleCatalog $catalog,
        protected ModuleDependencyGraph $dependencyGraph,
        protected ProductProfileCatalog $productProfiles,
    ) {}

    public function enableProfileForTenant(Tenant $tenant, string $profileCode): void
    {
        $this->enableForTenant($tenant, $this->productProfiles->moduleCodes($profileCode));
    }

    /**
     * Enable modules for a tenant. When $moduleCodes is null, all active catalog modules are enabled.
     *
     * @param  list<string>|null  $moduleCodes  Lowercase module codes (e.g. school, finance).
     */
    public function enableForTenant(Tenant $tenant, ?array $moduleCodes = null): void
    {
        $this->catalog->sync();

        $availableModules = Module::query()
            ->where('is_active', true)
            ->get();

        $requestedModuleCodes = $moduleCodes === null
            ? $availableModules->pluck('code')->map(fn (string $code): string => Str::lower($code))->all()
            : array_map(fn (string $code): string => Str::lower($code), $moduleCodes);

        $resolvedModuleCodes = $this->dependencyGraph->resolveOrFail(
            $availableModules,
            $requestedModuleCodes,
        );

        $modules = $availableModules->filter(
            fn (Module $module): bool => in_array(Str::lower($module->code), $resolvedModuleCodes, true),
        );

        foreach ($modules as $module) {
            $tenantModule = TenantModule::withTrashed()->firstOrNew([
                'tenant_id' => $tenant->getKey(),
                'module_id' => $module->getKey(),
            ]);

            if ($tenantModule->trashed()) {
                $tenantModule->restore();
            }

            $tenantModule->fill([
                'is_enabled' => true,
                'enabled_at' => now(),
                'disabled_at' => null,
            ])->save();

            Cache::forget("tenant_module_active:{$tenant->getKey()}:".str($module->code)->studly());
        }
    }
}
