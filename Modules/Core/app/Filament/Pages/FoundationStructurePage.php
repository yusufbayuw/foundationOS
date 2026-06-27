<?php

namespace Modules\Core\Filament\Pages;

use App\Support\TypedValue;
use Filament\Facades\Filament;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Collection;
use Modules\Core\Models\Organization;
use Modules\Core\Support\FilamentUi;

class FoundationStructurePage extends Page
{
    protected static \BackedEnum|string|null $navigationIcon = Heroicon::BuildingOffice2;

    protected static ?int $navigationSort = 5;

    protected string $view = 'core::filament.pages.foundation-structure';

    /** @var Collection<int, Organization> */
    public Collection $tree;

    public function mount(): void
    {
        $tenant = Filament::getTenant();
        $tenantId = TypedValue::tenantKey($tenant?->getKey());

        $this->tree = Organization::query()
            ->when($tenantId !== null, fn ($q) => $q->where($q->getModel()->qualifyColumn('tenant_id'), $tenantId))
            ->whereNull('parent_organization_id')
            ->with('childOrganizations')
            ->orderBy('name')
            ->get();
    }

    public function getTitle(): string
    {
        return FilamentUi::text('Foundation structure');
    }

    public static function getNavigationLabel(): string
    {
        return FilamentUi::text('Foundation structure');
    }

    public static function getNavigationGroup(): ?string
    {
        return FilamentUi::module('Core');
    }
}
