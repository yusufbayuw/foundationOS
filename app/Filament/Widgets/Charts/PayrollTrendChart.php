<?php

namespace App\Filament\Widgets\Charts;

use App\Filament\Widgets\Charts\Concerns\CachesChartData;
use Filament\Facades\Filament;
use Filament\Widgets\ChartWidget;
use Modules\Core\Support\FilamentUi;
use Modules\Employee\Models\SalarySlip;

class PayrollTrendChart extends ChartWidget
{
    use CachesChartData;

    protected static ?int $sort = 11;

    protected int|string|array $columnSpan = 'full';

    protected function getType(): string
    {
        return 'line';
    }

    public function getHeading(): ?string
    {
        return FilamentUi::text('Payroll trend');
    }

    protected function getData(): array
    {
        $tenantId = Filament::getTenant()?->getKey();
        if (! $tenantId) {
            return ['datasets' => [], 'labels' => []];
        }

        $days = match ($this->chartPeriod) {
            '7' => 7,
            '90' => 90,
            default => 30,
        };

        return $this->rememberChart('payroll_trend', function () use ($tenantId, $days): array {
            $data = $this->dailySumSeries(
                SalarySlip::class,
                $tenantId,
                $days,
                'created_at',
                'net_salary',
                fn ($query) => $query->whereIn('status', ['approved', 'paid']),
            );

            return [
                'datasets' => [
                    [
                        'label' => FilamentUi::text('Payroll'),
                        'data' => $data,
                        'borderColor' => '#f59e0b',
                    ],
                ],
                'labels' => $this->dateLabels($days),
            ];
        });
    }
}
