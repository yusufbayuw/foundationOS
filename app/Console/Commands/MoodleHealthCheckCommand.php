<?php

namespace App\Console\Commands;

use App\Integrations\Moodle\MoodleClient;
use App\Integrations\Moodle\Exceptions\MoodleIntegrationException;
use Illuminate\Console\Command;

class MoodleHealthCheckCommand extends Command
{
    protected $signature = 'fos:moodle:health-check';

    protected $description = 'Check Moodle connectivity, credentials, and required web service functions';

    public function handle(MoodleClient $client): int
    {
        $rows = [];
        $requiredFunctions = [
            'core_user_create_users',
            'core_user_update_users',
            'core_course_create_categories',
            'core_course_update_categories',
            'core_course_create_courses',
            'core_course_update_courses',
            'enrol_manual_enrol_users',
            'enrol_manual_unenrol_users',
        ];

        $optionalFunctionGroups = [
            'Cohort sync (MOODLE_COHORT_SYNC_ENABLED)' => [
                'enabled' => (bool) config('moodle.cohort_sync_enabled', true),
                'functions' => [
                    'core_cohort_create_cohorts',
                    'core_cohort_add_cohort_members',
                ],
            ],
            'Cohort lookup function' => [
                'enabled' => (bool) config('moodle.cohort_sync_enabled', true),
                'functions' => [
                    'core_cohort_get_cohorts',
                    'core_cohort_search_cohorts',
                ],
                'mode' => 'any',
            ],
            'Calendar sync (MOODLE_CALENDAR_SYNC_ENABLED)' => [
                'enabled' => (bool) config('moodle.calendar_sync_enabled', false),
                'functions' => [
                    'core_calendar_create_calendar_events',
                ],
            ],
            'Learning pull (MOODLE_LEARNING_PULL_ENABLED)' => [
                'enabled' => (bool) config('moodle.learning_pull_enabled', false),
                'functions' => [
                    'gradereport_overview_get_course_grades',
                    'core_completion_get_course_completion_status',
                    'core_completion_get_activities_completion_status',
                ],
            ],
            'Attendance pull (MOODLE_ATTENDANCE_PULL_ENABLED)' => [
                'enabled' => (bool) config('moodle.attendance_pull_enabled', false),
                'functions' => [
                    'mod_attendance_get_courses_with_today_sessions',
                    'mod_attendance_get_user_absences',
                ],
                'mode' => 'any',
            ],
        ];

        $baseUrl = (string) config('moodle.base_url');
        $token = (string) config('moodle.token');
        $enabled = (bool) config('moodle.enabled', false);

        $rows[] = ['Config enabled', $enabled ? 'OK' : 'FAIL', $enabled ? '-' : 'Set MOODLE_SYNC_ENABLED=true'];
        $rows[] = ['Base URL', $baseUrl !== '' ? 'OK' : 'FAIL', $baseUrl !== '' ? $baseUrl : 'Set MOODLE_BASE_URL'];
        $rows[] = ['Token', $token !== '' ? 'OK' : 'FAIL', $token !== '' ? 'Configured' : 'Set MOODLE_WS_TOKEN'];

        $siteInfo = null;
        $siteInfoError = null;

        if ($enabled && $baseUrl !== '' && $token !== '') {
            try {
                $siteInfo = $client->call('core_webservice_get_site_info');
                $rows[] = ['Webservice connection', 'OK', (string) ($siteInfo['sitename'] ?? 'Connected')];
            } catch (MoodleIntegrationException $exception) {
                $siteInfoError = $exception->getMessage();
                $rows[] = ['Webservice connection', 'FAIL', $siteInfoError];
            }
        }

        $availableFunctions = [];
        if (is_array($siteInfo) && isset($siteInfo['functions']) && is_array($siteInfo['functions'])) {
            foreach ($siteInfo['functions'] as $function) {
                if (is_array($function) && isset($function['name'])) {
                    $availableFunctions[] = (string) $function['name'];
                }
            }
        }

        foreach ($requiredFunctions as $functionName) {
            $ok = in_array($functionName, $availableFunctions, true);
            $rows[] = [
                "Function {$functionName}",
                $ok ? 'OK' : 'FAIL',
                $ok ? 'Available' : 'Include function in Moodle external service/token',
            ];
        }

        foreach ($optionalFunctionGroups as $label => $group) {
            $enabled = (bool) ($group['enabled'] ?? false);
            $functions = $group['functions'] ?? [];
            $mode = (string) ($group['mode'] ?? 'all');

            if (! $enabled) {
                $rows[] = [$label, 'SKIP', 'Feature disabled'];
                continue;
            }

            $matches = 0;
            foreach ($functions as $functionName) {
                if (in_array($functionName, $availableFunctions, true)) {
                    $matches++;
                }
            }

            $ok = $mode === 'any'
                ? $matches > 0
                : $matches === count($functions);

            $rows[] = [
                $label,
                $ok ? 'OK' : 'FAIL',
                $ok
                    ? ($mode === 'any' ? 'At least one function available' : 'All required functions available')
                    : 'Add missing webservice function(s) to Moodle token service',
            ];
        }

        $this->table(['Check', 'Status', 'Notes'], $rows);

        $failed = collect($rows)->contains(fn (array $row): bool => $row[1] === 'FAIL');
        if ($failed && $siteInfoError) {
            $this->warn("Connection error detail: {$siteInfoError}");
        }

        return $failed ? self::FAILURE : self::SUCCESS;
    }
}
