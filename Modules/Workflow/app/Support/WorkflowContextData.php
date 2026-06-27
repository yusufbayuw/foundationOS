<?php

namespace Modules\Workflow\Support;

use Modules\Workflow\Models\WorkflowInstance;

class WorkflowContextData
{
    /**
     * @param  array<string, mixed>  $incoming
     * @return array<string, mixed>
     */
    public static function fromInstance(WorkflowInstance $instance, array $incoming = []): array
    {
        $merged = array_replace_recursive(
            $instance->context_data ?? [],
            $instance->form_data ?? [],
            $instance->computed_data ?? [],
            $incoming,
            [
                '_workflow' => [
                    'instance_id' => $instance->getKey(),
                    'workflow_id' => $instance->workflow_id,
                    'workflow_version' => $instance->workflow_version,
                    'status' => $instance->status->value,
                    'current_step_id' => $instance->current_step_id,
                    'tenant_id' => $instance->tenant_id,
                    'organization_id' => $instance->organization_id,
                ],
                '_subject' => [
                    'type' => $instance->subject_type,
                    'id' => $instance->subject_id,
                    'label' => $instance->subject_label,
                ],
            ],
        );

        $normalized = [];

        foreach ($merged as $key => $value) {
            $normalized[(string) $key] = $value;
        }

        return $normalized;
    }
}
