<?php

namespace App\Integrations\Moodle;

use App\Jobs\ProcessMoodleSyncOutboxJob;
use App\Models\MoodleSyncOutbox;

class MoodleOutboxRetryService
{
    /**
     * @param  iterable<MoodleSyncOutbox>  $records
     */
    public function retryMany(iterable $records): int
    {
        $retried = 0;

        foreach ($records as $record) {
            if (! in_array($record->status, [
                MoodleSyncOutbox::STATUS_FAILED,
                MoodleSyncOutbox::STATUS_SKIPPED,
            ], true)) {
                continue;
            }

            $this->retry($record);
            $retried++;
        }

        return $retried;
    }

    public function retry(MoodleSyncOutbox $record): void
    {
        $record->forceFill([
            'status' => MoodleSyncOutbox::STATUS_PENDING,
            'attempts' => 0,
            'next_retry_at' => null,
            'last_error' => null,
            'synced_at' => null,
        ])->save();

        ProcessMoodleSyncOutboxJob::dispatch((int) $record->id);
    }
}
