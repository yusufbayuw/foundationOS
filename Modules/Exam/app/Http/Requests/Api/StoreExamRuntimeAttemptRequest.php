<?php

namespace Modules\Exam\Http\Requests\Api;

use App\Support\CurrentTenant;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Exam\Enums\ExamPermission;
use Modules\Exam\Models\ExamDefinition;
use Modules\Exam\Models\ExamParticipant;

class StoreExamRuntimeAttemptRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(CurrentTenant $currentTenant): array
    {
        return [
            'exam_definition_id' => [
                'required',
                'uuid',
                Rule::exists(ExamDefinition::class, 'id')
                    ->where('tenant_id', $currentTenant->id())
                    ->whereNull('deleted_at'),
            ],
            'exam_participant_id' => [
                'required',
                'uuid',
                Rule::exists(ExamParticipant::class, 'id')
                    ->where('tenant_id', $currentTenant->id())
                    ->where('exam_definition_id', $this->input('exam_definition_id'))
                    ->whereNull('deleted_at'),
            ],
            'runtime_attempt_id' => ['required', 'uuid'],
            'score' => ['nullable', 'numeric', 'min:0'],
            'result_json' => ['nullable', 'array', 'max:500'],
            'submitted_at' => ['nullable', 'date'],
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can(ExamPermission::SyncExamResult->value) ?? false;
    }
}
