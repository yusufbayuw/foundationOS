<?php

namespace Modules\Finance\Filament\Pages;

use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Pages\Page;
use Modules\Core\Models\Organization;
use Modules\Core\Support\FilamentUi;
use Modules\Finance\Services\FinancialReportService;

class BalanceSheetPage extends Page
{
    use HasPageShield;

    protected static string $routePath = '/finance/reports/balance-sheet';

    protected string $view = 'finance::filament.pages.balance-sheet';

    protected static ?int $navigationSort = 12;

    public string $asOf = '';

    public ?int $organizationId = null;

    /**
     * @var array<string, mixed>
     */
    public array $reportData = [];

    public function mount(): void
    {
        $this->asOf = now()->toDateString();
        $this->generateReport();
    }

    public static function getNavigationLabel(): string
    {
        return FilamentUi::text('Balance Sheet');
    }

    public static function getNavigationGroup(): string|\UnitEnum|null
    {
        return FilamentUi::module('Finance');
    }

    public function generateReport(): void
    {
        $tenant = Filament::getTenant();
        if (! $tenant) {
            return;
        }

        $service = app(FinancialReportService::class);
        $this->reportData = $service->balanceSheet(
            $tenant->getKey(),
            Carbon::parse($this->asOf),
            $this->organizationId,
        );
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('filter')
                ->label(FilamentUi::text('Filter'))
                ->icon('heroicon-o-funnel')
                ->form([
                    DatePicker::make('as_of')
                        ->label(FilamentUi::text('As Of Date'))
                        ->default($this->asOf)
                        ->required(),
                    Select::make('organization_id')
                        ->label(FilamentUi::field('organization_id'))
                        ->options(fn () => Organization::withoutTenantScope()
                            ->where('tenant_id', Filament::getTenant()?->getKey())
                            ->pluck('name', 'id')
                            ->toArray()
                        )
                        ->placeholder(FilamentUi::text('All Organizations'))
                        ->nullable(),
                ])
                ->action(function (array $data): void {
                    $this->asOf = $data['as_of'];
                    $this->organizationId = $data['organization_id'] ? (int) $data['organization_id'] : null;
                    $this->generateReport();
                }),

            Action::make('exportPdf')
                ->label(FilamentUi::text('Export PDF'))
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->url(fn () => route('finance.reports.balance-sheet.pdf', [
                    'as_of' => $this->asOf,
                    'organization_id' => $this->organizationId,
                ]))
                ->openUrlInNewTab(),
        ];
    }
}
