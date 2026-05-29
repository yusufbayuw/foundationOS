<?php

namespace Modules\Exam\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Modules\Exam\Enums\ExamStatus;
use Modules\Exam\Models\ExamDefinition;
use Modules\Exam\Services\ExamResultSyncService;

class SyncExamResultsCommand extends Command
{
    protected $signature = 'exam:sync-results
                            {examUuid? : Exam definition UUID to sync}
                            {--tenant= : Limit bulk sync to a tenant id}';

    protected $description = 'Pull exam results from Cloudflare runtime into foundationOS';

    public function handle(ExamResultSyncService $syncService): int
    {
        $examUuid = $this->argument('examUuid');

        if ($examUuid !== null && ! Str::isUuid($examUuid)) {
            $this->error('examUuid must be a valid UUID.');

            return self::FAILURE;
        }

        if ($examUuid !== null) {
            return $this->syncSingle($syncService, $examUuid);
        }

        return $this->syncAll($syncService);
    }

    protected function syncSingle(ExamResultSyncService $syncService, string $examUuid): int
    {
        $exam = ExamDefinition::withoutTenantScope()->find($examUuid);

        if ($exam === null) {
            $this->error("Exam definition {$examUuid} not found.");

            return self::FAILURE;
        }

        try {
            $summary = $syncService->sync($exam);

            $this->info("Synced results for \"{$exam->name}\" ({$exam->id}).");
            $this->table(
                ['Metric', 'Count'],
                collect($summary)
                    ->except('errors')
                    ->map(fn ($value, $key) => [$key, (string) $value])
                    ->values()
                    ->all(),
            );

            if ($summary['errors'] !== []) {
                $this->warn('Warnings:');
                foreach ($summary['errors'] as $error) {
                    $this->line('- '.$error);
                }
            }

            return self::SUCCESS;
        } catch (\Throwable $exception) {
            $this->error($exception->getMessage());

            return self::FAILURE;
        }
    }

    protected function syncAll(ExamResultSyncService $syncService): int
    {
        $query = ExamDefinition::withoutTenantScope()
            ->whereNotNull('runtime_exam_id')
            ->whereIn('status', [ExamStatus::Published, ExamStatus::Closed, ExamStatus::Scheduled]);

        if ($this->option('tenant')) {
            $query->where('tenant_id', (int) $this->option('tenant'));
        }

        $exams = $query->get();

        if ($exams->isEmpty()) {
            $this->warn('No published runtime exams found to sync.');

            return self::SUCCESS;
        }

        $failed = 0;

        foreach ($syncService->syncMany($exams) as $report) {
            if ($report['error'] !== null) {
                $failed++;
                $this->error($report['exam_definition_id'].': '.$report['error']);

                continue;
            }

            $summary = $report['summary'] ?? [];
            $this->info(sprintf(
                '%s — attempts: %d, answers: %d, results: %d',
                $report['exam_definition_id'],
                $summary['attempts_synced'] ?? 0,
                $summary['answers_synced'] ?? 0,
                $summary['results_synced'] ?? 0,
            ));
        }

        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }
}
