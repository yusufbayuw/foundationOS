<?php

namespace App\Integrations\Moodle;

use App\Models\MoodleEntityMapping;
use App\Support\TypedValue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Modules\Campus\Models\CourseOffering;
use Modules\Campus\Models\StudyPlan;
use Modules\Campus\Models\StudyPlanItem;
use Modules\Campus\Models\StudyResult;
use Modules\Campus\Services\CampusGpaCalculator;
use Modules\Campus\Services\GradebookConfigResolver;
use Modules\Core\Models\AcademicPeriod;

class MoodleDetailedGradePullService
{
    public function __construct(
        protected MoodleClient $client,
        protected MoodleSyncService $syncService,
        protected GradebookConfigResolver $config,
        protected CampusGpaCalculator $gpaCalculator,
    ) {}

    /**
     * Pull detailed grades for one AcademicPeriod and write/update StudyResult per StudyPlanItem.
     * Returns [pulled_count, students_recalculated].
     *
     * @return array{pulled: int, students_recalculated: int}
     */
    public function pullForPeriod(AcademicPeriod $period, ?int $tenantFilter = null): array
    {
        $tenantId = (int) $period->tenant_id;
        if ($tenantFilter !== null && $tenantFilter !== $tenantId) {
            return ['pulled' => 0, 'students_recalculated' => 0];
        }

        $items = StudyPlanItem::withoutTenantScope()
            ->whereHas('studyPlan', static function (Builder $query) use ($period): void {
                /** @var Builder<StudyPlan> $query */
                $query->where('academic_period_id', $period->id);
            })
            ->whereIn('status', ['approved', 'enrolled', 'active', 'completed'])
            ->with(['courseOffering', 'studyPlan.collageStudent.user'])
            ->get();

        $pulled = 0;
        $studentIds = [];

        foreach ($items as $item) {
            $offering = $item->courseOffering;
            $user = $item->studyPlan?->collageStudent?->user;
            if (! $offering || ! $user) {
                continue;
            }

            $moodleCourseId = $this->safeResolveCourseId($offering);
            $moodleUserId = $this->safeResolveUserId((int) $user->id);

            if ($moodleCourseId <= 0 || $moodleUserId <= 0) {
                continue;
            }

            $gradeItems = $this->fetchGradeItems($moodleUserId, $moodleCourseId);
            if ($gradeItems === []) {
                continue;
            }

            $this->processGradeItems($item, $this->normalizeGradeItems($gradeItems));
            $pulled++;

            $studentId = TypedValue::int($item->studyPlan->collage_student_id);
            if ($studentId > 0) {
                $studentIds[$studentId] = true;
            }
        }

        $recalculated = 0;
        foreach (array_keys($studentIds) as $studentId) {
            $this->gpaCalculator->recalculateForStudent((int) $studentId);
            $recalculated++;
        }

        return ['pulled' => $pulled, 'students_recalculated' => $recalculated];
    }

    /**
     * Map Moodle grade items → StudyResult breakdown for one StudyPlanItem.
     * Pure: writes via Eloquent but doesn't talk to Moodle. Returns the persisted StudyResult.
     *
     * @param  array<int, array{itemname?: string, graderaw?: float|string|null, grademax?: float|string|null}>  $gradeItems
     */
    public function processGradeItems(StudyPlanItem $item, array $gradeItems): StudyResult
    {
        $tenantId = TypedValue::int($item->tenant_id);
        /** @var array<string, array{weight: float, matchers: array<int, string>}> $components */
        $components = $this->config->componentsFor($tenantId);

        $breakdown = $this->buildBreakdown($gradeItems, $components);
        $weightedScore = $this->computeWeightedScore($breakdown, $components);
        $grade = $this->config->gradeFor($tenantId, $weightedScore);
        $passThreshold = $this->config->passGradePointFor($tenantId);

        $existing = StudyResult::withoutTenantScope()
            ->where('study_plan_item_id', $item->id)
            ->first();

        $attributes = [
            'tenant_id' => $tenantId,
            'study_plan_item_id' => $item->id,
            'grade_letter' => $grade['letter'],
            'grade_point' => $grade['point'],
            'weight_score' => round($weightedScore, 2),
            'components_breakdown' => $breakdown,
            'passed' => $grade['point'] >= $passThreshold,
            'moodle_pulled_at' => now(),
            'source' => 'moodle',
        ];

        if ($existing) {
            $existing->forceFill($attributes)->save();
            $fresh = $existing->fresh();

            return $fresh ?? $existing;
        }

        return StudyResult::create($attributes);
    }

    /**
     * @param  array<mixed>  $gradeItems
     * @return array<int, array{itemname?: string, graderaw?: float|string|null, grademax?: float|string|null}>
     */
    protected function normalizeGradeItems(array $gradeItems): array
    {
        $normalized = [];

        foreach ($gradeItems as $gradeItem) {
            if (! is_array($gradeItem)) {
                continue;
            }

            /** @var array{itemname?: string, graderaw?: float|string|null, grademax?: float|string|null} $gradeItem */
            $normalized[] = $gradeItem;
        }

        return $normalized;
    }

    /**
     * @param  array<int, array{itemname?: string, graderaw?: float|string|null, grademax?: float|string|null}>  $gradeItems
     * @param  array<string, array{weight: float, matchers: array<int, string>}>  $components
     * @return array<string, array{score: float, raw: float|null, max: float|null, source_item: string}>
     */
    protected function buildBreakdown(array $gradeItems, array $components): array
    {
        $breakdown = [];

        foreach ($gradeItems as $gi) {
            $name = strtolower(TypedValue::string($gi['itemname'] ?? null));
            if ($name === '') {
                continue;
            }
            $raw = $gi['graderaw'] ?? null;
            $max = $gi['grademax'] ?? null;
            if ($raw === null || $max === null || ! is_numeric($raw) || ! is_numeric($max) || (float) $max <= 0) {
                continue;
            }

            $score = (float) $raw / (float) $max * 100.0;
            $component = $this->matchComponent($name, $components);
            if ($component === null) {
                continue;
            }

            if (! isset($breakdown[$component])) {
                $breakdown[$component] = [
                    'score' => round($score, 2),
                    'raw' => (float) $raw,
                    'max' => (float) $max,
                    'source_item' => (string) ($gi['itemname'] ?? ''),
                ];
            } else {
                // Average across multiple grade items in same component.
                $prev = $breakdown[$component];
                $breakdown[$component] = [
                    'score' => round(($prev['score'] + $score) / 2, 2),
                    'raw' => $prev['raw'] + (float) $raw,
                    'max' => $prev['max'] + (float) $max,
                    'source_item' => $prev['source_item'].' + '.(string) ($gi['itemname'] ?? ''),
                ];
            }
        }

        return $breakdown;
    }

    /**
     * @param  array<string, array{weight: float, matchers: array<int, string>}>  $components
     */
    protected function matchComponent(string $name, array $components): ?string
    {
        foreach ($components as $componentName => $def) {
            foreach ($def['matchers'] as $matcher) {
                if (str_contains($name, strtolower($matcher))) {
                    return (string) $componentName;
                }
            }
        }

        return null;
    }

    /**
     * @param  array<string, array{score: float, raw: float|null, max: float|null, source_item: string}>  $breakdown
     * @param  array<string, array{weight: float, matchers: array<int, string>}>  $components
     */
    protected function computeWeightedScore(array $breakdown, array $components): float
    {
        $total = 0.0;
        $usedWeight = 0.0;

        foreach ($components as $name => $def) {
            if (! isset($breakdown[$name])) {
                continue;
            }
            $weight = $def['weight'];
            $total += ((float) $breakdown[$name]['score']) * $weight;
            $usedWeight += $weight;
        }

        if ($usedWeight <= 0) {
            return 0.0;
        }

        // Normalize when components partially present.
        return $total / $usedWeight;
    }

    /**
     * @return array<int, array{itemname?: string, graderaw?: float|string|null, grademax?: float|string|null}>
     */
    protected function fetchGradeItems(int $moodleUserId, int $moodleCourseId): array
    {
        try {
            $response = $this->client->call('gradereport_user_get_grade_items', [
                'courseid' => $moodleCourseId,
                'userid' => $moodleUserId,
            ]);
        } catch (\Throwable) {
            return [];
        }

        $items = Arr::get($response, 'usergrades.0.gradeitems', []);

        return $this->normalizeGradeItems(is_array($items) ? $items : []);
    }

    protected function safeResolveCourseId(CourseOffering $offering): int
    {
        try {
            return $this->syncService->resolveMoodleCourseIdForOffering($offering);
        } catch (\Throwable) {
            return 0;
        }
    }

    protected function safeResolveUserId(int $fosUserId): int
    {
        $mapping = MoodleEntityMapping::query()
            ->where('entity_type', 'user')
            ->where('fos_entity_id', $fosUserId)
            ->first();

        return $mapping ? (int) $mapping->moodle_id : 0;
    }
}
