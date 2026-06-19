<?php

namespace App\Console\Commands;

use App\Integrations\Moodle\MoodleClient;
use App\Models\MoodleEntityMapping;
use App\Models\MoodleLearningMetric;
use Illuminate\Console\Command;
use Modules\Core\Models\UserTenantRole;

class MoodlePullProgressCommand extends Command
{
    protected $signature = 'fos:moodle:pull-progress
        {--tenant= : Filter tenant_id}
        {--fos-user= : Pull only one FOS user id}
        {--fos-course= : Pull only one FOS course id}
        {--limit=200 : Max user-course pairs}';

    protected $description = 'Pull Moodle completion progress into FOS learning metrics snapshots';

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
        $limit = max(1, (int) $this->option('limit'));

        $courseQuery = MoodleEntityMapping::query()->where('entity_type', 'course');
        if ($tenantId !== null) {
            $courseQuery->where('tenant_id', $tenantId);
        }
        if ($fosCourseId !== null) {
            $courseQuery->where('fos_entity_id', $fosCourseId);
        }
        $courseMappings = $courseQuery->get();

        $userQuery = MoodleEntityMapping::query()->where('entity_type', 'user');
        if ($fosUserId !== null) {
            $userQuery->where('fos_entity_id', $fosUserId);
        }
        $userMappings = $userQuery->get()->keyBy('fos_entity_id');

        $tenantUsers = null;
        if ($tenantId !== null) {
            $tenantUsers = UserTenantRole::query()
                ->where('tenant_id', $tenantId)
                ->pluck('user_id')
                ->unique()
                ->values()
                ->all();
        }

        $processed = 0;
        $stored = 0;

        foreach ($courseMappings as $courseMapping) {
            foreach ($userMappings as $userMapping) {
                if ($processed >= $limit) {
                    break 2;
                }

                if ($tenantUsers !== null && ! in_array((int) $userMapping->fos_entity_id, $tenantUsers, true)) {
                    continue;
                }

                $moodleCourseId = (int) $courseMapping->moodle_id;
                $moodleUserId = (int) $userMapping->moodle_id;

                if ($moodleCourseId <= 0 || $moodleUserId <= 0) {
                    continue;
                }

                $courseStatus = $client->call('core_completion_get_course_completion_status', [
                    'courseid' => $moodleCourseId,
                    'userid' => $moodleUserId,
                ]);

                $activityStatus = $client->call('core_completion_get_activities_completion_status', [
                    'courseid' => $moodleCourseId,
                    'userid' => $moodleUserId,
                ]);

                MoodleLearningMetric::query()->create([
                    'tenant_id' => $courseMapping->tenant_id,
                    'fos_user_id' => $userMapping->fos_entity_id,
                    'fos_course_id' => $courseMapping->fos_entity_id,
                    'moodle_user_id' => $moodleUserId,
                    'moodle_course_id' => $moodleCourseId,
                    'metric_type' => 'completion_course',
                    'payload' => $courseStatus,
                    'pulled_at' => now(),
                ]);

                MoodleLearningMetric::query()->create([
                    'tenant_id' => $courseMapping->tenant_id,
                    'fos_user_id' => $userMapping->fos_entity_id,
                    'fos_course_id' => $courseMapping->fos_entity_id,
                    'moodle_user_id' => $moodleUserId,
                    'moodle_course_id' => $moodleCourseId,
                    'metric_type' => 'completion_activities',
                    'payload' => $activityStatus,
                    'pulled_at' => now(),
                ]);

                $processed++;
                $stored += 2;
            }
        }

        $this->info("Progress pull done. Pairs processed: {$processed}, snapshots stored: {$stored}.");

        return self::SUCCESS;
    }
}
