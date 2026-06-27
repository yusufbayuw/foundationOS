<?php

namespace Modules\Core\Support\Filament;

use Modules\Core\Models\Tenant;
use Modules\Core\Support\TenantSettingsResolver;

class TenantBrandingResolver
{
    public function __construct(
        private readonly TenantSettingsResolver $settings,
    ) {}

    public function forTenant(?Tenant $tenant): TenantBranding
    {
        if ($tenant === null) {
            return TenantBranding::default();
        }

        $branding = $this->settings->group($tenant->getKey(), 'branding');

        return new TenantBranding(
            primaryColor: $branding['primary_color'] ?? null,
            brandLogo: $branding['brand_logo'] ?? null,
        );
    }

    public function forget(int|string $tenantId): void
    {
        $this->settings->forgetGroup($tenantId, 'branding');
    }
}
