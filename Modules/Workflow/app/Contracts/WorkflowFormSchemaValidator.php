<?php

namespace Modules\Workflow\Contracts;

use Modules\Workflow\Models\WorkflowStep;

interface WorkflowFormSchemaValidator
{
    public function validate(WorkflowStep $step, array $formData, array $context = []): array;
}
