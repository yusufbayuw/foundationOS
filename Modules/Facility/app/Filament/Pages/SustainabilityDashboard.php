<?php

namespace Modules\Facility\Filament\Pages;

use Filament\Facades\Filament;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Modules\Core\Support\FilamentUi;
use Modules\Facility\Models\UtilityReading;

class SustainabilityDashboard extends Page
{
    protected static \BackedEnum|string|null $navigationIcon = Heroicon::Sun;

    protected static ?int $navigationSort = 90;

    protected string $view = 'facility::filament.pages.sustainability-dashboard';

    public float $totalCarbonKg = 0;

    public function mount(): void
    {
        $tenantId = Filament::getTenant()?->getKey();

        $this->totalCarbonKg = UtilityReading::query()
            ->when($tenantId, fn ($q) => $q->where('tenant_id', $tenantId))
            ->get()
            ->sum(fn (UtilityReading $reading) => (float) ($reading->reading_value ?? 0) * (float) ($reading->emission_factor ?? 0));
    }

    public function getTitle(): string
    {
        return FilamentUi::text('Sustainability dashboard');
    }

    public static function getNavigationLabel(): string
    {
        return FilamentUi::text('Sustainability dashboard');
    }

    public static function getNavigationGroup(): ?string
    {
        return FilamentUi::module('Facility');
    }
}
