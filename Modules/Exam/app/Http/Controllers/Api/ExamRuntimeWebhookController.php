<?php

namespace Modules\Exam\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Support\TypedValue;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Exam\Models\ExamDefinition;
use Modules\Exam\Models\ExamParticipant;
use Modules\Exam\Services\ExamRuntimeSyncService;

class ExamRuntimeWebhookController extends Controller
{
    public function storeAttempt(Request $request, ExamRuntimeSyncService $syncService): JsonResponse
    {
        /**
         * @var array{
         *   exam_definition_id: string,
         *   exam_participant_id: string,
         *   runtime_attempt_id?: string|null,
         *   score?: int|float|string|null,
         *   sync_status?: string|null,
         *   result_json?: array<string, mixed>|null,
         *   submitted_at?: string|null
         * } $validated
         */
        $validated = $request->validate([
            'exam_definition_id' => ['required', 'uuid'],
            'exam_participant_id' => ['required', 'uuid'],
            'runtime_attempt_id' => ['nullable', 'string'],
            'score' => ['nullable', 'numeric'],
            'sync_status' => ['nullable', 'string'],
            'result_json' => ['nullable', 'array'],
            'submitted_at' => ['nullable', 'date'],
        ]);

        /** @var ExamDefinition $definition */
        $definition = ExamDefinition::query()->findOrFail(TypedValue::string($validated['exam_definition_id']));
        /** @var ExamParticipant $participant */
        $participant = ExamParticipant::query()
            ->where('exam_definition_id', $definition->id)
            ->findOrFail(TypedValue::string($validated['exam_participant_id']));

        $attemptSync = $syncService->ingestAttempt($definition, $participant, $validated);

        return response()->json([
            'data' => [
                'id' => $attemptSync->id,
                'sync_status' => $attemptSync->sync_status,
            ],
        ]);
    }
}
