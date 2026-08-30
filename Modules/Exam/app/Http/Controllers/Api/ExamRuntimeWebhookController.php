<?php

namespace Modules\Exam\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\Exam\Http\Requests\Api\StoreExamRuntimeAttemptRequest;
use Modules\Exam\Models\ExamDefinition;
use Modules\Exam\Services\ExamRuntimeSyncService;

class ExamRuntimeWebhookController extends Controller
{
    public function storeAttempt(
        StoreExamRuntimeAttemptRequest $request,
        ExamRuntimeSyncService $syncService,
    ): JsonResponse {
        $validated = $request->validated();
        $definition = ExamDefinition::query()->findOrFail($validated['exam_definition_id']);
        $this->authorize('syncResults', $definition);

        $participant = $definition->examParticipants()
            ->findOrFail($validated['exam_participant_id']);

        $attemptSync = $syncService->ingestAttempt($definition, $participant, $validated);

        return response()->json([
            'data' => [
                'id' => $attemptSync->id,
                'sync_status' => $attemptSync->sync_status,
            ],
        ]);
    }
}
