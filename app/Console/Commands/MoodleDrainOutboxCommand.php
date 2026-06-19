<?php

namespace App\Console\Commands;

use App\Jobs\ProcessMoodleSyncOutboxJob;
use App\Models\MoodleSyncOutbox;
use Illuminate\Console\Command;

class MoodleDrainOutboxCommand extends Command
{
    protected $signature = 'fos:moodle:drain-outbox
        {--limit= : Max number of outbox items to dispatch}
        {--queue= : Queue override}
        {--force-failed : Include failed items too}';

    protected $description = 'Dispatch pending Moodle outbox items to queue';

    public function handle(): int
    {
        $limit = (int) ($this->option('limit') ?: config('moodle.batch_limit', 100));
        $limit = max(1, $limit);
        $queue = (string) ($this->option('queue') ?: config('moodle.queue', 'moodle-sync'));
        $forceFailed = (bool) $this->option('force-failed');

        if (! config('moodle.enabled', false)) {
            $this->warn('Moodle sync tidak aktif (MOODLE_SYNC_ENABLED=false).');

            return self::SUCCESS;
        }

        $query = MoodleSyncOutbox::query()->where(function ($builder) use ($forceFailed): void {
            $builder->ready();

            if ($forceFailed) {
                $builder->orWhere('status', MoodleSyncOutbox::STATUS_FAILED);
            }
        });

        $items = $query
            ->orderBy('id')
            ->limit($limit)
            ->get(['id']);

        foreach ($items as $item) {
            ProcessMoodleSyncOutboxJob::dispatch((int) $item->id)->onQueue($queue);
        }

        $this->info("Dispatched {$items->count()} outbox item(s) ke queue '{$queue}'.");

        return self::SUCCESS;
    }
}
