<?php

namespace App\Jobs;

use App\Integrations\Moodle\MoodleSyncRetry;
use App\Integrations\Moodle\MoodleSyncService;
use App\Models\MoodleSyncOutbox;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Throwable;

class ProcessMoodleSyncOutboxJob implements ShouldQueue
{
    use Queueable;

    public int $tries;

    public function __construct(public int $outboxId)
    {
        $this->onQueue((string) config('moodle.queue', 'moodle-sync'));
        $this->tries = max(1, (int) config('moodle.max_attempts', 7));
    }

    public function handle(MoodleSyncService $syncService, MoodleSyncRetry $retry): void
    {
        if (! config('moodle.enabled', false)) {
            return;
        }

        /** @var MoodleSyncOutbox|null $outbox */
        $outbox = MoodleSyncOutbox::query()->find($this->outboxId);
        if (! $outbox) {
            return;
        }

        if (! in_array($outbox->status, [MoodleSyncOutbox::STATUS_PENDING, MoodleSyncOutbox::STATUS_FAILED], true)) {
            return;
        }

        $outbox->forceFill([
            'status' => MoodleSyncOutbox::STATUS_PROCESSING,
            'last_error' => null,
        ])->save();

        try {
            $syncService->syncOutboxItem($outbox);

            $outbox->forceFill([
                'status' => MoodleSyncOutbox::STATUS_SYNCED,
                'synced_at' => now(),
                'next_retry_at' => null,
                'last_error' => null,
            ])->save();
        } catch (Throwable $exception) {
            $attempts = (int) $outbox->attempts + 1;
            $maxAttempts = max(1, (int) config('moodle.max_attempts', 7));
            $isTerminal = $attempts >= $maxAttempts;

            $outbox->forceFill([
                'attempts' => $attempts,
                'status' => $isTerminal ? MoodleSyncOutbox::STATUS_FAILED : MoodleSyncOutbox::STATUS_PENDING,
                'next_retry_at' => $isTerminal ? null : $retry->nextRetryAt($attempts),
                'last_error' => mb_substr($exception->getMessage(), 0, 65000),
            ])->save();

            if ($isTerminal) {
                report($exception);
            }
        }
    }
}

