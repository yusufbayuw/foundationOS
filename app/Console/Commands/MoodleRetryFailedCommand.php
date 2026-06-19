<?php

namespace App\Console\Commands;

use App\Models\MoodleSyncOutbox;
use Illuminate\Console\Command;

class MoodleRetryFailedCommand extends Command
{
    protected $signature = 'fos:moodle:retry-failed
        {--limit=100 : Max failed items to reset}
        {--entity= : Filter entity type}
        {--tenant= : Filter tenant_id}';

    protected $description = 'Reset failed Moodle outbox items back to pending';

    public function handle(): int
    {
        $limit = max(1, (int) $this->option('limit'));
        $entity = trim((string) ($this->option('entity') ?? ''));
        $tenant = $this->option('tenant');
        $tenantId = is_numeric($tenant) ? (int) $tenant : null;

        $query = MoodleSyncOutbox::query()
            ->where('status', MoodleSyncOutbox::STATUS_FAILED)
            ->orderBy('id');

        if ($entity !== '') {
            $query->where('entity_type', $entity);
        }

        if ($tenantId !== null) {
            $query->where('tenant_id', $tenantId);
        }

        $items = $query->limit($limit)->get();

        foreach ($items as $item) {
            $item->forceFill([
                'status' => MoodleSyncOutbox::STATUS_PENDING,
                'next_retry_at' => null,
                'last_error' => null,
            ])->save();
        }

        $this->info("Reset {$items->count()} failed outbox item(s) to pending.");

        return self::SUCCESS;
    }
}
