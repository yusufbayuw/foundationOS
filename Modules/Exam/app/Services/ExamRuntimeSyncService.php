<?php

namespace Modules\Exam\Services;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Modules\Exam\Contracts\GradeBridgeInterface;
use Modules\Exam\Models\ExamAttemptSync;
use Modules\Exam\Models\ExamDefinition;
use Modules\Exam\Models\ExamParticipant;

class ExamRuntimeSyncService
{
    /**
     * @param  iterable<GradeBridgeInterface>  $gradeBridges
     */
    public function __construct(
        protected iterable $gradeBridges = [],
    ) {}

    /**
     * @param  array<string, mixed>  $payload
     */
    public function ingestAttempt(ExamDefinition $definition, ExamParticipant $participant, array $payload): ExamAttemptSync
    {
        $runtimeAttemptId = $payload['runtime_attempt_id'] ?? null;

        if (! is_string($runtimeAttemptId) || ! Str::isUuid($runtimeAttemptId)) {
            throw ValidationException::withMessages([
                'runtime_attempt_id' => __('The runtime attempt ID must be a valid UUID.'),
            ]);
        }

        if (
            (int) $participant->tenant_id !== (int) $definition->tenant_id
            || (string) $participant->exam_definition_id !== (string) $definition->getKey()
        ) {
            throw ValidationException::withMessages([
                'exam_participant_id' => __('The selected participant does not belong to this exam.'),
            ]);
        }

        return DB::transaction(function () use ($definition, $participant, $payload): ExamAttemptSync {
            $values = [
                'tenant_id' => $definition->tenant_id,
                'exam_participant_id' => $participant->id,
                'sync_status' => $payload['sync_status'] ?? 'received',
                'score' => $payload['score'] ?? null,
                'result_json' => $payload['result_json'] ?? $payload,
                'submitted_at' => isset($payload['submitted_at']) ? Carbon::parse($payload['submitted_at']) : now(),
            ];

            $attemptSync = ExamAttemptSync::query()->firstOrCreate(
                [
                    'exam_definition_id' => $definition->id,
                    'runtime_attempt_id' => $payload['runtime_attempt_id'],
                ],
                $values,
            );

            if ((string) $attemptSync->exam_participant_id !== (string) $participant->getKey()) {
                throw ValidationException::withMessages([
                    'runtime_attempt_id' => __('The runtime attempt ID is already assigned to another participant.'),
                ]);
            }

            if (! $attemptSync->wasRecentlyCreated) {
                $attemptSync->fill($values)->save();
            }

            $this->dispatchGradeBridges($definition, $attemptSync);

            return $attemptSync->refresh();
        });
    }

    protected function dispatchGradeBridges(ExamDefinition $definition, ExamAttemptSync $attemptSync): void
    {
        foreach ($this->gradeBridges as $bridge) {
            if ($bridge->supports($definition)) {
                $bridge->syncAttempt($definition, $attemptSync);
            }
        }
    }
}
