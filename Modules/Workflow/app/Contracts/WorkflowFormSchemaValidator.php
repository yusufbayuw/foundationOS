<?php

namespace Modules\Workflow\Contracts;

use Modules\Workflow\Models\WorkflowStep;

interface WorkflowFormSchemaValidator
{
    /**
     * @param  array<string, mixed>  $formData
     * @param  array<string, mixed>  $context
     * @return array<string, mixed>
     */
    public function validate(WorkflowStep $step, array $formData, array $context = []): array;
}
