<?php

namespace App\Console\Commands;

use App\Integrations\Moodle\Exceptions\MoodleIntegrationException;
use App\Integrations\Moodle\MoodleClient;
use App\Integrations\Moodle\MoodleMapper;
use App\Integrations\Moodle\MoodleOutboxService;
use App\Integrations\Moodle\MoodleSyncService;
use App\Support\TypedValue;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Modules\Campus\Models\Course;
use Modules\Core\Models\User;

class MoodleReconcileCommand extends Command
{
    protected $signature = 'fos:moodle:reconcile
        {entity=all : user|course|all}
        {--tenant= : Filter tenant_id (course only)}
        {--limit=500 : Max records per entity}
        {--dry-run : Audit only, no enqueue}
        {--fix : Enqueue remediation to Moodle via outbox}';

    protected $description = 'Reconcile Moodle drift against FOS as source-of-truth (safe inbound handling)';

    public function __construct(
        protected MoodleClient $client,
        protected MoodleSyncService $syncService,
        protected MoodleOutboxService $outbox,
        protected MoodleMapper $mapper,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        if (! config('moodle.enabled', false)) {
            $this->warn('Moodle sync disabled.');

            return self::SUCCESS;
        }

        $entity = strtolower(trim((string) $this->argument('entity')));
        if (! in_array($entity, ['user', 'course', 'all'], true)) {
            $this->error('Entity harus salah satu dari: user, course, all.');

            return self::FAILURE;
        }

        $dryRun = (bool) $this->option('dry-run');
        $fix = (bool) $this->option('fix');
        if ($dryRun && $fix) {
            $this->error('Gunakan salah satu: --dry-run atau --fix, bukan keduanya.');

            return self::FAILURE;
        }

        $tenantOption = $this->option('tenant');
        $tenantId = is_numeric($tenantOption) ? (int) $tenantOption : null;
        $limit = max(1, (int) $this->option('limit'));

        $summary = [
            'user_checked' => 0,
            'course_checked' => 0,
            'drift_missing_in_moodle' => 0,
            'drift_state_mismatch' => 0,
            'remediation_enqueued' => 0,
            'errors' => 0,
        ];

        if (in_array($entity, ['user', 'all'], true)) {
            $this->reconcileUsers($limit, $fix, $summary);
        }

        if (in_array($entity, ['course', 'all'], true)) {
            $this->reconcileCourses($tenantId, $limit, $fix, $summary);
        }

        $this->table(
            ['Metric', 'Value'],
            [
                ['user_checked', (string) $summary['user_checked']],
                ['course_checked', (string) $summary['course_checked']],
                ['drift_missing_in_moodle', (string) $summary['drift_missing_in_moodle']],
                ['drift_state_mismatch', (string) $summary['drift_state_mismatch']],
                ['remediation_enqueued', (string) $summary['remediation_enqueued']],
                ['errors', (string) $summary['errors']],
            ],
        );

        if ($fix) {
            $this->info('Reconcile fix mode selesai. Remediasi sudah dienqueue ke outbox.');
        } else {
            $this->info('Reconcile audit selesai. Gunakan --fix untuk enqueue remediasi.');
        }

        return $summary['errors'] > 0 ? self::FAILURE : self::SUCCESS;
    }

    /**
     * @param  array{user_checked: int, course_checked: int, drift_missing_in_moodle: int, drift_state_mismatch: int, remediation_enqueued: int, errors: int}  $summary
     */
    protected function reconcileUsers(int $limit, bool $fix, array &$summary): void
    {
        $query = User::withTrashed()->orderBy('id');
        if ($limit > 0) {
            $query->limit($limit);
        }

        $users = $query->get();

        foreach ($users as $user) {
            $summary['user_checked']++;

            $shouldDeactivate = $this->shouldDeactivateUser($user);
            $idnumber = $this->mapper->userIdnumber((int) $user->id);

            try {
                $moodleUserId = $this->syncService->findMoodleUserIdByIdnumber($idnumber);
            } catch (MoodleIntegrationException $exception) {
                $summary['errors']++;
                $this->warn("User {$user->id}: {$exception->getMessage()}");

                continue;
            }

            if ($moodleUserId <= 0) {
                if (! $shouldDeactivate) {
                    $summary['drift_missing_in_moodle']++;
                    if ($fix) {
                        $this->enqueueUserRemediation((int) $user->id, MoodleOutboxService::ACTION_UPSERT);
                        $summary['remediation_enqueued']++;
                    }
                }

                continue;
            }

            try {
                $moodleUsers = $this->client->call('core_user_get_users_by_field', [
                    'field' => 'idnumber',
                    'values' => [$idnumber],
                ]);
            } catch (MoodleIntegrationException $exception) {
                $summary['errors']++;
                $this->warn("User {$user->id}: {$exception->getMessage()}");

                continue;
            }

            $firstUser = Arr::first($moodleUsers);
            if (! is_array($firstUser)) {
                continue;
            }

            $suspended = TypedValue::int($firstUser['suspended'] ?? 0);
            $mismatch = $shouldDeactivate ? $suspended !== 1 : $suspended !== 0;
            if (! $mismatch) {
                continue;
            }

            $summary['drift_state_mismatch']++;
            if ($fix) {
                $this->enqueueUserRemediation(
                    (int) $user->id,
                    $shouldDeactivate ? MoodleOutboxService::ACTION_DEACTIVATE : MoodleOutboxService::ACTION_UPSERT,
                );
                $summary['remediation_enqueued']++;
            }
        }
    }

    /**
     * @param  array{user_checked: int, course_checked: int, drift_missing_in_moodle: int, drift_state_mismatch: int, remediation_enqueued: int, errors: int}  $summary
     */
    protected function reconcileCourses(?int $tenantId, int $limit, bool $fix, array &$summary): void
    {
        $query = Course::withTrashed()->orderBy('id');
        if ($tenantId !== null) {
            $query->where($query->getModel()->qualifyColumn('tenant_id'), $tenantId);
        }
        if ($limit > 0) {
            $query->limit($limit);
        }

        $courses = $query->get();

        foreach ($courses as $course) {
            $summary['course_checked']++;

            $shouldDeactivate = $this->shouldDeactivateCourse($course);
            $idnumber = $this->mapper->courseIdnumber((int) $course->id);

            try {
                $response = $this->client->call('core_course_get_courses_by_field', [
                    'field' => 'idnumber',
                    'value' => $idnumber,
                ]);
            } catch (MoodleIntegrationException $exception) {
                $summary['errors']++;
                $this->warn("Course {$course->id}: {$exception->getMessage()}");

                continue;
            }

            $courses = is_array($response['courses'] ?? null) ? $response['courses'] : [];
            $moodleCourse = is_array($courses[0] ?? null) ? $courses[0] : null;
            if (! is_array($moodleCourse)) {
                if (! $shouldDeactivate) {
                    $summary['drift_missing_in_moodle']++;
                    if ($fix) {
                        $this->enqueueCourseRemediation(
                            (int) $course->id,
                            (int) $course->tenant_id,
                            MoodleOutboxService::ACTION_UPSERT,
                        );
                        $summary['remediation_enqueued']++;
                    }
                }

                continue;
            }

            $visible = TypedValue::int($moodleCourse['visible'] ?? 1);
            $mismatch = $shouldDeactivate ? $visible !== 0 : $visible !== 1;
            if (! $mismatch) {
                continue;
            }

            $summary['drift_state_mismatch']++;
            if ($fix) {
                $this->enqueueCourseRemediation(
                    (int) $course->id,
                    (int) $course->tenant_id,
                    $shouldDeactivate ? MoodleOutboxService::ACTION_DEACTIVATE : MoodleOutboxService::ACTION_UPSERT,
                );
                $summary['remediation_enqueued']++;
            }
        }
    }

    protected function enqueueUserRemediation(int $userId, string $action): void
    {
        $version = now()->timestamp;
        $dedupe = "reconcile:user:{$userId}:{$action}:{$version}";

        $this->outbox->enqueue(
            MoodleOutboxService::ENTITY_USER,
            $userId,
            null,
            $action,
            ['reason' => 'reconcile_drift'],
            $dedupe,
        );
    }

    protected function enqueueCourseRemediation(int $courseId, int $tenantId, string $action): void
    {
        $version = now()->timestamp;
        $dedupe = "reconcile:course:{$courseId}:{$action}:{$version}";

        $this->outbox->enqueue(
            MoodleOutboxService::ENTITY_COURSE,
            $courseId,
            $tenantId,
            $action,
            ['tenant_id' => $tenantId, 'reason' => 'reconcile_drift'],
            $dedupe,
        );
    }

    protected function shouldDeactivateUser(User $user): bool
    {
        if ($user->trashed()) {
            return true;
        }

        return strtolower((string) $user->status) !== 'active';
    }

    protected function shouldDeactivateCourse(Course $course): bool
    {
        if ($course->trashed()) {
            return true;
        }

        return ! (bool) $course->is_active;
    }
}
