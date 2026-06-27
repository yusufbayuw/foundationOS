<?php

namespace App\Console\Commands;

use App\Models\MoodleLearningMetric;
use App\Models\MoodleSyncOutbox;
use Illuminate\Console\Command;

class MoodleOpsReportCommand extends Command
{
    protected $signature = 'fos:moodle:ops-report {--json : Output as JSON}';

    protected $description = 'Show Moodle integration operational metrics (outbox, retry, and pull snapshots)';

    public function handle(): int
    {
        $pending = MoodleSyncOutbox::query()->where('status', MoodleSyncOutbox::STATUS_PENDING)->count();
        $processing = MoodleSyncOutbox::query()->where('status', MoodleSyncOutbox::STATUS_PROCESSING)->count();
        $synced = MoodleSyncOutbox::query()->where('status', MoodleSyncOutbox::STATUS_SYNCED)->count();
        $failed = MoodleSyncOutbox::query()->where('status', MoodleSyncOutbox::STATUS_FAILED)->count();
        $skipped = MoodleSyncOutbox::query()->where('status', MoodleSyncOutbox::STATUS_SKIPPED)->count();

        $oldestPending = MoodleSyncOutbox::query()
            ->where('status', MoodleSyncOutbox::STATUS_PENDING)
            ->orderBy('created_at')
            ->value('created_at');

        $failedByEntity = MoodleSyncOutbox::query()
            ->selectRaw('entity_type, COUNT(*) AS total')
            ->where('status', MoodleSyncOutbox::STATUS_FAILED)
            ->groupBy('entity_type')
            ->get()
            ->mapWithKeys(fn (MoodleSyncOutbox $row): array => [
                (string) $row->entity_type => (int) ($row->getAttribute('total') ?? 0),
            ])
            ->all();

        $pullLast24h = MoodleLearningMetric::query()
            ->where('pulled_at', '>=', now()->subDay())
            ->count();

        $pullByMetric = MoodleLearningMetric::query()
            ->selectRaw('metric_type, COUNT(*) AS total')
            ->where('pulled_at', '>=', now()->subDay())
            ->groupBy('metric_type')
            ->get()
            ->mapWithKeys(fn (MoodleLearningMetric $row): array => [
                (string) $row->metric_type => (int) ($row->getAttribute('total') ?? 0),
            ])
            ->all();

        $report = [
            'outbox' => [
                'pending' => $pending,
                'processing' => $processing,
                'synced' => $synced,
                'failed' => $failed,
                'skipped' => $skipped,
                'oldest_pending_at' => $oldestPending ? (string) $oldestPending : null,
                'failed_by_entity' => $failedByEntity,
            ],
            'learning_pull' => [
                'snapshots_last_24h' => $pullLast24h,
                'by_metric_type_last_24h' => $pullByMetric,
            ],
        ];

        if ((bool) $this->option('json')) {
            $encoded = json_encode($report, JSON_PRETTY_PRINT);
            $this->line($encoded !== false ? $encoded : '{}');

            return self::SUCCESS;
        }

        $this->table(
            ['Metric', 'Value'],
            [
                ['outbox.pending', (string) $pending],
                ['outbox.processing', (string) $processing],
                ['outbox.synced', (string) $synced],
                ['outbox.failed', (string) $failed],
                ['outbox.skipped', (string) $skipped],
                ['outbox.oldest_pending_at', $oldestPending ? (string) $oldestPending : '-'],
                ['learning_pull.snapshots_last_24h', (string) $pullLast24h],
            ],
        );

        if ($failedByEntity !== []) {
            $this->line('Failed by entity:');
            foreach ($failedByEntity as $entity => $count) {
                $this->line("- {$entity}: {$count}");
            }
        }

        if ($pullByMetric !== []) {
            $this->line('Learning snapshots by metric (24h):');
            foreach ($pullByMetric as $metric => $count) {
                $this->line("- {$metric}: {$count}");
            }
        }

        return self::SUCCESS;
    }
}
