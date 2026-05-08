<?php

namespace Modules\Employee\Services;

use Illuminate\Support\Facades\DB;
use Modules\Core\Models\User;
use Modules\Employee\Enums\KpiScoreStatus;
use Modules\Employee\Models\KpiScore;
use Modules\Employee\Models\KpiTemplate;
use RuntimeException;

class KpiScoringService
{
    /**
     * Recalculate total_score and grade from the scores JSON array.
     * Each entry in scores: ['indicator_id' => X, 'weight' => Y, 'score' => Z]
     * Weighted total = sum(score * weight/100)
     */
    public function recalculate(KpiScore $kpiScore): KpiScore
    {
        $scores = $kpiScore->scores ?? [];
        $totalScore = $this->computeWeightedScore($scores);
        $grade = $this->resolveGrade($totalScore);

        $kpiScore->total_score = $totalScore;
        $kpiScore->grade = $grade;
        $kpiScore->save();

        return $kpiScore;
    }

    /**
     * Initialize a new KPI score sheet from a template for a given employee/period.
     * Populates scores array with indicator stubs (score = null).
     */
    public function initFromTemplate(
        int $employeeId,
        int $templateId,
        int $month,
        int $year,
        int $tenantId,
    ): KpiScore {
        $template = KpiTemplate::findOrFail($templateId);
        $indicators = $template->indicators ?? [];

        $scores = collect($indicators)->map(fn ($ind) => [
            'indicator_id' => $ind['id'] ?? null,
            'indicator_code' => $ind['code'] ?? null,
            'indicator_name' => $ind['name'] ?? null,
            'weight' => $ind['weight_percentage'] ?? 0,
            'target_value' => $ind['target_value'] ?? null,
            'actual_value' => null,
            'score' => null,
            'notes' => null,
        ])->values()->toArray();

        return KpiScore::create([
            'tenant_id' => $tenantId,
            'employee_id' => $employeeId,
            'kpi_template_id' => $templateId,
            'period_month' => $month,
            'period_year' => $year,
            'scores' => $scores,
            'total_score' => 0,
            'grade' => null,
            'status' => KpiScoreStatus::Draft,
        ]);
    }

    /**
     * Submit a draft KPI score for evaluation.
     */
    public function submit(KpiScore $kpiScore): KpiScore
    {
        return DB::transaction(function () use ($kpiScore): KpiScore {
            $kpiScore = $kpiScore->fresh();

            if ($kpiScore->status !== KpiScoreStatus::Draft) {
                throw new RuntimeException('Only draft KPI scores can be submitted.');
            }

            $this->recalculate($kpiScore);

            $kpiScore->forceFill([
                'status' => KpiScoreStatus::Submitted,
                'submitted_at' => now(),
            ])->save();

            return $kpiScore->fresh();
        });
    }

    /**
     * Mark KPI score as evaluated by the evaluator.
     */
    public function evaluate(KpiScore $kpiScore, User $evaluator): KpiScore
    {
        return DB::transaction(function () use ($kpiScore, $evaluator): KpiScore {
            $kpiScore = $kpiScore->fresh();

            if ($kpiScore->status !== KpiScoreStatus::Submitted) {
                throw new RuntimeException('Only submitted KPI scores can be evaluated.');
            }

            $this->recalculate($kpiScore);

            $kpiScore->forceFill([
                'status' => KpiScoreStatus::Evaluated,
                'evaluator_id' => $evaluator->getKey(),
                'evaluated_at' => now(),
            ])->save();

            return $kpiScore->fresh();
        });
    }

    /**
     * Approve an evaluated KPI score.
     */
    public function approve(KpiScore $kpiScore): KpiScore
    {
        return DB::transaction(function () use ($kpiScore): KpiScore {
            $kpiScore = $kpiScore->fresh();

            if ($kpiScore->status !== KpiScoreStatus::Evaluated) {
                throw new RuntimeException('Only evaluated KPI scores can be approved.');
            }

            $kpiScore->forceFill([
                'status' => KpiScoreStatus::Approved,
                'approved_at' => now(),
            ])->save();

            return $kpiScore->fresh();
        });
    }

    /**
     * Compute weighted score: sum of (score * weight) / 100.
     * Each entry must have 'score' (0–100) and 'weight' (percentage).
     */
    public function computeWeightedScore(array $scores): float
    {
        $total = 0.0;

        foreach ($scores as $entry) {
            $score = (float) ($entry['score'] ?? 0);
            $weight = (float) ($entry['weight'] ?? 0);
            $total += ($score * $weight) / 100;
        }

        return round($total, 2);
    }

    /**
     * Standard grade scale (can be overridden per tenant in future).
     */
    public function resolveGrade(float $totalScore): string
    {
        return match (true) {
            $totalScore >= 90 => 'A',
            $totalScore >= 80 => 'B',
            $totalScore >= 70 => 'C',
            $totalScore >= 60 => 'D',
            default => 'E',
        };
    }
}
