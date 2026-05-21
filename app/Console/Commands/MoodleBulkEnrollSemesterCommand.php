<?php

namespace App\Console\Commands;

use App\Integrations\Moodle\MoodleStudyPlanSyncService;
use Illuminate\Console\Command;

class MoodleBulkEnrollSemesterCommand extends Command
{
    protected $signature = 'fos:moodle:bulk-enroll-semester
        {semester : academic_period_id of the semester to roll out}';

    protected $description = 'Bulk-enqueue Moodle enrollment for every approved KRS in the given semester.';

    public function handle(MoodleStudyPlanSyncService $sync): int
    {
        if (! config('moodle.enabled', false)) {
            $this->warn('Moodle sync disabled.');

            return self::SUCCESS;
        }

        $period = (int) $this->argument('semester');
        $count = $sync->bulkEnrollSemester($period);
        $this->info("Enqueued {$count} enrollment(s) for academic_period_id={$period}.");

        return self::SUCCESS;
    }
}
