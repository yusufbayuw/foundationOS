<?php

namespace App\Filament\Widgets\Charts;

use App\Filament\Widgets\Charts\Concerns\CachesChartData;
use Filament\Facades\Filament;
use Filament\Widgets\ChartWidget;
use Modules\Core\Support\FilamentUi;
use Modules\Finance\Models\Payment;

class RevenueTrendChart extends ChartWidget
{
    use CachesChartData;

    protected static ?int $sort = 10;

    protected ?string $heading = null;

    protected int|string|array $columnSpan = 'full';

    protected function getType(): string
    {
        return 'line';
    }

    public function getHeading(): ?string
    {
        return FilamentUi::text('Revenue trend');
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

        return $this->rememberChart('revenue_trend', function () use ($tenantId, $days): array {
            $data = $this->dailySumSeries(
                Payment::class,
                $tenantId,
                $days,
                'payment_date',
                'amount',
                fn ($query) => $query->where('status', 'verified'),
            );

            return [
                'datasets' => [
                    [
                        'label' => FilamentUi::text('Revenue'),
                        'data' => $data,
                        'borderColor' => '#6366f1',
                    ],
                ],
                'labels' => $this->dateLabels($days),
            ];
        });
    }
}
