<?php

namespace Modules\Core\Filament\Support;

use Filament\Facades\Filament;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;

class TenantField
{
    /**
     * Create a hidden tenant_id field that auto-fills from the current Filament tenant.
     *
     * Replaces the manual Select::make('tenant_id') pattern that shouldn't be
     * user-facing in a tenant-aware panel.
     */
    public static function make(): Hidden
    {
        return Hidden::make('tenant_id')
            ->default(fn () => Filament::getTenant()?->getKey())
            ->dehydrateStateUsing(fn ($state) => $state ?: Filament::getTenant()?->getKey());
    }

    /**
     * For resources that need an organization selector scoped to the current tenant.
     */
    public static function organizationSelect(): Select
    {
        return Select::make('organization_id')
            ->label(\Modules\Core\Support\FilamentUi::field('organization_id'))
            ->relationship(
                'organization',
                'name',
                fn ($query) => $query->where('tenant_id', Filament::getTenant()?->getKey()),
            );
    }
}
