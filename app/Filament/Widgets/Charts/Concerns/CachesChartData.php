<?php

namespace App\Filament\Widgets\Charts\Concerns;

use Carbon\Carbon;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;

trait CachesChartData
{
    public ?string $chartPeriod = '30';

    protected function chartCacheKey(string $suffix): string
    {
        $tenantId = Filament::getTenant()?->getKey() ?? 'global';

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

    /**
     * @param  \Closure(Builder): void  $scope
     * @return list<float>
     */
    protected function dailySumSeries(
        string $modelClass,
        int|string $tenantId,
        int $days,
        string $dateColumn,
        string $sumColumn,
        \Closure $scope,
    ): array {
        $start = now()->subDays($days - 1)->startOfDay();

        /** @var Builder $query */
        $query = $modelClass::query()
            ->where('tenant_id', $tenantId)
            ->where($dateColumn, '>=', $start);

        $scope($query);

        $totals = $query
            ->selectRaw("DATE({$dateColumn}) as series_date, SUM({$sumColumn}) as total")
            ->groupBy('series_date')
            ->pluck('total', 'series_date');

        $data = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            $data[] = (float) ($totals[$date] ?? 0);
        }

        return $data;
    }

    /**
     * @param  \Closure(Builder): void  $scope
     * @return list<int>
     */
    protected function dailyCumulativeCountSeries(
        string $modelClass,
        int|string $tenantId,
        int $days,
        string $dateColumn,
        \Closure $scope,
    ): array {
        /** @var Builder $query */
        $query = $modelClass::query()->where('tenant_id', $tenantId);
        $scope($query);

        $timestamps = $query->pluck($dateColumn);

        $data = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $end = now()->subDays($i)->endOfDay();
            $data[] = $timestamps->filter(fn ($timestamp): bool => $timestamp <= $end)->count();
        }

        return $data;
    }
}
