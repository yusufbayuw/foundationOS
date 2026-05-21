<?php

namespace App\Console\Commands;

use App\Integrations\Moodle\MoodleDetailedGradePullService;
use Illuminate\Console\Command;
use Modules\Core\Models\AcademicPeriod;

class MoodlePullDetailedGradesCommand extends Command
{
    protected $signature = 'fos:moodle:pull-detailed-grades
        {--tenant= : Tenant id filter}
        {--semester= : Academic period id (semester) untuk di-pull; default = semua aktif}';

    protected $description = 'Pull detailed Moodle grade items per StudyPlanItem (UTS/UAS/tugas) → StudyResult breakdown + recalc IPK';

    public function handle(MoodleDetailedGradePullService $service): int
    {
        if (! config('moodle.enabled', false)) {
            $this->warn('Moodle sync disabled.');

            return self::SUCCESS;
        }

        if (! config('moodle.learning_pull_enabled', false)) {
            $this->warn('Learning pull disabled. Set MOODLE_LEARNING_PULL_ENABLED=true.');

            return self::SUCCESS;
        }

        $tenantId = is_numeric($this->option('tenant')) ? (int) $this->option('tenant') : null;
        $semesterId = is_numeric($this->option('semester')) ? (int) $this->option('semester') : null;

        $periodQuery = AcademicPeriod::withoutTenantScope();
        if ($semesterId !== null) {
            $periodQuery->whereKey($semesterId);
        } else {
            $periodQuery->where('is_active', true);
        }
        if ($tenantId !== null) {
            $periodQuery->where('tenant_id', $tenantId);
        }

        $periods = $periodQuery->get();
        if ($periods->isEmpty()) {
            $this->warn('No matching AcademicPeriod found.');

            return self::SUCCESS;
        }

        $totalPulled = 0;
        $totalRecalc = 0;

        foreach ($periods as $period) {
            $result = $service->pullForPeriod($period, $tenantId);
            $totalPulled += $result['pulled'];
            $totalRecalc += $result['students_recalculated'];

            $this->line("Period {$period->id} ({$period->code}): pulled={$result['pulled']}, students_recalculated={$result['students_recalculated']}.");
        }

        $this->info("Detailed grades pull done. Total pulled: {$totalPulled}, GPAs recalculated: {$totalRecalc}.");

        return self::SUCCESS;
    }
}
