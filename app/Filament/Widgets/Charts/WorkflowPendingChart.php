<?php

namespace App\Filament\Widgets\Charts;

use App\Filament\Widgets\Charts\Concerns\CachesChartData;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Facades\Filament;
use Filament\Widgets\ChartWidget;
use Modules\Core\Support\FilamentUi;
use Modules\Workflow\Models\WorkflowInstance;

class WorkflowPendingChart extends ChartWidget
{
    use CachesChartData;
    use HasWidgetShield;

    protected static ?int $sort = 13;

    protected int|string|array $columnSpan = 1;

    protected function getType(): string
    {
        return 'bar';
    }

    public function getHeading(): ?string
    {
        return FilamentUi::text('Pending approvals trend');
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

        return $this->rememberChart('workflow_pending', function () use ($tenantId, $days): array {
            $labels = $this->dateLabels($days);
            $data = [];

            for ($i = $days - 1; $i >= 0; $i--) {
                $date = now()->subDays($i);
                $data[] = (int) WorkflowInstance::query()
                    ->where('tenant_id', $tenantId)
                    ->whereIn('status', ['pending', 'in_progress'])
                    ->whereDate('created_at', '<=', $date)
                    ->count();
            }

            return [
                'datasets' => [
                    [
                        'label' => FilamentUi::text('Pending Approvals'),
                        'data' => $data,
                        'backgroundColor' => '#8b5cf6',
                    ],
                ],
                'labels' => $labels,
            ];
        });
    }
}
