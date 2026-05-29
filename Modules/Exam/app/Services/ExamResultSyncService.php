<?php

namespace Modules\Exam\Services;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\Exam\Enums\ExamAuditAction;
use Modules\Exam\Enums\ExamRuntimeSyncAction;
use Modules\Exam\Enums\ExamRuntimeSyncStatus;
use Modules\Exam\Models\ExamActivityLog;
use Modules\Exam\Models\ExamAnswer;
use Modules\Exam\Models\ExamAttempt;
use Modules\Exam\Models\ExamDefinition;
use Modules\Exam\Models\ExamParticipant;
use Modules\Exam\Models\ExamQuestion;
use Modules\Exam\Models\ExamResult;
use Modules\Exam\Models\ExamRuntimeSyncLog;

class ExamResultSyncService
{
    public function __construct(
        protected ExamRuntimeClient $runtimeClient,
        protected ExamRuntimeSyncService $attemptSyncService,
        protected ExamAuditLogger $auditLogger,
        protected ExamGradebookEventDispatcher $gradebookEvents,
    ) {}

    /**
     * @return array{
     *     attempts_synced: int,
     *     answers_synced: int,
     *     results_synced: int,
     *     activity_logs_synced: int,
     *     participants_skipped: int,
     *     errors: list<string>
     * }
     */
    public function sync(ExamDefinition $definition): array
    {
        $runtimeExamId = $this->requireRuntimeExamId($definition);

        $payload = $this->runtimeClient->fetchExamResults($runtimeExamId);
        $summary = $this->ingestPayload($definition, $payload);

        $this->writeSyncLog($definition, $payload, $summary, null, $runtimeExamId);

        $this->auditLogger->log(
            ExamAuditAction::SyncResults,
            $definition,
            'Exam results synced from runtime.',
            newValues: $summary,
        );

        $this->dispatchGradebookSyncedEvents($definition);

        return $summary;
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array{
     *     attempts_synced: int,
     *     answers_synced: int,
     *     results_synced: int,
     *     activity_logs_synced: int,
     *     participants_skipped: int,
     *     errors: list<string>
     * }
     */
    public function ingestPayload(ExamDefinition $definition, array $payload): array
    {
        $stats = [
            'attempts_synced' => 0,
            'answers_synced' => 0,
            'results_synced' => 0,
            'activity_logs_synced' => 0,
            'participants_skipped' => 0,
            'errors' => [],
        ];

        $attemptRows = $this->normalizeList($payload['attempts'] ?? []);
        $answerRows = $this->normalizeList($payload['answers'] ?? []);
        $resultRows = $this->normalizeList($payload['results'] ?? []);
        $activityRows = $this->normalizeList($payload['activity_logs'] ?? $payload['activityLogs'] ?? []);

        foreach ($attemptRows as $row) {
            if (is_array($row) && isset($row['answers']) && is_array($row['answers'])) {
                foreach ($row['answers'] as $nestedAnswer) {
                    if (is_array($nestedAnswer)) {
                        $answerRows[] = array_merge($nestedAnswer, [
                            'attempt_id' => $nestedAnswer['attempt_id'] ?? $row['id'] ?? null,
                        ]);
                    }
                }
            }
        }

        $attemptMap = [];

        DB::transaction(function () use ($definition, $attemptRows, &$attemptMap, &$stats): void {
            foreach ($attemptRows as $index => $row) {
                if (! is_array($row)) {
                    continue;
                }

                $participant = $this->resolveParticipant($definition, $row);

                if ($participant === null) {
                    $stats['participants_skipped']++;

                    continue;
                }

                $runtimeAttemptId = $this->requireRuntimeUuid(
                    $row['id'] ?? $row['attempt_id'] ?? $row['runtime_attempt_id'] ?? null,
                    'attempt',
                );

                if ($runtimeAttemptId === null) {
                    $stats['errors'][] = 'Row '.($index + 1).': attempt id must be a UUID.';

                    continue;
                }

                $attempt = ExamAttempt::withoutTenantScope()->updateOrCreate(
                    [
                        'exam_definition_id' => $definition->id,
                        'runtime_attempt_id' => $runtimeAttemptId,
                    ],
                    [
                        'tenant_id' => $definition->tenant_id,
                        'exam_participant_id' => $participant->id,
                        'attempt_number' => (int) ($row['attempt_number'] ?? 1),
                        'status' => (string) ($row['status'] ?? 'submitted'),
                        'score' => $row['score'] ?? null,
                        'started_at' => $this->parseTimestamp($row['started_at'] ?? null),
                        'submitted_at' => $this->parseTimestamp($row['submitted_at'] ?? null),
                        'metadata_json' => $row['metadata'] ?? $row['metadata_json'] ?? null,
                    ],
                );

                $attemptMap[$runtimeAttemptId] = $attempt;
                $stats['attempts_synced']++;

                $this->mirrorAttemptSync($definition, $participant, $attempt);
            }
        });

        DB::transaction(function () use ($definition, $answerRows, $attemptMap, &$stats): void {
            foreach ($answerRows as $index => $row) {
                if (! is_array($row)) {
                    continue;
                }

                $attempt = $this->resolveAttempt($definition, $row, $attemptMap);

                if ($attempt === null) {
                    $stats['errors'][] = 'Answer row '.($index + 1).': attempt not found.';

                    continue;
                }

                $runtimeAnswerId = $this->optionalRuntimeUuid($row['id'] ?? $row['answer_id'] ?? $row['runtime_answer_id'] ?? null);
                $questionId = $this->resolveQuestionId($definition, $row);

                $match = ['exam_attempt_id' => $attempt->id];

                if ($runtimeAnswerId !== null) {
                    $match['runtime_answer_id'] = $runtimeAnswerId;
                } elseif ($questionId !== null) {
                    $match['exam_question_id'] = $questionId;
                } else {
                    $stats['errors'][] = 'Answer row '.($index + 1).': missing answer or question identifier.';

                    continue;
                }

                ExamAnswer::withoutTenantScope()->updateOrCreate(
                    $match,
                    [
                        'tenant_id' => $definition->tenant_id,
                        'exam_definition_id' => $definition->id,
                        'exam_question_id' => $questionId,
                        'answer_value' => $this->stringifyAnswerValue($row),
                        'is_correct' => isset($row['is_correct']) ? (bool) $row['is_correct'] : null,
                        'score' => $row['score'] ?? null,
                        'metadata_json' => $row['metadata'] ?? $row['metadata_json'] ?? null,
                    ],
                );

                $stats['answers_synced']++;
            }
        });

        DB::transaction(function () use ($definition, $resultRows, $attemptMap, $payload, &$stats): void {
            foreach ($resultRows as $index => $row) {
                if (! is_array($row)) {
                    continue;
                }

                $runtimeResultId = $this->requireRuntimeUuid(
                    $row['id'] ?? $row['result_id'] ?? $row['runtime_result_id'] ?? null,
                    'result',
                );

                if ($runtimeResultId === null) {
                    $stats['errors'][] = 'Result row '.($index + 1).': result id must be a UUID.';

                    continue;
                }

                $participant = $this->resolveParticipant($definition, $row);
                $attempt = $this->resolveAttempt($definition, $row, $attemptMap);

                if ($participant === null && $attempt !== null) {
                    $participant = $attempt->examParticipant;
                }

                if ($participant === null) {
                    $stats['participants_skipped']++;

                    continue;
                }

                $result = ExamResult::withoutTenantScope()->updateOrCreate(
                    [
                        'exam_definition_id' => $definition->id,
                        'runtime_result_id' => $runtimeResultId,
                    ],
                    [
                        'tenant_id' => $definition->tenant_id,
                        'exam_participant_id' => $participant->id,
                        'exam_attempt_id' => $attempt?->id,
                        'score' => $row['score'] ?? $attempt?->score,
                        'max_score' => $row['max_score'] ?? $definition->max_score,
                        'is_passed' => isset($row['passed']) ? (bool) $row['passed'] : (isset($row['is_passed']) ? (bool) $row['is_passed'] : null),
                        'grade_letter' => $row['grade_letter'] ?? $row['grade'] ?? null,
                        'status' => (string) ($row['status'] ?? 'final'),
                        'submitted_at' => $this->parseTimestamp($row['submitted_at'] ?? null) ?? $attempt?->submitted_at,
                        'analytics_json' => $row['analytics'] ?? $row['analytics_json'] ?? null,
                    ],
                );

                $this->enrichResultMetrics($definition, $result, $attempt);

                $stats['results_synced']++;
            }

            if (isset($payload['analytics']) && is_array($payload['analytics'])) {
                $this->storeExamAnalyticsSummary($definition, $payload['analytics'], $attemptMap);
            }
        });

        DB::transaction(function () use ($definition, $activityRows, $attemptMap, &$stats): void {
            foreach ($activityRows as $index => $row) {
                if (! is_array($row)) {
                    continue;
                }

                $runtimeActivityId = $this->requireRuntimeUuid(
                    $row['id'] ?? $row['activity_id'] ?? $row['runtime_activity_id'] ?? null,
                    'activity',
                );

                if ($runtimeActivityId === null) {
                    $stats['errors'][] = 'Activity row '.($index + 1).': activity id must be a UUID.';

                    continue;
                }

                $attempt = $this->resolveAttempt($definition, $row, $attemptMap);

                if ($attempt === null) {
                    $stats['errors'][] = 'Activity row '.($index + 1).': attempt not found.';

                    continue;
                }

                ExamActivityLog::withoutTenantScope()->updateOrCreate(
                    [
                        'exam_attempt_id' => $attempt->id,
                        'runtime_activity_id' => $runtimeActivityId,
                    ],
                    [
                        'tenant_id' => $definition->tenant_id,
                        'exam_definition_id' => $definition->id,
                        'exam_participant_id' => $attempt->exam_participant_id,
                        'event_type' => (string) ($row['event_type'] ?? $row['event'] ?? $row['type'] ?? 'activity'),
                        'occurred_at' => $this->parseTimestamp($row['occurred_at'] ?? $row['created_at'] ?? null),
                        'payload_json' => $row['payload'] ?? $row['payload_json'] ?? $row,
                    ],
                );

                $stats['activity_logs_synced']++;
            }
        });

        $this->refreshSuspiciousActivityCounts($definition);

        return $stats;
    }

    protected function enrichResultMetrics(ExamDefinition $definition, ExamResult $result, ?ExamAttempt $attempt): void
    {
        $maxScore = (float) ($result->max_score ?? $definition->max_score ?? 0);
        $score = (float) ($result->score ?? 0);
        $percentage = $maxScore > 0 ? round(($score / $maxScore) * 100, 2) : null;

        if ($result->is_passed === null && $definition->passing_score !== null) {
            $result->is_passed = $score >= (float) $definition->passing_score;
        }

        $result->forceFill([
            'percentage' => $percentage,
            'submitted_at' => $result->submitted_at ?? $attempt?->submitted_at,
        ])->save();
    }

    protected function refreshSuspiciousActivityCounts(ExamDefinition $definition): void
    {
        $suspiciousEvents = [
            'tab_blur',
            'tab_switch',
            'copy',
            'paste',
            'suspicious',
            'fullscreen_exit',
            'devtools',
        ];

        $results = ExamResult::withoutTenantScope()
            ->where('exam_definition_id', $definition->id)
            ->whereNotNull('exam_attempt_id')
            ->get();

        foreach ($results as $result) {
            $count = ExamActivityLog::withoutTenantScope()
                ->where('exam_attempt_id', $result->exam_attempt_id)
                ->whereIn('event_type', $suspiciousEvents)
                ->count();

            $result->forceFill(['suspicious_activity_count' => $count])->save();
        }
    }

    /**
     * @param  array<int, ExamDefinition>  $definitions
     * @return list<array{exam_definition_id: string, summary: array<string, mixed>|null, error: ?string}>
     */
    public function syncMany(iterable $definitions): array
    {
        $reports = [];

        foreach ($definitions as $definition) {
            try {
                $reports[] = [
                    'exam_definition_id' => $definition->id,
                    'summary' => $this->sync($definition),
                    'error' => null,
                ];
            } catch (\Throwable $exception) {
                $runtimeExamId = $definition->runtime_exam_id;

                $this->writeSyncLog(
                    $definition,
                    [],
                    null,
                    $exception->getMessage(),
                    Str::isUuid((string) $runtimeExamId) ? $runtimeExamId : null,
                    ExamRuntimeSyncStatus::Failed,
                );

                $reports[] = [
                    'exam_definition_id' => $definition->id,
                    'summary' => null,
                    'error' => $exception->getMessage(),
                ];
            }
        }

        return $reports;
    }

    protected function requireRuntimeExamId(ExamDefinition $definition): string
    {
        $runtimeExamId = (string) $definition->runtime_exam_id;

        if (! Str::isUuid($runtimeExamId)) {
            throw new \InvalidArgumentException('Publish the exam to runtime before syncing results.');
        }

        return $runtimeExamId;
    }

    protected function resolveParticipant(ExamDefinition $definition, array $row): ?ExamParticipant
    {
        $foundationId = $row['participant_external_id']
            ?? $row['participant_foundation_id']
            ?? $row['external_id']
            ?? $row['foundation_id']
            ?? null;

        if (is_string($foundationId) && Str::isUuid($foundationId)) {
            $participant = ExamParticipant::withoutTenantScope()
                ->where('exam_definition_id', $definition->id)
                ->where('id', $foundationId)
                ->first();

            if ($participant !== null) {
                return $participant;
            }
        }

        $runtimeParticipantId = $row['participant_id'] ?? $row['runtime_participant_id'] ?? null;

        if (is_string($runtimeParticipantId) && Str::isUuid($runtimeParticipantId)) {
            return ExamParticipant::withoutTenantScope()
                ->where('exam_definition_id', $definition->id)
                ->where('metadata_json->runtime_participant_id', $runtimeParticipantId)
                ->first();
        }

        return null;
    }

    /**
     * @param  array<string, ExamAttempt>  $attemptMap
     */
    protected function resolveAttempt(ExamDefinition $definition, array $row, array $attemptMap): ?ExamAttempt
    {
        $runtimeAttemptId = $this->optionalRuntimeUuid(
            $row['attempt_id'] ?? $row['runtime_attempt_id'] ?? $row['attemptId'] ?? null,
        );

        if ($runtimeAttemptId !== null && isset($attemptMap[$runtimeAttemptId])) {
            return $attemptMap[$runtimeAttemptId];
        }

        if ($runtimeAttemptId !== null) {
            return ExamAttempt::withoutTenantScope()
                ->where('exam_definition_id', $definition->id)
                ->where('runtime_attempt_id', $runtimeAttemptId)
                ->first();
        }

        return null;
    }

    protected function resolveQuestionId(ExamDefinition $definition, array $row): ?string
    {
        $questionId = $row['question_external_id']
            ?? $row['question_foundation_id']
            ?? $row['exam_question_id']
            ?? $row['question_id']
            ?? null;

        if (! is_string($questionId) || ! Str::isUuid($questionId)) {
            return null;
        }

        $exists = ExamQuestion::withoutTenantScope()
            ->where('tenant_id', $definition->tenant_id)
            ->whereKey($questionId)
            ->exists();

        return $exists ? $questionId : null;
    }

    protected function mirrorAttemptSync(ExamDefinition $definition, ExamParticipant $participant, ExamAttempt $attempt): void
    {
        $this->attemptSyncService->ingestAttempt($definition, $participant, [
            'runtime_attempt_id' => $attempt->runtime_attempt_id,
            'score' => $attempt->score,
            'sync_status' => 'synced',
            'result_json' => [
                'exam_attempt_id' => $attempt->id,
                'runtime_attempt_id' => $attempt->runtime_attempt_id,
                'status' => $attempt->status,
            ],
            'submitted_at' => $attempt->submitted_at?->toIso8601String(),
        ]);
    }

    /**
     * @param  array<string, ExamAttempt>  $attemptMap
     * @param  array<string, mixed>  $analytics
     */
    protected function storeExamAnalyticsSummary(ExamDefinition $definition, array $analytics, array $attemptMap): void
    {
        $metadata = $definition->metadata_json ?? [];
        $metadata['runtime_analytics_summary'] = $analytics;
        $definition->forceFill(['metadata_json' => $metadata])->save();

        foreach ($analytics['by_attempt'] ?? [] as $row) {
            if (! is_array($row)) {
                continue;
            }

            $attempt = $this->resolveAttempt($definition, $row, $attemptMap);

            if ($attempt === null) {
                continue;
            }

            $attempt->forceFill([
                'metadata_json' => array_merge($attempt->metadata_json ?? [], [
                    'analytics' => $row,
                ]),
            ])->save();
        }
    }

    /**
     * @param  array<string, mixed>  $payload
     * @param  array<string, mixed>|null  $summary
     */
    protected function writeSyncLog(
        ExamDefinition $definition,
        array $payload,
        ?array $summary,
        ?string $errorMessage,
        ?string $runtimeId,
        ExamRuntimeSyncStatus $status = ExamRuntimeSyncStatus::Success,
    ): ExamRuntimeSyncLog {
        return ExamRuntimeSyncLog::query()->create([
            'tenant_id' => $definition->tenant_id,
            'exam_definition_id' => $definition->id,
            'action' => ExamRuntimeSyncAction::SyncResults,
            'status' => $status,
            'runtime_id' => $runtimeId,
            'request_summary' => [
                'runtime_exam_id' => $runtimeId,
                'attempt_count' => count($payload['attempts'] ?? []),
                'answer_count' => count($payload['answers'] ?? []),
                'result_count' => count($payload['results'] ?? []),
                'activity_log_count' => count($payload['activity_logs'] ?? $payload['activityLogs'] ?? []),
            ],
            'response_summary' => $summary,
            'error_message' => $errorMessage,
        ]);
    }

    /**
     * @return list<mixed>
     */
    protected function normalizeList(mixed $value): array
    {
        if (! is_array($value)) {
            return [];
        }

        return array_is_list($value) ? $value : [$value];
    }

    protected function parseTimestamp(mixed $value): ?Carbon
    {
        if ($value === null || $value === '') {
            return null;
        }

        try {
            return Carbon::parse($value);
        } catch (\Throwable) {
            return null;
        }
    }

    protected function stringifyAnswerValue(array $row): ?string
    {
        $value = $row['answer_value'] ?? $row['value'] ?? $row['answer'] ?? null;

        if (is_array($value)) {
            return json_encode($value, JSON_THROW_ON_ERROR);
        }

        return $value !== null ? (string) $value : null;
    }

    protected function requireRuntimeUuid(mixed $value, string $label): ?string
    {
        if (! is_string($value) || ! Str::isUuid($value)) {
            return null;
        }

        return $value;
    }

    protected function optionalRuntimeUuid(mixed $value): ?string
    {
        return is_string($value) && Str::isUuid($value) ? $value : null;
    }

    protected function dispatchGradebookSyncedEvents(ExamDefinition $definition): void
    {
        ExamResult::withoutTenantScope()
            ->where('exam_definition_id', $definition->id)
            ->whereNotNull('exam_participant_id')
            ->each(fn (ExamResult $result): mixed => $this->gradebookEvents->synced($result));
    }
}
