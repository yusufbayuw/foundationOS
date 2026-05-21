<?php

namespace App\Integrations\Moodle;

use App\Jobs\ProcessMoodleSyncOutboxJob;
use App\Models\MoodleSyncOutbox;
use Illuminate\Database\QueryException;

class MoodleOutboxService
{
    public const ENTITY_USER = 'user';

    public const ENTITY_COURSE = 'course';

    public const ENTITY_ENROLLMENT = 'enrollment';

    public const ENTITY_LECTURER_ASSIGNMENT = 'lecturer_assignment';

    public const ENTITY_COURSE_OFFERING = 'course_offering';

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

        try {
            $outbox = MoodleSyncOutbox::query()->create([
                'entity_type' => $entityType,
                'entity_id' => $entityId,
                'tenant_id' => $tenantId,
                'action' => $action,
                'payload' => $payload,
                'dedupe_key' => $dedupeKey,
                'status' => MoodleSyncOutbox::STATUS_PENDING,
                'attempts' => 0,
                'next_retry_at' => null,
            ]);

            ProcessMoodleSyncOutboxJob::dispatch((int) $outbox->id)
                ->onQueue((string) config('moodle.queue', 'moodle-sync'))
                ->afterCommit();

            return $outbox;
        } catch (QueryException $exception) {
            if ($this->isDuplicateKeyException($exception)) {
                return null;
            }

            throw $exception;
        }
    }

    protected function isDuplicateKeyException(QueryException $exception): bool
    {
        $state = $exception->errorInfo[0] ?? null;
        $code = $exception->errorInfo[1] ?? null;

        return $state === '23000' || in_array($code, [1062, 1555, 2067], true);
    }
}
