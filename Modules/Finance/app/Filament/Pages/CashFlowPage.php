<?php

namespace Modules\Finance\Filament\Pages;

use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Pages\Page;
use Modules\Core\Models\Organization;
use Modules\Core\Support\FilamentUi;
use Modules\Finance\Services\FinancialReportService;

class CashFlowPage extends Page
{
    use HasPageShield;

    protected static string $routePath = '/finance/reports/cash-flow';

    protected string $view = 'finance::filament.pages.cash-flow';

    protected static ?int $navigationSort = 13;

    public string $dateFrom = '';

    public string $dateTo = '';

    public ?int $organizationId = null;

    public array $reportData = [];

    public function mount(): void
    {
        $this->dateFrom = now()->startOfYear()->toDateString();
        $this->dateTo = now()->toDateString();
        $this->generateReport();
    }

    public static function getNavigationLabel(): string
    {
        return FilamentUi::text('Cash Flow');
    }

    public static function getNavigationGroup(): string|\UnitEnum|null
    {
        return FilamentUi::module('Finance');
    }

    public function generateReport(): void
    {
        $tenant = current_tenant_model();
        if (! $tenant) {
            return;
        }

        $service = app(FinancialReportService::class);
        $this->reportData = $service->cashFlow(
            $tenant->getKey(),
            Carbon::parse($this->dateFrom),
            Carbon::parse($this->dateTo),
            $this->organizationId,
        );
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('filter')
                ->label(FilamentUi::text('Filter Period'))
                ->icon('heroicon-o-funnel')
                ->form([
                    DatePicker::make('date_from')
                        ->label(FilamentUi::text('From'))
                        ->default($this->dateFrom)
                        ->required(),
                    DatePicker::make('date_to')
                        ->label(FilamentUi::text('To'))
                        ->default($this->dateTo)
                        ->required(),
                    Select::make('organization_id')
                        ->label(FilamentUi::field('organization_id'))
                        ->options(fn () => Organization::withoutTenantScope()
                            ->where('tenant_id', current_tenant_id())
                            ->pluck('name', 'id')
                            ->toArray()
                        )
                        ->placeholder(FilamentUi::text('All Organizations'))
                        ->nullable(),
                ])
                ->action(function (array $data): void {
                    $this->dateFrom = $data['date_from'];
                    $this->dateTo = $data['date_to'];
                    $this->organizationId = $data['organization_id'] ? (int) $data['organization_id'] : null;
                    $this->generateReport();
                }),

            Action::make('exportPdf')
                ->label(FilamentUi::text('Export PDF'))
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->url(fn () => route('finance.reports.cash-flow.pdf', [
                    'from' => $this->dateFrom,
                    'to' => $this->dateTo,
                    'organization_id' => $this->organizationId,
                ]))
                ->openUrlInNewTab(),
        ];
    }
}
