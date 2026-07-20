<?php

namespace App\Jobs;

use App\Concerns\InteractsWithTenant;
use App\Integrations\Moodle\MoodleOutboxRetryService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class RetryMoodleSyncOutboxBatchJob implements ShouldQueue
{
    use InteractsWithTenant, Queueable;

    /**
     * @param  list<int>  $ids
     */
    public function __construct(public array $ids)
    {
        $this->ids = array_values(array_unique(array_map('intval', $ids)));
        $this->onQueue((string) config('moodle.queue', 'moodle-sync'));
        $this->captureCurrentTenant();
    }

    public function handle(MoodleOutboxRetryService $retryService): void
    {
        foreach (array_chunk($this->ids, $this->chunkSize()) as $ids) {
            $retryService->retryManyByIds($ids);
        }
    }

    private function chunkSize(): int
    {
        return max(1, (int) config('moodle.batch_limit', 100));
    }
}
