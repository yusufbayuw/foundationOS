<?php

namespace Modules\School\Filament\Pages;

use App\Support\TypedValue;
use BackedEnum;
use Filament\Facades\Filament;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Modules\Core\Support\FilamentUi;
use Modules\School\Services\AcademicAnalyticsService;

class AcademicAnalytics extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::ChartBar;

    protected static ?string $navigationLabel = null;

    protected static ?int $navigationSort = 200;

    protected string $view = 'school::filament.pages.academic-analytics';

    public static function getNavigationGroup(): ?string
    {
        return FilamentUi::module('School');
    }

    public static function getNavigationLabel(): string
    {
        return FilamentUi::text('Academic analytics');
    }

    /**
     * @return array<string, mixed>
     */
    public function getAnalyticsSummary(): array
    {
        $tenant = Filament::getTenant();
        if (! $tenant) {
            return [];
        }

        return app(AcademicAnalyticsService::class)->summaryForTenant(TypedValue::int($tenant->getKey()));
    }
}
