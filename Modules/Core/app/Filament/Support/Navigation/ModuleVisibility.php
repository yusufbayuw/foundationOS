<?php

namespace Modules\Core\Filament\Support\Navigation;

use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use Modules\Core\Models\TenantModule;

class ModuleVisibility
{
    /** Core and Global are always required; all other modules are gated by TenantModule. */
    private const ALWAYS_VISIBLE_MODULES = ['Core', 'Global'];

    public static function shouldRegisterNavigation(string $module): bool
    {
        if (in_array($module, self::ALWAYS_VISIBLE_MODULES, true)) {
            return true;
        }

        $tenant = Filament::getTenant();

        if (! $tenant) {
            return true;
        }

        return in_array(strtolower($module), self::enabledModuleCodes($tenant->getKey()), true);
    }

    /**
     * @return list<string> lowercase module codes enabled for the tenant
     */
    public static function enabledModuleCodes(int|string $tenantId): array
    {
        return Cache::remember(
            self::enabledModulesCacheKey($tenantId),
            now()->addMinutes(5),
            fn (): array => TenantModule::query()
                ->where('tenant_id', $tenantId)
                ->where('is_enabled', true)
                ->whereHas('module', fn (Builder $query) => $query->where('is_active', true))
                ->with('module:id,code')
                ->get()
                ->pluck('module.code')
                ->filter()
                ->map(fn (string $code): string => strtolower($code))
                ->unique()
                ->values()
                ->all(),
        );
    }

    public static function forgetForTenant(int|string $tenantId): void
    {
        Cache::forget(self::enabledModulesCacheKey($tenantId));
    }

    public static function enabledModulesCacheKey(int|string $tenantId): string
    {
        return "tenant_enabled_module_codes:{$tenantId}";
    }

    /** @deprecated Use forgetForTenant() */
    public static function cacheKey(int|string $tenantId, string $module): string
    {
        return "tenant_module_active:{$tenantId}:{$module}";
    }
}
