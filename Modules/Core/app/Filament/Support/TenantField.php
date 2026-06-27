<?php

namespace Modules\Core\Filament\Support;

use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Modules\Core\Models\AcademicPeriod;
use Modules\Core\Services\ContextDefaults;
use Modules\Core\Support\FilamentUi;

class TenantField
{
    /**
     * Create a hidden tenant_id field that auto-fills from CurrentTenant.
     */
    public static function make(): Hidden
    {
        return Hidden::make('tenant_id')
            ->default(fn (): int|string|null => current_tenant_id())
            ->dehydrateStateUsing(fn (): int|string|null => current_tenant_id());
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
                fn ($query) => $query->where('tenant_id', current_tenant_id()),
            );
    }

    /**
     * Hidden organization_id defaulting from the user's primary tenant membership.
     */
    public static function organizationHidden(): Hidden
    {
        return Hidden::make('organization_id')
            ->default(fn (): int|string|null => app(ContextDefaults::class)->resolveOrganizationId(
                auth()->user(),
                current_tenant_id(),
            ))
            ->dehydrateStateUsing(fn (): int|string|null => app(ContextDefaults::class)->resolveOrganizationId(
                auth()->user(),
                current_tenant_id(),
            ));
    }

    /**
     * Hidden academic_period_id defaulting to the tenant's active period.
     */
    public static function academicPeriodHidden(): Hidden
    {
        return Hidden::make('academic_period_id')
            ->default(fn (): int|string|null => app(ContextDefaults::class)->resolveAcademicPeriodId(
                current_tenant_id(),
            ))
            ->dehydrateStateUsing(fn (): int|string|null => app(ContextDefaults::class)->resolveAcademicPeriodId(
                current_tenant_id(),
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
                ->where('tenant_id', current_tenant_id())
                ->orderByDesc('is_active')
                ->orderByDesc('start_date')
                ->pluck('name', 'id')
                ->all());
    }
}
