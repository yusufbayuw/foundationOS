<?php

namespace Modules\Core\Support\Filament;

use Illuminate\Support\Facades\Cache;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\TenantSetting;

class TenantBrandingResolver
{
    public function forTenant(?Tenant $tenant): TenantBranding
    {
        if ($tenant === null) {
            return TenantBranding::default();
        }

        return $this->resolveCached($tenant);
    }

    public function forget(int|string $tenantId): void
    {
        Cache::forget(self::cacheKey($tenantId));
    }

    public static function cacheKey(int|string $tenantId): string
    {
        return "tenant_branding:{$tenantId}";
    }

    private function resolveCached(Tenant $tenant): TenantBranding
    {
        /** @var array<string, string> $settings */
        $settings = Cache::remember(
            self::cacheKey($tenant->getKey()),
            now()->addHours(24),
            fn (): array => TenantSetting::query()
                ->where('tenant_id', $tenant->getKey())
                ->where('group', 'branding')
                ->whereIn('key', ['brand_logo', 'primary_color'])
                ->pluck('value', 'key')
                ->all(),
        );

        return new TenantBranding(
            primaryColor: $settings['primary_color'] ?? null,
            brandLogo: $settings['brand_logo'] ?? null,
        );
    }
}
