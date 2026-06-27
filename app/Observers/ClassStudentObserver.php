<?php

namespace App\Observers;

use App\Integrations\Moodle\MoodleOutboxService;
use App\Integrations\Moodle\MoodleSyncContext;
use Illuminate\Support\Carbon;
use Modules\School\Models\ClassStudent;

class ClassStudentObserver
{
    public function __construct(protected MoodleOutboxService $outbox) {}

    public function created(ClassStudent $classStudent): void
    {
        $this->enqueueByState($classStudent);
    }

    public function updated(ClassStudent $classStudent): void
    {
        $relevant = [
            'tenant_id',
            'class_id',
            'student_id',
            'status',
            'exit_date',
            'deleted_at',
        ];

        if (! $classStudent->wasChanged($relevant)) {
            return;
        }

        $this->enqueueByState($classStudent);
    }

    public function deleted(ClassStudent $classStudent): void
    {
        $this->enqueue($classStudent, MoodleOutboxService::ACTION_UNENROLL);
    }

    public function restored(ClassStudent $classStudent): void
    {
        $this->enqueueByState($classStudent);
    }

    public function forceDeleted(ClassStudent $classStudent): void
    {
        $this->enqueue($classStudent, MoodleOutboxService::ACTION_UNENROLL);
    }

    protected function enqueueByState(ClassStudent $classStudent): void
    {
        $action = $this->shouldUnenroll($classStudent)
            ? MoodleOutboxService::ACTION_UNENROLL
            : MoodleOutboxService::ACTION_ENROLL;

        $this->enqueue($classStudent, $action);
    }

    protected function enqueue(ClassStudent $classStudent, string $action): void
    {
        if (MoodleSyncContext::disabled()) {
            return;
        }

        $version = optional($classStudent->updated_at)->timestamp ?? now()->timestamp;
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
                'exit_date' => $classStudent->exit_date?->toDateString(),
                'deleted_at' => (($d = $classStudent->getAttribute('deleted_at')) instanceof Carbon ? $d->toDateTimeString() : null),
            ],
            $dedupe,
        );
    }

    protected function shouldUnenroll(ClassStudent $classStudent): bool
    {
        if ($classStudent->trashed() || $classStudent->exit_date !== null) {
            return true;
        }

        $status = strtolower(trim((string) $classStudent->status));

        return in_array($status, ['inactive', 'nonaktif', 'keluar', 'left', 'withdrawn'], true);
    }
}
