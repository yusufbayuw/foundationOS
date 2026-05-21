<?php

namespace Modules\Campus\Services;

use Modules\Core\Models\TenantSetting;

/**
 * Resolve per-tenant gradebook configuration (components + grading scale) with
 * sensible Indonesian-university defaults when no tenant override exists.
 */
class GradebookConfigResolver
{
    public const SETTING_GROUP = 'campus';

    public const SETTING_KEY_COMPONENTS = 'gradebook_components';

    public const SETTING_KEY_SCALE = 'gradebook_scale';

    public const SETTING_KEY_PASS_GRADE = 'gradebook_pass_grade_point';

    /**
     * Default component definitions: keyed component name → weight (0..1) + matchers.
     *
     * @return array<string, array{weight: float, matchers: array<int, string>}>
     */
    public function defaultComponents(): array
    {
        return [
            'attendance' => ['weight' => 0.10, 'matchers' => ['absen', 'attendance', 'kehadiran']],
            'assignment' => ['weight' => 0.20, 'matchers' => ['tugas', 'assignment', 'homework', 'kuis', 'quiz']],
            'midterm' => ['weight' => 0.30, 'matchers' => ['uts', 'midterm', 'mid-term', 'mid']],
            'final' => ['weight' => 0.40, 'matchers' => ['uas', 'final', 'final exam', 'final-exam']],
        ];
    }

    /**
     * Default grading scale (Indonesian university PAP convention).
     *
     * @return array<int, array{min: float, letter: string, point: float}>
     */
    public function defaultScale(): array
    {
        return [
            ['min' => 85.0, 'letter' => 'A', 'point' => 4.0],
            ['min' => 80.0, 'letter' => 'A-', 'point' => 3.7],
            ['min' => 75.0, 'letter' => 'B+', 'point' => 3.3],
            ['min' => 70.0, 'letter' => 'B', 'point' => 3.0],
            ['min' => 65.0, 'letter' => 'B-', 'point' => 2.7],
            ['min' => 60.0, 'letter' => 'C+', 'point' => 2.3],
            ['min' => 55.0, 'letter' => 'C', 'point' => 2.0],
            ['min' => 50.0, 'letter' => 'D', 'point' => 1.0],
            ['min' => 0.0, 'letter' => 'E', 'point' => 0.0],
        ];
    }

    public function componentsFor(int $tenantId): array
    {
        $override = $this->setting($tenantId, self::SETTING_KEY_COMPONENTS);
        if (is_array($override) && $override !== []) {
            return $override;
        }

        return $this->defaultComponents();
    }

    public function scaleFor(int $tenantId): array
    {
        $override = $this->setting($tenantId, self::SETTING_KEY_SCALE);
        if (is_array($override) && $override !== []) {
            return $override;
        }

        return $this->defaultScale();
    }

    public function passGradePointFor(int $tenantId): float
    {
        $override = $this->setting($tenantId, self::SETTING_KEY_PASS_GRADE);
        if (is_numeric($override)) {
            return (float) $override;
        }

        return 1.0;
    }

    /**
     * Convert numeric score (0..100) → ['letter' => ..., 'point' => ...] per tenant scale.
     */
    public function gradeFor(int $tenantId, float $score): array
    {
        foreach ($this->scaleFor($tenantId) as $tier) {
            if ($score >= (float) $tier['min']) {
                return ['letter' => (string) $tier['letter'], 'point' => (float) $tier['point']];
            }
        }

        return ['letter' => 'E', 'point' => 0.0];
    }

    protected function setting(int $tenantId, string $key): mixed
    {
        $record = TenantSetting::withoutTenantScope()
            ->where('tenant_id', $tenantId)
            ->where('group', self::SETTING_GROUP)
            ->where('key', $key)
            ->first();

        if (! $record) {
            return null;
        }

        $value = $record->value;

        return match ($record->type) {
            'json', 'array' => is_string($value) ? json_decode($value, true) : $value,
            'float' => (float) $value,
            'int' => (int) $value,
            'bool' => (bool) $value,
            default => $value,
        };
    }
}
