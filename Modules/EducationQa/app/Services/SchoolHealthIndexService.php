<?php

namespace Modules\EducationQa\Services;

use Illuminate\Support\Facades\DB;

class SchoolHealthIndexService
{
    /**
     * @param  array{academic_score: float|int, collection_rate: float|int, runway_score: float|int, hr_retention_score: float|int, asset_utilization_score: float|int, satisfaction_score: float|int}  $signals
     * @return array{score: float, band: string}
     */
    public function compute(int $tenantId, int $organizationId, array $signals): array
    {
        $weightedScore = (
            ((float) $signals['academic_score'] * 0.25)
            + ((float) $signals['collection_rate'] * 0.20)
            + ((float) $signals['runway_score'] * 0.15)
            + ((float) $signals['hr_retention_score'] * 0.15)
            + ((float) $signals['asset_utilization_score'] * 0.15)
            + ((float) $signals['satisfaction_score'] * 0.10)
        );

        $score = round($weightedScore, 2);
        $band = match (true) {
            $score >= 85 => 'excellent',
            $score >= 70 => 'healthy',
            $score >= 55 => 'watch',
            default => 'critical',
        };

        DB::table('school_health_indices')->updateOrInsert(
            [
                'tenant_id' => $tenantId,
                'organization_id' => $organizationId,
                'period' => now()->format('Y-m'),
            ],
            [
                'score' => $score,
                'band' => $band,
                'signals' => json_encode($signals, JSON_THROW_ON_ERROR),
                'updated_at' => now(),
                'created_at' => now(),
            ],
        );

        return ['score' => $score, 'band' => $band];
    }
}
