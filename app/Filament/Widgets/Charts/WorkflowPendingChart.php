<?php

namespace App\Filament\Widgets\Charts;

use App\Filament\Widgets\Charts\Concerns\CachesChartData;
use Filament\Facades\Filament;
use Filament\Widgets\ChartWidget;
use Modules\Core\Support\FilamentUi;
use Modules\Workflow\Models\WorkflowInstance;

class WorkflowPendingChart extends ChartWidget
{
    use CachesChartData;

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
            $data = $this->dailyCumulativeCountSeries(
                WorkflowInstance::class,
                $tenantId,
                $days,
                'created_at',
                fn ($query) => $query->whereIn('status', ['pending', 'in_progress']),
            );

            return [
                'datasets' => [
                    [
                        'label' => FilamentUi::text('Pending Approvals'),
                        'data' => $data,
                        'backgroundColor' => '#8b5cf6',
                    ],
                ],
                'labels' => $this->dateLabels($days),
            ];
        });
    }
}
