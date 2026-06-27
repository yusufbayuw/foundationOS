<?php

namespace Modules\Exam\Services;

use App\Support\TypedValue;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
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
        return DB::transaction(function () use ($definition, $participant, $payload): ExamAttemptSync {
            $attemptSync = ExamAttemptSync::query()->updateOrCreate(
                [
                    'exam_definition_id' => $definition->id,
                    'exam_participant_id' => $participant->id,
                    'runtime_attempt_id' => $payload['runtime_attempt_id'] ?? null,
                ],
                [
                    'tenant_id' => $definition->tenant_id,
                    'sync_status' => $payload['sync_status'] ?? 'received',
                    'score' => $payload['score'] ?? null,
                    'result_json' => $payload['result_json'] ?? $payload,
                    'submitted_at' => isset($payload['submitted_at'])
                        ? Carbon::parse(TypedValue::string($payload['submitted_at']))
                        : now(),
                ],
            );

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
