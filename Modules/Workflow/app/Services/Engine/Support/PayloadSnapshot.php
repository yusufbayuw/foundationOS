<?php

namespace Modules\Workflow\Services\Engine\Support;

use Modules\Workflow\Models\WorkflowInstance;

class PayloadSnapshot
{
    /**
     * @return array<string, mixed>
     */
    public function capture(WorkflowInstance $instance): array
    {
        return [
            'context_data' => $instance->context_data,
            'form_data' => $instance->form_data,
            'computed_data' => $instance->computed_data,
            'current_assignees' => $instance->current_assignees,
            'status' => $instance->status?->value ?? $instance->status,
            'current_step_id' => $instance->current_step_id,
        ];
    }
}
