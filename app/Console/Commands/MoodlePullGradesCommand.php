<?php

namespace App\Console\Commands;

use App\Integrations\Moodle\MoodleClient;
use App\Models\MoodleEntityMapping;
use App\Models\MoodleLearningMetric;
use Illuminate\Console\Command;

class MoodlePullGradesCommand extends Command
{
    protected $signature = 'fos:moodle:pull-grades
        {--tenant= : Filter tenant_id}
        {--fos-user= : Pull only one FOS user id}
        {--fos-course= : Pull only one FOS course id}';

    protected $description = 'Pull Moodle course grades into FOS learning metrics snapshots';

    public function handle(MoodleClient $client): int
    {
        if (! config('moodle.enabled', false)) {
            $this->warn('Moodle sync disabled.');

            return self::SUCCESS;
        }

        if (! config('moodle.learning_pull_enabled', false)) {
            $this->warn('Learning pull disabled. Set MOODLE_LEARNING_PULL_ENABLED=true.');

            return self::SUCCESS;
        }

        $tenantOption = $this->option('tenant');
        $tenantId = is_numeric($tenantOption) ? (int) $tenantOption : null;
        $fosUserId = is_numeric($this->option('fos-user')) ? (int) $this->option('fos-user') : null;
        $fosCourseId = is_numeric($this->option('fos-course')) ? (int) $this->option('fos-course') : null;

        $usersQuery = MoodleEntityMapping::query()->where('entity_type', 'user');
        if ($fosUserId !== null) {
            $usersQuery->where('fos_entity_id', $fosUserId);
        }

        $userMappings = $usersQuery->get();
        $stored = 0;

        foreach ($userMappings as $userMapping) {
            $moodleUserId = (int) $userMapping->moodle_id;
            if ($moodleUserId <= 0) {
                continue;
            }

            $response = $client->call('gradereport_overview_get_course_grades', [
                'userid' => $moodleUserId,
            ]);

            $grades = $response['grades'] ?? [];
            if (! is_array($grades)) {
                continue;
            }

            foreach ($grades as $grade) {
                if (! is_array($grade)) {
                    continue;
                }

                $moodleCourseId = (int) ($grade['courseid'] ?? 0);
                if ($moodleCourseId <= 0) {
                    continue;
                }

                $courseMapping = MoodleEntityMapping::query()
                    ->where('entity_type', 'course')
                    ->where('moodle_id', $moodleCourseId)
                    ->first();

                if (! $courseMapping) {
                    continue;
                }

                if ($tenantId !== null && (int) $courseMapping->tenant_id !== $tenantId) {
                    continue;
                }

                if ($fosCourseId !== null && (int) $courseMapping->fos_entity_id !== $fosCourseId) {
                    continue;
                }

                MoodleLearningMetric::query()->create([
                    'tenant_id' => $courseMapping->tenant_id,
                    'fos_user_id' => $userMapping->fos_entity_id,
                    'fos_course_id' => $courseMapping->fos_entity_id,
                    'moodle_user_id' => $moodleUserId,
                    'moodle_course_id' => $moodleCourseId,
                    'metric_type' => 'grade',
                    'payload' => $grade,
                    'pulled_at' => now(),
                ]);

                $stored++;
            }
        }

        $this->info("Grades pull done. Stored snapshots: {$stored}.");

        return self::SUCCESS;
    }
}
