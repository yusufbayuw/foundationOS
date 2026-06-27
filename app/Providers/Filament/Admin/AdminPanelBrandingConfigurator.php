<?php

namespace App\Providers\Filament\Admin;

use Filament\Panel;
use Modules\Core\Support\Filament\TenantBrandingResolver;

class AdminPanelBrandingConfigurator
{
    public function __construct(
        private readonly TenantBrandingResolver $brandingResolver,
    ) {}

    public function configure(Panel $panel): Panel
    {
        return $panel
            ->colors(fn (): array => $this->brandingResolver
                ->forTenant(filament()->getTenant())
                ->filamentColors())
            ->brandLogo(fn (): ?string => $this->brandingResolver
                ->forTenant(filament()->getTenant())
                ->filamentLogoUrl());
    }
}
