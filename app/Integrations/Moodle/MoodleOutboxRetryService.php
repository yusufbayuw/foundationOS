<?php

namespace App\Integrations\Moodle;

use App\Jobs\ProcessMoodleSyncOutboxJob;
use App\Models\MoodleSyncOutbox;

class MoodleOutboxRetryService
{
    /**
     * @param  list<int>  $ids
     */
    public function retryManyByIds(array $ids): int
    {
        $ids = array_values(array_unique(array_map('intval', $ids)));

        if ($ids === []) {
            return 0;
        }

        $retryableStatuses = [
            MoodleSyncOutbox::STATUS_FAILED,
            MoodleSyncOutbox::STATUS_SKIPPED,
        ];

        $queue = (string) config('moodle.queue', 'moodle-sync');
        $retried = 0;

        MoodleSyncOutbox::query()
            ->whereKey($ids)
            ->whereIn('status', $retryableStatuses)
            ->orderBy('id')
            ->chunkById($this->chunkSize(), function ($records) use ($queue, &$retried): void {
                foreach ($records as $record) {
                    $record->forceFill([
                        'status' => MoodleSyncOutbox::STATUS_PENDING,
                        'attempts' => 0,
                        'next_retry_at' => null,
                        'last_error' => null,
                        'synced_at' => null,
                    ])->save();

                    ProcessMoodleSyncOutboxJob::dispatch((int) $record->id)->onQueue($queue);

                    $retried++;
                }
            });

        return $retried;
    }

    /**
     * @param  iterable<MoodleSyncOutbox>  $records
     */
    public function retryMany(iterable $records): int
    {
        return $this->retryManyByIds(
            collect($records)
                ->pluck('id')
                ->map(fn (mixed $id): int => (int) $id)
                ->all(),
        );
    }

    public function retry(MoodleSyncOutbox $record): void
    {
        $this->retryManyByIds([(int) $record->id]);
    }

    private function chunkSize(): int
    {
        return max(1, (int) config('moodle.batch_limit', 100));
    }
}
