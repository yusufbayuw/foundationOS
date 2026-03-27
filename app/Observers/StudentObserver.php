<?php

namespace App\Observers;

use App\Integrations\Moodle\MoodleOutboxService;
use App\Integrations\Moodle\MoodleSyncContext;
use Modules\School\Models\ClassStudent;
use Modules\School\Models\Student;

class StudentObserver
{
    public function __construct(protected MoodleOutboxService $outbox) {}

    public function updated(Student $student): void
    {
        $relevant = [
            'user_id',
            'status',
            'deleted_at',
        ];

        if (! $student->wasChanged($relevant)) {
            return;
        }

        $this->enqueueForStudent($student);
    }

    public function deleted(Student $student): void
    {
        $this->enqueueForStudent($student, MoodleOutboxService::ACTION_UNENROLL);
    }

    public function restored(Student $student): void
    {
        $this->enqueueForStudent($student);
    }

    public function forceDeleted(Student $student): void
    {
        $this->enqueueForStudent($student, MoodleOutboxService::ACTION_UNENROLL);
    }

    protected function enqueueForStudent(Student $student, ?string $forcedAction = null): void
    {
        if (MoodleSyncContext::disabled()) {
            return;
        }

        $classStudents = ClassStudent::withTrashed()
            ->where('student_id', $student->id)
            ->get();

        foreach ($classStudents as $classStudent) {
            $action = $forcedAction;

            if ($action === null) {
                $action = $this->shouldUnenroll($student, $classStudent)
                    ? MoodleOutboxService::ACTION_UNENROLL
                    : MoodleOutboxService::ACTION_ENROLL;
            }

            $version = optional($student->updated_at)->timestamp ?? now()->timestamp;
            $dedupe = "enrollment:{$classStudent->id}:{$action}:{$version}";

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
                    'student_status' => $student->status,
                    'student_deleted_at' => $student->deleted_at?->toDateTimeString(),
                ],
                $dedupe,
            );
        }
    }

    protected function shouldUnenroll(Student $student, ClassStudent $classStudent): bool
    {
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
