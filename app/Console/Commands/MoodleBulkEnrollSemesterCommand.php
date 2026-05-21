<?php

namespace App\Console\Commands;

use App\Integrations\Moodle\MoodleOutboxService;
use App\Observers\StudyPlanItemObserver;
use Illuminate\Console\Command;
use Modules\Campus\Models\StudyPlanItem;
use Modules\Core\Models\AcademicPeriod;

class MoodleBulkEnrollSemesterCommand extends Command
{
    protected $signature = 'fos:moodle:bulk-enroll-semester
        {period : Academic period id (semester) untuk di-bulk enroll}
        {--tenant= : Filter tenant_id (optional)}
        {--dry-run : Hitung kandidat tanpa enqueue outbox}';

    protected $description = 'Enqueue Moodle enroll outbox untuk semua StudyPlanItem approved di academic period tertentu (semester rollover)';

    public function handle(MoodleOutboxService $outbox): int
    {
        $periodId = (int) $this->argument('period');
        $period = AcademicPeriod::withoutTenantScope()->find($periodId);

        if (! $period) {
            $this->error("AcademicPeriod {$periodId} not found.");

            return self::FAILURE;
        }

        if (! config('moodle.enabled', false) && ! $this->option('dry-run')) {
            $this->warn('Moodle sync disabled (MOODLE_SYNC_ENABLED=false). Use --dry-run to preview.');

            return self::SUCCESS;
        }

        $tenantOption = $this->option('tenant');
        $tenantId = is_numeric($tenantOption) ? (int) $tenantOption : null;

        $query = StudyPlanItem::withoutTenantScope()
            ->whereHas('studyPlan', fn ($q) => $q->where('academic_period_id', $periodId))
            ->whereIn('status', StudyPlanItemObserver::ENROLLED_STATUSES)
            ->whereNotNull('course_offering_id');

        if ($tenantId !== null) {
            $query->where('tenant_id', $tenantId);
        }

        $total = (clone $query)->count();
        $this->info("Found {$total} eligible StudyPlanItem(s) for period {$periodId}".($tenantId ? " (tenant {$tenantId})" : '').'.');

        if ($this->option('dry-run')) {
            return self::SUCCESS;
        }

        $enqueued = 0;
        $skipped = 0;

        $query->with(['studyPlan.collageStudent'])
            ->chunkById(200, function ($items) use ($outbox, &$enqueued, &$skipped): void {
                foreach ($items as $item) {
                    $userId = (int) ($item->studyPlan?->collageStudent?->user_id ?? 0);

                    if ($userId <= 0 || ! $item->course_offering_id) {
                        $skipped++;

                        continue;
                    }

                    $dedupe = 'study_plan_enrollment:'.$item->id.':enroll:bulk:'.$item->updated_at?->timestamp;
                    $created = $outbox->enqueue(
                        MoodleOutboxService::ENTITY_STUDY_PLAN_ENROLLMENT,
                        (int) $item->id,
                        (int) $item->tenant_id,
                        MoodleOutboxService::ACTION_ENROLL,
                        [
                            'tenant_id' => (int) $item->tenant_id,
                            'study_plan_id' => (int) $item->study_plan_id,
                            'course_offering_id' => (int) $item->course_offering_id,
                            'user_id' => $userId,
                            'status' => $item->status,
                            'source' => 'bulk-enroll-semester',
                        ],
                        $dedupe,
                    );

                    $created ? $enqueued++ : $skipped++;
                }
            });

        $this->info("Bulk enroll: enqueued {$enqueued}, skipped/dedupe {$skipped}.");

        return self::SUCCESS;
    }
}
