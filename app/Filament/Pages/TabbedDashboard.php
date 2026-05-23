<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\Charts\OutstandingArApChart;
use App\Filament\Widgets\Charts\PayrollTrendChart;
use App\Filament\Widgets\Charts\RevenueTrendChart;
use App\Filament\Widgets\Charts\WorkflowPendingChart;
use App\Filament\Widgets\ExecutiveStatsOverview;
use App\Filament\Widgets\NavigationGridWidget;
use Filament\Facades\Filament;
use Filament\Pages\Dashboard;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\WidgetConfiguration;
use Modules\Campus\Filament\Widgets\CampusStatsOverview;
use Modules\Core\Models\User;
use Modules\Core\Support\FilamentUi;
use Modules\Employee\Filament\Widgets\EmployeeStatsOverview;
use Modules\Enrollment\Filament\Widgets\EnrollmentStatsOverview;
use Modules\Finance\Filament\Widgets\FinanceStatsOverview;
use Modules\Finance\Filament\Widgets\FinanceStatsWidget;
use Modules\Library\Filament\Widgets\LibraryStatsOverview;
use Modules\Procurement\Filament\Widgets\ProcurementStatsOverview;
use Modules\School\Filament\Widgets\SchoolStatsOverview;
use Modules\School\Filament\Widgets\SchoolStatsWidget;

class TabbedDashboard extends Dashboard
{
    public function getColumns(): int|array
    {
        return [
            'default' => 1,
            'sm' => 2,
            'lg' => 4,
        ];
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components([
            Tabs::make()
                ->persistTabInQueryString()
                ->tabs([
                    Tab::make(FilamentUi::text('Overview'))
                        ->icon('heroicon-o-home')
                        ->schema([
                            $this->widgetsGrid([AccountWidget::class], 1),
                            $this->widgetsGrid([NavigationGridWidget::class], 1),
                        ]),

                    Tab::make(FilamentUi::module('School'))
                        ->icon('heroicon-o-academic-cap')
                        ->schema([
                            $this->widgetsGrid([
                                SchoolStatsWidget::class,
                                SchoolStatsOverview::class,
                            ]),
                        ]),

                    Tab::make(FilamentUi::module('Campus'))
                        ->icon('heroicon-o-building-library')
                        ->schema([
                            $this->widgetsGrid([
                                CampusStatsOverview::class,
                            ]),
                        ]),

                    Tab::make(FilamentUi::module('Enrollment'))
                        ->icon('heroicon-o-clipboard-document-check')
                        ->schema([
                            $this->widgetsGrid([
                                EnrollmentStatsOverview::class,
                            ]),
                        ]),

                    Tab::make(FilamentUi::module('Employee'))
                        ->icon('heroicon-o-identification')
                        ->schema([
                            $this->widgetsGrid([
                                EmployeeStatsOverview::class,
                            ]),
                        ]),

                    Tab::make(FilamentUi::module('Finance'))
                        ->icon('heroicon-o-banknotes')
                        ->schema([
                            $this->widgetsGrid([
                                FinanceStatsWidget::class,
                                FinanceStatsOverview::class,
                                RevenueTrendChart::class,
                                PayrollTrendChart::class,
                                OutstandingArApChart::class,
                                WorkflowPendingChart::class,
                            ]),
                        ]),

                    Tab::make(FilamentUi::text('Executive'))
                        ->icon('heroicon-o-presentation-chart-line')
                        ->visible(fn (): bool => static::canViewExecutiveTab())
                        ->schema([
                            $this->widgetsGrid([
                                ExecutiveStatsOverview::class,
                            ]),
                        ]),

                    Tab::make(FilamentUi::module('Procurement'))
                        ->icon('heroicon-o-truck')
                        ->schema([
                            $this->widgetsGrid([
                                ProcurementStatsOverview::class,
                            ]),
                        ]),

                    Tab::make(FilamentUi::module('Library'))
                        ->icon('heroicon-o-book-open')
                        ->schema([
                            $this->widgetsGrid([
                                LibraryStatsOverview::class,
                            ]),
                        ]),
                ]),
        ]);
    }

    /**
     * @param  array<class-string|WidgetConfiguration>  $widgets
     */
    protected function widgetsGrid(array $widgets, int|string|array|null $columns = null): Grid
    {
        return Grid::make($columns ?? $this->getColumns())
            ->schema(fn (): array => $this->getWidgetsSchemaComponents($widgets));
    }

    public static function canViewExecutiveTab(): bool
    {
        $user = auth()->user();
        if (! $user instanceof User) {
            return false;
        }

        if ($user->isGlobalSuperAdmin()) {
            return true;
        }

        $tenant = Filament::getTenant();
        if (! $tenant) {
            return false;
        }

        return $user->userTenantRoles()
            ->where('tenant_id', $tenant->getKey())
            ->whereHas('tenantRole', fn ($q) => $q->where('is_super_admin', true))
            ->exists();
    }
}
