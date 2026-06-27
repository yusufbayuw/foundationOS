<?php

namespace App\Console\Commands;

use App\Integrations\Moodle\MoodleOutboxService;
use App\Support\TypedValue;
use Illuminate\Console\Command;
use Modules\Campus\Models\Course;
use Modules\Core\Models\User;
use Modules\School\Models\ClassStudent;
use Modules\School\Models\Student;

class MoodleBackfillCommand extends Command
{
    protected $signature = 'fos:moodle:backfill
        {entity : user|course|enrollment|all}
        {--tenant= : Filter by tenant_id}
        {--dry-run : Show counts without enqueue}';

    protected $description = 'Backfill FOS data into Moodle sync outbox';

    public function __construct(protected MoodleOutboxService $outbox)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $entity = strtolower(trim((string) $this->argument('entity')));
        $tenantOption = $this->option('tenant');
        $tenantId = is_numeric($tenantOption) ? (int) $tenantOption : null;
        $dryRun = (bool) $this->option('dry-run');

        if (! in_array($entity, ['user', 'course', 'enrollment', 'all'], true)) {
            $this->error('Entity harus salah satu dari: user, course, enrollment, all.');

            return self::FAILURE;
        }

        if (! config('moodle.enabled', false)) {
            $this->warn('Moodle sync saat ini nonaktif (MOODLE_SYNC_ENABLED=false).');
        }

        $summary = [
            'user' => 0,
            'course' => 0,
            'enrollment' => 0,
        ];

        if (in_array($entity, ['user', 'all'], true)) {
            $summary['user'] = $this->backfillUsers($dryRun);
        }

        if (in_array($entity, ['course', 'all'], true)) {
            $summary['course'] = $this->backfillCourses($tenantId, $dryRun);
        }

        if (in_array($entity, ['enrollment', 'all'], true)) {
            $summary['enrollment'] = $this->backfillEnrollments($tenantId, $dryRun);
        }

        $this->table(
            ['Entity', 'Enqueued'],
            [
                ['user', (string) $summary['user']],
                ['course', (string) $summary['course']],
                ['enrollment', (string) $summary['enrollment']],
            ],
        );

        if ($dryRun) {
            $this->info('Dry-run selesai. Tidak ada data yang ditulis ke outbox.');
        }

        return self::SUCCESS;
    }

    protected function backfillUsers(bool $dryRun): int
    {
        $count = 0;

        User::withTrashed()->chunkById(500, function ($users) use (&$count, $dryRun): void {
            foreach ($users as $user) {
                $action = $this->shouldDeactivateUser($user) ? MoodleOutboxService::ACTION_DEACTIVATE : MoodleOutboxService::ACTION_UPSERT;
                $version = TypedValue::int(
                    data_get($user, 'updated_at.timestamp'),
                    TypedValue::int(now()->timestamp),
                );
                $dedupe = "user:{$user->id}:{$action}:{$version}";

                if (! $dryRun) {
                    $this->outbox->enqueue(
                        MoodleOutboxService::ENTITY_USER,
                        (int) $user->id,
                        null,
                        $action,
                        ['email' => $user->email],
                        $dedupe,
                    );
                }

                $count++;
            }
        });

        return $count;
    }

    protected function backfillCourses(?int $tenantId, bool $dryRun): int
    {
        $count = 0;

        $query = Course::withTrashed();
        if ($tenantId !== null) {
            $query->where($query->getModel()->qualifyColumn('tenant_id'), $tenantId);
        }

        $query->chunkById(500, function ($courses) use (&$count, $dryRun): void {
            foreach ($courses as $course) {
                $action = $this->shouldDeactivateCourse($course) ? MoodleOutboxService::ACTION_DEACTIVATE : MoodleOutboxService::ACTION_UPSERT;
                $version = TypedValue::int(
                    data_get($course, 'updated_at.timestamp'),
                    TypedValue::int(now()->timestamp),
                );
                $dedupe = "course:{$course->id}:{$action}:{$version}";

                if (! $dryRun) {
                    $this->outbox->enqueue(
                        MoodleOutboxService::ENTITY_COURSE,
                        (int) $course->id,
                        (int) $course->tenant_id,
                        $action,
                        [
                            'tenant_id' => (int) $course->tenant_id,
                            'code' => $course->code,
                        ],
                        $dedupe,
                    );
                }

                $count++;
            }
        });

        return $count;
    }

    protected function backfillEnrollments(?int $tenantId, bool $dryRun): int
    {
        $count = 0;

        $query = ClassStudent::withTrashed()->with('student');
        if ($tenantId !== null) {
            $query->where($query->getModel()->qualifyColumn('tenant_id'), $tenantId);
        }

        $query->chunkById(500, function ($classStudents) use (&$count, $dryRun): void {
            foreach ($classStudents as $classStudent) {
                $student = $classStudent->student ?: Student::withTrashed()->find($classStudent->student_id);
                $action = $this->shouldUnenroll($student, $classStudent)
                    ? MoodleOutboxService::ACTION_UNENROLL
                    : MoodleOutboxService::ACTION_ENROLL;
                $version = TypedValue::int(
                    data_get($classStudent, 'updated_at.timestamp'),
                    TypedValue::int(now()->timestamp),
                );
                $dedupe = "enrollment:{$classStudent->id}:{$action}:{$version}";

                if (! $dryRun) {
                    $this->outbox->enqueue(
                        MoodleOutboxService::ENTITY_ENROLLMENT,
                        (int) $classStudent->id,
                        (int) $classStudent->tenant_id,
                        $action,
                        [
                            'class_id' => (int) $classStudent->class_id,
                            'student_id' => (int) $classStudent->student_id,
                            'tenant_id' => (int) $classStudent->tenant_id,
                            'status' => $classStudent->status,
                            'student_status' => $student?->status,
                        ],
                        $dedupe,
                    );
                }

                $count++;
            }
        });

        return $count;
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

    protected function shouldUnenroll(?Student $student, ClassStudent $classStudent): bool
    {
        if (! $student || ! $student->user_id) {
            return true;
        }

        if ($student->trashed()) {
            return true;
        }

        $studentStatus = strtolower(trim((string) $student->status));
        if (in_array($studentStatus, ['inactive', 'nonaktif', 'keluar', 'left', 'withdrawn'], true)) {
            return true;
        }

        if ($classStudent->trashed() || $classStudent->exit_date !== null) {
            return true;
        }

        $classStatus = strtolower(trim((string) $classStudent->status));

        return in_array($classStatus, ['inactive', 'nonaktif', 'keluar', 'left', 'withdrawn'], true);
    }
}
