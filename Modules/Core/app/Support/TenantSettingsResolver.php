<?php

namespace Modules\Core\Support;

use Illuminate\Support\Facades\Cache;
use Modules\Core\Models\TenantSetting;

class TenantSettingsResolver
{
    public function value(int|string $tenantId, string $group, string $key, mixed $default = null): mixed
    {
        return $this->group($tenantId, $group)[$key] ?? $default;
    }

    /**
     * @return array<string, string>
     */
    public function group(int|string $tenantId, string $group): array
    {
        return Cache::remember(
            self::cacheKey($tenantId, $group),
            now()->addHours(24),
            fn (): array => TenantSetting::query()
                ->where('tenant_id', $tenantId)
                ->where('group', $group)
                ->pluck('value', 'key')
                ->all(),
        );
    }

    public function forgetGroup(int|string $tenantId, string $group): void
    {
        Cache::forget(self::cacheKey($tenantId, $group));
    }

    public static function cacheKey(int|string $tenantId, string $group): string
    {
        return "tenant_settings:{$tenantId}:{$group}";
    }
}
