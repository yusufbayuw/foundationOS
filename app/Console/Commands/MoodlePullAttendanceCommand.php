<?php

namespace App\Console\Commands;

use App\Integrations\Moodle\Exceptions\MoodleIntegrationException;
use App\Integrations\Moodle\MoodleClient;
use App\Models\MoodleEntityMapping;
use App\Models\MoodleLearningMetric;
use Illuminate\Console\Command;
use Modules\Core\Models\UserTenantRole;

class MoodlePullAttendanceCommand extends Command
{
    protected $signature = 'fos:moodle:pull-attendance
        {--tenant= : Filter tenant_id}
        {--fos-user= : Pull only one FOS user id}
        {--limit=100 : Max users to process}';

    protected $description = 'Pull attendance snapshots from Moodle (plugin-aware) into FOS learning metrics';

    public function handle(MoodleClient $client): int
    {
        if (! config('moodle.enabled', false)) {
            $this->warn('Moodle sync disabled.');
            return self::SUCCESS;
        }

        if (! config('moodle.attendance_pull_enabled', false)) {
            $this->warn('Attendance pull disabled. Set MOODLE_ATTENDANCE_PULL_ENABLED=true.');
            return self::SUCCESS;
        }

        $attendanceFunction = $this->resolveAttendanceFunction($client);
        if ($attendanceFunction === null) {
            $this->warn('Attendance plugin webservice function not found. Skipping pull.');
            return self::SUCCESS;
        }

        $tenantOption = $this->option('tenant');
        $tenantId = is_numeric($tenantOption) ? (int) $tenantOption : null;
        $fosUserId = is_numeric($this->option('fos-user')) ? (int) $this->option('fos-user') : null;
        $limit = max(1, (int) $this->option('limit'));

        $query = MoodleEntityMapping::query()->where('entity_type', 'user');
        if ($fosUserId !== null) {
            $query->where('fos_entity_id', $fosUserId);
        }

        $userMappings = $query->limit($limit)->get();

        $tenantUsers = null;
        if ($tenantId !== null) {
            $tenantUsers = UserTenantRole::query()
                ->where('tenant_id', $tenantId)
                ->pluck('user_id')
                ->unique()
                ->values()
                ->all();
        }

        $stored = 0;
        $skipped = 0;

        foreach ($userMappings as $userMapping) {
            $fosMappedUserId = (int) $userMapping->fos_entity_id;
            if ($tenantUsers !== null && ! in_array($fosMappedUserId, $tenantUsers, true)) {
                continue;
            }

            $moodleUserId = (int) $userMapping->moodle_id;
            if ($moodleUserId <= 0) {
                $skipped++;
                continue;
            }

            try {
                $payload = $client->call($attendanceFunction, [
                    'userid' => $moodleUserId,
                ]);
            } catch (MoodleIntegrationException $exception) {
                $this->warn("Skip user {$fosMappedUserId}: {$exception->getMessage()}");
                $skipped++;
                continue;
            }

            MoodleLearningMetric::query()->create([
                'tenant_id' => $tenantId,
                'fos_user_id' => $fosMappedUserId,
                'fos_course_id' => null,
                'moodle_user_id' => $moodleUserId,
                'moodle_course_id' => null,
                'metric_type' => 'attendance_today_sessions',
                'payload' => [
                    'function' => $attendanceFunction,
                    'data' => $payload,
                ],
                'pulled_at' => now(),
            ]);

            $stored++;
        }

        $this->info("Attendance pull done with {$attendanceFunction}. Stored: {$stored}, skipped: {$skipped}.");

        return self::SUCCESS;
    }

    protected function resolveAttendanceFunction(MoodleClient $client): ?string
    {
        try {
            $siteInfo = $client->call('core_webservice_get_site_info');
        } catch (MoodleIntegrationException) {
            return null;
        }

        $available = [];
        foreach (($siteInfo['functions'] ?? []) as $function) {
            if (! is_array($function) || ! isset($function['name'])) {
                continue;
            }

            $available[] = (string) $function['name'];
        }

        foreach (['mod_attendance_get_courses_with_today_sessions', 'mod_attendance_get_user_absences'] as $candidate) {
            if (in_array($candidate, $available, true)) {
                return $candidate;
            }
        }

        return null;
    }
}
