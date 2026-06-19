<?php

namespace Modules\Core\Filament\Support;

use Filament\Facades\Filament;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Modules\Core\Models\AcademicPeriod;
use Modules\Core\Services\ContextDefaults;
use Modules\Core\Support\FilamentUi;

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
            ->label(FilamentUi::field('organization_id'))
            ->relationship(
                'organization',
                'name',
                fn ($query) => $query->where('tenant_id', Filament::getTenant()?->getKey()),
            );
    }

    /**
     * Hidden organization_id defaulting from the user's primary tenant membership.
     */
    public static function organizationHidden(): Hidden
    {
        return Hidden::make('organization_id')
            ->default(fn () => app(ContextDefaults::class)->resolveOrganizationId(
                auth()->user(),
                Filament::getTenant()?->getKey(),
            ))
            ->dehydrateStateUsing(fn ($state) => $state ?: app(ContextDefaults::class)->resolveOrganizationId(
                auth()->user(),
                Filament::getTenant()?->getKey(),
            ));
    }

    /**
     * Hidden academic_period_id defaulting to the tenant's active period.
     */
    public static function academicPeriodHidden(): Hidden
    {
        return Hidden::make('academic_period_id')
            ->default(fn () => app(ContextDefaults::class)->resolveAcademicPeriodId(
                Filament::getTenant()?->getKey(),
            ))
            ->dehydrateStateUsing(fn ($state) => $state ?: app(ContextDefaults::class)->resolveAcademicPeriodId(
                Filament::getTenant()?->getKey(),
            ));
    }

    /**
     * Select for academic period — use when the user must override the default.
     */
    public static function academicPeriodSelect(): Select
    {
        return Select::make('academic_period_id')
            ->label(FilamentUi::field('academic_period_id'))
            ->options(fn () => AcademicPeriod::query()
                ->where('tenant_id', Filament::getTenant()?->getKey())
                ->orderByDesc('is_active')
                ->orderByDesc('start_date')
                ->pluck('name', 'id')
                ->all());
    }
}
