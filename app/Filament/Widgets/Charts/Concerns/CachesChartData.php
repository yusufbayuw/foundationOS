<?php

namespace App\Filament\Widgets\Charts\Concerns;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

trait CachesChartData
{
    public ?string $chartPeriod = '30';

    protected function chartCacheKey(string $suffix): string
    {
        $tenantId = current_tenant_id() ?? 'global';

        return "chart:{$suffix}:{$tenantId}:{$this->chartPeriod}";
    }

    /**
     * @return array{0: Carbon, 1: Carbon}
     */
    protected function periodRange(): array
    {
        $days = match ($this->chartPeriod) {
            '7' => 7,
            '90' => 90,
            default => 30,
        };

        return [now()->subDays($days)->startOfDay(), now()->endOfDay()];
    }

    /**
     * @param  callable(): array  $resolver
     */
    protected function rememberChart(string $suffix, callable $resolver): array
    {
        return Cache::remember($this->chartCacheKey($suffix), now()->addMinutes(5), $resolver);
    }

    /**
     * @return array<int, string>
     */
    protected function dateLabels(int $days): array
    {
        $labels = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $labels[] = now()->subDays($i)->format('d M');
        }

        return $labels;
    }
}
