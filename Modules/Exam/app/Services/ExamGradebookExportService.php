<?php

namespace Modules\Exam\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Core\Models\User;
use Modules\Exam\Enums\GradebookExportStatus;
use Modules\Exam\Enums\GradebookExportTargetModule;
use Modules\Exam\Models\ExamDefinition;
use Modules\Exam\Models\ExamGradebookExportLog;
use Modules\Exam\Models\ExamResult;
use Modules\Exam\Support\GradebookExportOutcome;

class ExamGradebookExportService
{
    public function __construct(
        protected SchoolGradeBridgeService $schoolBridge,
        protected CampusGradeBridgeService $campusBridge,
        protected ExamGradebookEventDispatcher $eventDispatcher,
    ) {}

    public function isSchoolGradebookReady(): bool
    {
        return $this->schoolBridge->isGradebookAvailable();
    }

    public function isCampusGradebookReady(): bool
    {
        return $this->campusBridge->isGradebookAvailable();
    }

    /**
     * @return array{
     *     target: string,
     *     success: int,
     *     skipped: int,
     *     failed: int,
     *     events_dispatched: int,
     *     integration: string,
     *     errors: list<string>
     * }
     */
    public function pushToSchoolGradebook(ExamDefinition $definition, ?User $pushedBy = null): array
    {
        return $this->push($definition, GradebookExportTargetModule::School, $pushedBy);
    }

    /**
     * @return array{
     *     target: string,
     *     success: int,
     *     skipped: int,
     *     failed: int,
     *     events_dispatched: int,
     *     integration: string,
     *     errors: list<string>
     * }
     */
    public function pushToCampusGradebook(ExamDefinition $definition, ?User $pushedBy = null): array
    {
        return $this->push($definition, GradebookExportTargetModule::Campus, $pushedBy);
    }

    /**
     * @return array{
     *     target: string,
     *     success: int,
     *     skipped: int,
     *     failed: int,
     *     events_dispatched: int,
     *     integration: string,
     *     errors: list<string>
     * }
     */
    protected function push(
        ExamDefinition $definition,
        GradebookExportTargetModule $target,
        ?User $pushedBy,
    ): array {
        $pushedBy ??= Auth::user() instanceof User ? Auth::user() : null;

        $summary = [
            'target' => $target->value,
            'success' => 0,
            'skipped' => 0,
            'failed' => 0,
            'events_dispatched' => 0,
            'integration' => 'direct',
            'errors' => [],
        ];

        $bridgeReady = match ($target) {
            GradebookExportTargetModule::School => $this->isSchoolGradebookReady(),
            GradebookExportTargetModule::Campus => $this->isCampusGradebookReady(),
        };

        if (! $bridgeReady) {
            $summary['integration'] = 'event_only';
            $summary['events_dispatched'] = $this->dispatchResultEventsForExam($definition);

            return $summary;
        }

        $results = ExamResult::withoutTenantScope()
            ->where('exam_definition_id', $definition->id)
            ->with('examParticipant')
            ->get();

        if ($results->isEmpty()) {
            $summary['errors'][] = 'No exam results to export. Sync results from runtime first.';

            return $summary;
        }

        DB::transaction(function () use ($definition, $target, $results, $pushedBy, &$summary): void {
            foreach ($results as $result) {
                $outcome = match ($target) {
                    GradebookExportTargetModule::School => $this->schoolBridge->exportResult($definition, $result),
                    GradebookExportTargetModule::Campus => $this->campusBridge->exportResult($definition, $result),
                };

                $this->writeLog($definition, $result, $target, $outcome, $pushedBy);

                match ($outcome->status) {
                    GradebookExportStatus::Success => $summary['success']++,
                    GradebookExportStatus::Skipped => $summary['skipped']++,
                    GradebookExportStatus::Failed => $summary['failed']++,
                };

                if ($outcome->errorMessage !== null && $outcome->status !== GradebookExportStatus::Success) {
                    $summary['errors'][] = $outcome->errorMessage;
                }

                if ($outcome->status !== GradebookExportStatus::Success) {
                    $this->eventDispatcher->synced($result, [
                        'gradebook_export' => $target->value,
                        'export_status' => $outcome->status->value,
                    ]);
                    $summary['events_dispatched']++;
                }
            }
        });

        return $summary;
    }

    protected function writeLog(
        ExamDefinition $definition,
        ExamResult $result,
        GradebookExportTargetModule $target,
        GradebookExportOutcome $outcome,
        ?User $pushedBy,
    ): ExamGradebookExportLog {
        return ExamGradebookExportLog::query()->create([
            'tenant_id' => $definition->tenant_id,
            'exam_definition_id' => $definition->id,
            'exam_result_id' => $result->id,
            'exam_participant_id' => $result->exam_participant_id,
            'target_module' => $target,
            'target_reference_type' => $outcome->targetReferenceType,
            'target_reference_id' => $outcome->targetReferenceId,
            'status' => $outcome->status,
            'error_message' => $outcome->errorMessage,
            'pushed_by' => $pushedBy?->id,
            'metadata_json' => $outcome->metadata,
        ]);
    }

    protected function dispatchResultEventsForExam(ExamDefinition $definition): int
    {
        $count = 0;

        ExamResult::withoutTenantScope()
            ->where('exam_definition_id', $definition->id)
            ->whereNotNull('exam_participant_id')
            ->each(function (ExamResult $result) use (&$count): void {
                $this->eventDispatcher->synced($result, ['gradebook_export' => 'deferred']);
                $count++;
            });

        return $count;
    }
}
