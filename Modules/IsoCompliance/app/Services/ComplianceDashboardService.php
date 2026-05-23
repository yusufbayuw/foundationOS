<?php

namespace Modules\IsoCompliance\Services;

class ComplianceDashboardService
{
    private const LEVELS = [
        'not_implemented' => 0,
        'partial' => 2,
        'implemented' => 3,
        'audited' => 4,
    ];

    /**
     * @param  array<string, string>  $controlStatuses
     * @return array{maturity_score: float, control_coverage_percent: float, audit_readiness_score: float}
     */
    public function score(int $tenantId, ?int $organizationId = null, array $controlStatuses = []): array
    {
        if ($controlStatuses === []) {
            return [
                'maturity_score' => 0.0,
                'control_coverage_percent' => 0.0,
                'audit_readiness_score' => 0.0,
            ];
        }

        $totalControls = count($controlStatuses);
        $implementedLevels = array_map(
            fn (string $status): int => self::LEVELS[$status] ?? 0,
            array_values($controlStatuses),
        );

        $covered = count(array_filter($implementedLevels, fn (int $level): bool => $level > 0));
        $audited = count(array_filter($controlStatuses, fn (string $status): bool => $status === 'audited'));
        $maxScore = $totalControls * max(self::LEVELS);

        return [
            'maturity_score' => round((array_sum($implementedLevels) / $maxScore) * 100, 2),
            'control_coverage_percent' => round(($covered / $totalControls) * 100, 2),
            'audit_readiness_score' => round(($audited / $totalControls) * 100, 2),
        ];
    }
}
