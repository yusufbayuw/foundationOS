<?php

namespace App\Filament\Widgets\Charts;

use App\Filament\Widgets\Charts\Concerns\CachesChartData;
use App\Services\ExecutiveMetricsService;
use App\Support\TypedValue;
use Filament\Facades\Filament;
use Filament\Widgets\ChartWidget;
use Modules\Core\Support\FilamentUi;

class OutstandingArApChart extends ChartWidget
{
    use CachesChartData;

    protected static ?int $sort = 12;

    protected int|string|array $columnSpan = 1;

    protected function getType(): string
    {
        return 'doughnut';
    }

    public function getHeading(): ?string
    {
        return FilamentUi::text('Outstanding AR vs AP');
    }

    protected function getData(): array
    {
        $tenantId = Filament::getTenant()?->getKey();
        if (! $tenantId) {
            return ['datasets' => [], 'labels' => []];
        }

        return $this->rememberChart('ar_ap', function () use ($tenantId): array {
            $metrics = app(ExecutiveMetricsService::class)->forTenant(TypedValue::int($tenantId));

            return [
                'datasets' => [
                    [
                        'data' => [
                            $metrics['outstanding_ar'],
                            $metrics['outstanding_ap'],
                        ],
                        'backgroundColor' => ['#ef4444', '#f97316'],
                    ],
                ],
                'labels' => [
                    FilamentUi::text('Outstanding AR'),
                    FilamentUi::text('Outstanding AP'),
                ],
            ];
        });
    }
}
