<?php

namespace Modules\KpiEnterprise\Services;

use Modules\KpiEnterprise\Models\KpiActual;
use Modules\KpiEnterprise\Models\KpiCascade;
use Modules\KpiEnterprise\Models\KpiMetric;
use Modules\KpiEnterprise\Models\KpiTarget;

class KpiRollupService
{
    public function linkChild(KpiMetric $parent, KpiMetric $child): KpiCascade
    {
        return KpiCascade::withoutTenantScope()->firstOrCreate(
            [
                'tenant_id' => $parent->tenant_id,
                'parent_kpi_metric_id' => $parent->id,
                'child_kpi_metric_id' => $child->id,
            ],
            [
                'organization_id' => $parent->organization_id,
                'code' => $parent->code.'-'.$child->code,
                'name' => "{$parent->name} / {$child->name}",
            ],
        );
    }

    public function rollup(KpiMetric $parent, string $period): float
    {
        $children = KpiCascade::withoutTenantScope()
            ->where('parent_kpi_metric_id', $parent->id)
            ->pluck('child_kpi_metric_id');

        $weightedScore = 0.0;
        $totalWeight = 0.0;

        foreach ($children as $childId) {
            $target = KpiTarget::withoutTenantScope()
                ->where('kpi_metric_id', $childId)
                ->where('period', $period)
                ->first();

            $actual = KpiActual::withoutTenantScope()
                ->where('kpi_metric_id', $childId)
                ->where('period', $period)
                ->first();

            if ($target === null || $actual === null || (float) $target->target_value === 0.0) {
                continue;
            }

            $weight = (float) $target->weight;
            $score = min(((float) $actual->actual_value / (float) $target->target_value) * 100, 100);
            $weightedScore += $score * $weight;
            $totalWeight += $weight;
        }

        $score = $totalWeight > 0 ? round($weightedScore / $totalWeight, 2) : 0.0;

        KpiActual::withoutTenantScope()->updateOrCreate(
            [
                'tenant_id' => $parent->tenant_id,
                'kpi_metric_id' => $parent->id,
                'period' => $period,
            ],
            [
                'organization_id' => $parent->organization_id,
                'code' => $parent->code.'-'.$period,
                'name' => "{$parent->name} {$period}",
                'actual_value' => $score,
                'score' => $score,
            ],
        );

        return $score;
    }
}
