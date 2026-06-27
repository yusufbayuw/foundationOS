<?php

namespace Modules\Core\Filament\Support\Navigation;

use App\Support\TypedValue;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use Modules\Core\Models\Module;
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

        $tenantId = TypedValue::tenantKey($tenant->getKey());
        if ($tenantId === null) {
            return true;
        }

        return Cache::remember(
            self::cacheKey($tenantId, $module),
            now()->addMinutes(5),
            fn () => TenantModule::query()
                ->whereHas('module', function (Builder $query) use ($module): void {
                    /** @var Builder<Module> $query */
                    $query->where('code', strtolower($module));
                })
                ->where('tenant_id', $tenantId)
                ->where('is_enabled', true)
                ->exists()
        );
    }

    public static function cacheKey(int|string $tenantId, string $module): string
    {
        return "tenant_module_active:{$tenantId}:{$module}";
    }
}
