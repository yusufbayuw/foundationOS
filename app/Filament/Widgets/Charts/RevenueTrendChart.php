<?php

namespace App\Filament\Widgets\Charts;

use App\Filament\Widgets\Charts\Concerns\CachesChartData;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Facades\Filament;
use Filament\Widgets\ChartWidget;
use Modules\Core\Support\FilamentUi;
use Modules\Finance\Models\Payment;

class RevenueTrendChart extends ChartWidget
{
    use CachesChartData;
    use HasWidgetShield;

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
            $labels = $this->dateLabels($days);
            $data = [];

            for ($i = $days - 1; $i >= 0; $i--) {
                $date = now()->subDays($i)->toDateString();
                $data[] = (float) Payment::query()
                    ->where('tenant_id', $tenantId)
                    ->where('status', 'verified')
                    ->whereDate('payment_date', $date)
                    ->sum('amount');
            }

            return [
                'datasets' => [
                    [
                        'label' => FilamentUi::text('Revenue'),
                        'data' => $data,
                        'borderColor' => '#6366f1',
                    ],
                ],
                'labels' => $labels,
            ];
        });
    }
}
