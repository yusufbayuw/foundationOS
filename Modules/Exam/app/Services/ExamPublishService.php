<?php

namespace Modules\Exam\Services;

use App\Support\TypedValue;
use Illuminate\Support\Str;
use Modules\Exam\Enums\ExamAuditAction;
use Modules\Exam\Enums\ExamRuntimeSyncAction;
use Modules\Exam\Enums\ExamRuntimeSyncStatus;
use Modules\Exam\Enums\ExamStatus;
use Modules\Exam\Exceptions\ExamRuntimeException;
use Modules\Exam\Models\ExamDefinition;
use Modules\Exam\Models\ExamPublishSnapshot;
use Modules\Exam\Models\ExamRuntimeSyncLog;

class ExamPublishService
{
    public function __construct(
        protected ExamRuntimePayloadBuilder $payloadBuilder,
        protected ExamRuntimeClient $runtimeClient,
        protected ExamDefinitionScoreCalculator $scoreCalculator,
        protected ExamAuditLogger $auditLogger,
        protected ExamGradebookEventDispatcher $gradebookEvents,
    ) {}

    public function publish(ExamDefinition $definition): ExamPublishSnapshot
    {
        if (! in_array($definition->status, [ExamStatus::Ready, ExamStatus::Scheduled], true)) {
            throw new \InvalidArgumentException('Only ready exams can be published.');
        }

        if ($this->scoreCalculator->questionCount($definition) < 1) {
            throw new \InvalidArgumentException('Add at least one question before publishing.');
        }

        return $this->pushToRuntime($definition, ExamRuntimeSyncAction::Publish, markPublished: true);
    }

    public function republish(ExamDefinition $definition): ExamPublishSnapshot
    {
        if (! in_array($definition->status, [ExamStatus::Published, ExamStatus::Scheduled, ExamStatus::Ready], true)) {
            throw new \InvalidArgumentException('Only published or ready exams can be republished.');
        }

        if ($this->scoreCalculator->questionCount($definition) < 1) {
            throw new \InvalidArgumentException('Add at least one question before republishing.');
        }

        return $this->pushToRuntime($definition, ExamRuntimeSyncAction::Republish, markPublished: false);
    }

    public function syncParticipantsOnly(ExamDefinition $definition): ExamRuntimeSyncLog
    {
        $this->assertRuntimeMapped($definition);

        $payload = $this->payloadBuilder->buildParticipantsOnly($definition);

        return $this->executeSync(
            $definition,
            ExamRuntimeSyncAction::SyncParticipants,
            $payload,
            fn () => $this->runtimeClient->syncParticipants($payload),
        );
    }

    public function syncAdminAccessOnly(ExamDefinition $definition): ExamRuntimeSyncLog
    {
        $this->assertRuntimeMapped($definition);

        $payload = $this->payloadBuilder->buildAdminAccessOnly($definition);

        return $this->executeSync(
            $definition,
            ExamRuntimeSyncAction::SyncAdminAccess,
            $payload,
            fn () => $this->runtimeClient->syncAdminAccess($payload),
        );
    }

    protected function pushToRuntime(
        ExamDefinition $definition,
        ExamRuntimeSyncAction $action,
        bool $markPublished,
    ): ExamPublishSnapshot {
        if ($definition->max_score === null) {
            $definition->forceFill([
                'max_score' => $this->scoreCalculator->totalScore($definition),
            ])->save();
        }

        $payload = $this->payloadBuilder->buildFull($definition);
        $snapshot = $this->createSnapshot($definition, $payload);

        try {
            $response = $this->runtimeClient->publishExam($payload);
            $parsed = $this->runtimeClient->parsePublishResponse($response);

            $this->writeSyncLog(
                $definition,
                $action,
                ExamRuntimeSyncStatus::Success,
                $payload,
                $response,
                null,
                $parsed['runtime_exam_id'],
            );

            $snapshot->forceFill([
                'runtime_exam_id' => $parsed['runtime_exam_id'],
                'publish_status' => $parsed['publish_status'],
                'published_at' => now(),
                'error_message' => null,
            ])->save();

            $definition->forceFill([
                'runtime_exam_id' => $parsed['runtime_exam_id'],
                'last_published_at' => now(),
            ]);

            if ($markPublished) {
                $definition->forceFill([
                    'status' => ExamStatus::Published,
                    'published_at' => $definition->published_at ?? now(),
                ]);
            }

            $definition->save();

            $this->auditLogger->log(
                $action === ExamRuntimeSyncAction::Republish
                    ? ExamAuditAction::RepublishExam
                    : ExamAuditAction::PublishExam,
                $definition,
                $action === ExamRuntimeSyncAction::Republish
                    ? 'Exam republished to runtime.'
                    : 'Exam published to runtime.',
                newValues: [
                    'runtime_exam_id' => $parsed['runtime_exam_id'],
                    'exam_definition_id' => $definition->id,
                ],
            );

            if ($markPublished) {
                $this->gradebookEvents->published($definition, [
                    'runtime_exam_id' => $parsed['runtime_exam_id'],
                ]);
            }

            return $snapshot->refresh();
        } catch (ExamRuntimeException $exception) {
            $this->writeSyncLog(
                $definition,
                $action,
                ExamRuntimeSyncStatus::Failed,
                $payload,
                $exception->responseBody,
                $exception->getMessage(),
                $this->normalizeRuntimeId($definition->runtime_exam_id),
            );

            $snapshot->forceFill([
                'publish_status' => 'failed',
                'error_message' => $exception->getMessage(),
            ])->save();

            throw $exception;
        }
    }

    /**
     * @param  array<string, mixed>  $payload
     * @param  callable(): array<string, mixed>  $callback
     */
    protected function executeSync(
        ExamDefinition $definition,
        ExamRuntimeSyncAction $action,
        array $payload,
        callable $callback,
    ): ExamRuntimeSyncLog {
        try {
            $response = $callback();
            $runtimeId = TypedValue::string($response['runtime_id'] ?? $definition->runtime_exam_id ?? '');

            $log = $this->writeSyncLog(
                $definition,
                $action,
                ExamRuntimeSyncStatus::Success,
                $payload,
                $response,
                null,
                Str::isUuid($runtimeId) ? $runtimeId : $this->normalizeRuntimeId($definition->runtime_exam_id),
            );

            if ($action === ExamRuntimeSyncAction::SyncParticipants) {
                $this->auditLogger->log(
                    ExamAuditAction::SyncParticipants,
                    $definition,
                    'Exam participants synced to runtime.',
                    newValues: ['participant_count' => $log->request_summary['participant_count'] ?? null],
                );
            }

            return $log;
        } catch (ExamRuntimeException $exception) {
            $this->writeSyncLog(
                $definition,
                $action,
                ExamRuntimeSyncStatus::Failed,
                $payload,
                $exception->responseBody,
                $exception->getMessage(),
                $this->normalizeRuntimeId($definition->runtime_exam_id),
            );

            throw $exception;
        }
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    protected function createSnapshot(ExamDefinition $definition, array $payload): ExamPublishSnapshot
    {
        $version = TypedValue::int($definition->examPublishSnapshots()->max('version')) + 1;

        return ExamPublishSnapshot::query()->create([
            'tenant_id' => $definition->tenant_id,
            'exam_definition_id' => $definition->id,
            'version' => $version,
            'payload_json' => $payload,
            'publish_status' => 'pending',
        ]);
    }

    /**
     * @param  array<string, mixed>  $payload
     * @param  array<string, mixed>|null  $response
     */
    protected function writeSyncLog(
        ExamDefinition $definition,
        ExamRuntimeSyncAction $action,
        ExamRuntimeSyncStatus $status,
        array $payload,
        ?array $response,
        ?string $errorMessage,
        ?string $runtimeId,
    ): ExamRuntimeSyncLog {
        return ExamRuntimeSyncLog::query()->create([
            'tenant_id' => $definition->tenant_id,
            'exam_definition_id' => $definition->id,
            'action' => $action,
            'status' => $status,
            'runtime_id' => $runtimeId,
            'request_summary' => $this->payloadBuilder->summarizeForLog($payload),
            'response_summary' => $this->summarizeResponse($response),
            'error_message' => $errorMessage,
        ]);
    }

    /**
     * @param  array<string, mixed>|null  $response
     * @return array<string, mixed>|null
     */
    protected function summarizeResponse(?array $response): ?array
    {
        if ($response === null) {
            return null;
        }

        $data = $response['data'] ?? null;
        $nestedRuntimeId = is_array($data) ? ($data['runtime_id'] ?? null) : null;

        return [
            'runtime_id' => $response['runtime_id'] ?? $nestedRuntimeId,
            'external_id' => $response['external_id'] ?? $response['foundation_id'] ?? null,
            'status' => $response['status'] ?? null,
            'message' => $response['message'] ?? null,
        ];
    }

    protected function assertRuntimeMapped(ExamDefinition $definition): void
    {
        if (! Str::isUuid((string) $definition->runtime_exam_id)) {
            throw new \InvalidArgumentException('Publish the exam to runtime before running this sync.');
        }
    }

    protected function normalizeRuntimeId(?string $runtimeId): ?string
    {
        if ($runtimeId === null || $runtimeId === '') {
            return null;
        }

        return Str::isUuid($runtimeId) ? $runtimeId : null;
    }
}
