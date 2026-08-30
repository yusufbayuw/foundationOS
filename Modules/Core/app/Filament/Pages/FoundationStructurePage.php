<?php

namespace Modules\Core\Filament\Pages;

use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Facades\Filament;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Collection;
use Modules\Core\Models\Organization;
use Modules\Core\Support\FilamentUi;

class FoundationStructurePage extends Page
{
    use HasPageShield;

    protected static \BackedEnum|string|null $navigationIcon = Heroicon::BuildingOffice2;

    protected static ?int $navigationSort = 5;

    protected string $view = 'core::filament.pages.foundation-structure';

    /** @var Collection<int, Organization> */
    public Collection $tree;

    public function mount(): void
    {
        $tenant = Filament::getTenant();

        $this->tree = Organization::query()
            ->when($tenant, fn ($q) => $q->where('tenant_id', $tenant->getKey()))
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
