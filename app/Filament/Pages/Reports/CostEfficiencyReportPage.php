<?php

namespace App\Filament\Pages\Reports;

use App\Services\CrossModuleReportService;
use App\Support\TypedValue;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Pages\Page;
use Modules\Core\Support\FilamentUi;

class CostEfficiencyReportPage extends Page
{
    use HasPageShield;

    protected static string $routePath = '/reports/cost-efficiency';

    protected string $view = 'filament.pages.reports.cost-efficiency';

    protected static ?int $navigationSort = 1;

    public string $dateFrom = '';

    public string $dateTo = '';

    public string $periodPreset = '30';

    /**
     * @var array<string, mixed>
     */
    public array $reportData = [];

    public function mount(): void
    {
        $this->applyPreset('30');
        $this->generateReport();
    }

    public static function getNavigationLabel(): string
    {
        return FilamentUi::text('Cost efficiency');
    }

    public static function getNavigationGroup(): string|\UnitEnum|null
    {
        return FilamentUi::text('Reports');
    }

    public function applyPreset(string $days): void
    {
        $this->periodPreset = $days;
        $this->dateTo = now()->toDateString();
        $this->dateFrom = now()->subDays((int) $days)->toDateString();
    }

    public function generateReport(): void
    {
        $tenant = Filament::getTenant();
        if (! $tenant) {
            return;
        }

        $this->reportData = app(CrossModuleReportService::class)->costEfficiency(
            TypedValue::int($tenant->getKey()),
            Carbon::parse($this->dateFrom),
            Carbon::parse($this->dateTo),
        );
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('filter')
                ->label(FilamentUi::text('Filter Period'))
                ->icon('heroicon-o-funnel')
                ->form([
                    Select::make('periodPreset')
                        ->label(FilamentUi::text('Period'))
                        ->options([
                            '7' => FilamentUi::text('Last 7 days'),
                            '30' => FilamentUi::text('Last 30 days'),
                            '90' => FilamentUi::text('Last 90 days'),
                        ])
                        ->default($this->periodPreset),
                    DatePicker::make('dateFrom')
                        ->label(FilamentUi::text('From'))
                        ->default($this->dateFrom)
                        ->required(),
                    DatePicker::make('dateTo')
                        ->label(FilamentUi::text('To'))
                        ->default($this->dateTo)
                        ->required(),
                ])
                ->action(function (array $data): void {
                    if (isset($data['periodPreset'])) {
                        $this->applyPreset(TypedValue::string($data['periodPreset'], '30'));
                    }
                    $this->dateFrom = TypedValue::string($data['dateFrom'], $this->dateFrom);
                    $this->dateTo = TypedValue::string($data['dateTo'], $this->dateTo);
                    $this->generateReport();
                }),
        ];
    }
}
