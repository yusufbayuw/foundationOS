<?php

namespace App\Console\Commands;

use App\Models\MoodleSyncOutbox;
use App\Support\TypedValue;
use Illuminate\Console\Command;

class MoodleSweepStaleProcessingCommand extends Command
{
    protected $signature = 'fos:moodle:sweep-stale-processing
        {--minutes= : Reset rows stuck in processing longer than N minutes}';

    protected $description = 'Reset Moodle outbox rows stuck in processing back to pending';

    public function handle(): int
    {
        $minutes = TypedValue::int(($this->option('minutes')) ?: config('tenancy.moodle_processing_stale_minutes', 30));

        $reset = MoodleSyncOutbox::query()
            ->staleProcessing($minutes)
            ->update([
                'status' => MoodleSyncOutbox::STATUS_PENDING,
                'last_error' => 'Reset from stale processing by sweeper',
            ]);

        $this->info("Reset {$reset} stale processing row(s) older than {$minutes} minute(s).");

        return self::SUCCESS;
    }
}
