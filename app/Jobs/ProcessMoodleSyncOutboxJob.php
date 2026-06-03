<?php

namespace App\Jobs;

use App\Concerns\InteractsWithTenant;
use App\Integrations\Moodle\Exceptions\MoodleReadonlySkipException;
use App\Integrations\Moodle\MoodleSyncRetry;
use App\Integrations\Moodle\MoodleSyncService;
use App\Models\MoodleSyncOutbox;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Throwable;

class ProcessMoodleSyncOutboxJob implements ShouldQueue
{
    use InteractsWithTenant, Queueable;

    public int $tries;

    public function __construct(public int $outboxId)
    {
        $this->onQueue((string) config('moodle.queue', 'moodle-sync'));
        $this->tries = max(1, (int) config('moodle.max_attempts', 7));
        $this->captureCurrentTenant();
    }

    public function handle(MoodleSyncService $syncService, MoodleSyncRetry $retry): void
    {
        if (! config('moodle.enabled', false)) {
            return;
        }

        $outbox = MoodleSyncOutbox::tryClaim($this->outboxId);

        if (! $outbox) {
            return;
        }

        try {
            $syncService->syncOutboxItem($outbox);

            $outbox->forceFill([
                'status' => MoodleSyncOutbox::STATUS_SYNCED,
                'synced_at' => now(),
                'next_retry_at' => null,
                'last_error' => null,
            ])->save();
        } catch (MoodleReadonlySkipException) {
            $outbox->forceFill([
                'status' => MoodleSyncOutbox::STATUS_SKIPPED,
                'last_error' => 'MOODLE_SYNC_READONLY=true',
                'synced_at' => null,
                'next_retry_at' => null,
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
