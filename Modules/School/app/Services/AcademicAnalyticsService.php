<?php

namespace Modules\School\Services;

use Illuminate\Support\Facades\Cache;
use Modules\School\Models\StudentGrade;

class AcademicAnalyticsService
{
    public function summaryForTenant(int $tenantId): array
    {
        return Cache::remember(
            "academic_analytics:tenant:{$tenantId}",
            now()->addHour(),
            fn (): array => $this->buildSummary($tenantId),
        );
    }

    /**
     * @return array{
     *     average_score: float|null,
     *     grade_distribution: array<string, int>,
     *     outlier_count: int,
     *     remedial_flags: int,
     * }
     */
    protected function buildSummary(int $tenantId): array
    {
        $grades = StudentGrade::query()
            ->where('tenant_id', $tenantId)
            ->get(['score', 'school_class_id']);

        if ($grades->isEmpty()) {
            return [
                'average_score' => null,
                'grade_distribution' => [],
                'outlier_count' => 0,
                'remedial_flags' => 0,
            ];
        }

        $scores = $grades->pluck('score')->filter()->map(fn ($s) => (float) $s);
        $average = $scores->avg();
        $stdDev = $this->standardDeviation($scores->all());
        $outliers = $scores->filter(fn (float $s): bool => abs($s - $average) > max(10, $stdDev * 2))->count();

        $distribution = [
            'A' => $scores->filter(fn (float $s): bool => $s >= 85)->count(),
            'B' => $scores->filter(fn (float $s): bool => $s >= 70 && $s < 85)->count(),
            'C' => $scores->filter(fn (float $s): bool => $s >= 55 && $s < 70)->count(),
            'D' => $scores->filter(fn (float $s): bool => $s < 55)->count(),
        ];

        $remedialFlags = $scores->filter(fn (float $s): bool => $s < 75)->count();

        return [
            'average_score' => round((float) $average, 2),
            'grade_distribution' => $distribution,
            'outlier_count' => $outliers,
            'remedial_flags' => $remedialFlags,
        ];
    }

    /**
     * @param  list<float>  $values
     */
    protected function standardDeviation(array $values): float
    {
        $count = count($values);
        if ($count === 0) {
            return 0.0;
        }

        $mean = array_sum($values) / $count;
        $variance = array_sum(array_map(fn (float $v): float => ($v - $mean) ** 2, $values)) / $count;

        return sqrt($variance);
    }
}
