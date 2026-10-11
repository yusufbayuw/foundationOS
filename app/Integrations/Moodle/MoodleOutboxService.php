<?php

namespace App\Integrations\Moodle;

use App\Jobs\ProcessMoodleSyncOutboxJob;
use App\Models\MoodleSyncOutbox;

class MoodleOutboxService
{
    public const ENTITY_USER = 'user';

    public const ENTITY_COURSE = 'course';

    public const ENTITY_ENROLLMENT = 'enrollment';

    public const ENTITY_LECTURER_ASSIGNMENT = 'lecturer_assignment';

    public const ENTITY_COURSE_OFFERING = 'course_offering';

    public const ENTITY_STUDY_PLAN_ENROLLMENT = 'study_plan_enrollment';

    public const ACTION_UPSERT = 'upsert';

    public const ACTION_DEACTIVATE = 'deactivate';

    public const ACTION_ENROLL = 'enroll';

    public const ACTION_UNENROLL = 'unenroll';

    public const ACTION_ASSIGN = 'assign';

    public const ACTION_UNASSIGN = 'unassign';

    public function enqueue(
        string $entityType,
        int $entityId,
        ?int $tenantId,
        string $action,
        array $payload,
        string $dedupeKey,
    ): ?MoodleSyncOutbox {
        if (! config('moodle.enabled', false)) {
            return null;
        }

        $outbox = MoodleSyncOutbox::query()->createOrFirst(
            ['dedupe_key' => $dedupeKey],
            [
                'entity_type' => $entityType,
                'entity_id' => $entityId,
                'tenant_id' => $tenantId,
                'action' => $action,
                'payload' => $payload,
                'status' => MoodleSyncOutbox::STATUS_PENDING,
                'attempts' => 0,
                'next_retry_at' => null,
            ],
        );

        if (! $outbox->wasRecentlyCreated) {
            return null;
        }

        ProcessMoodleSyncOutboxJob::dispatch((int) $outbox->id)
            ->onQueue((string) config('moodle.queue', 'moodle-sync'))
            ->afterCommit();

        return $outbox;
    }
}
