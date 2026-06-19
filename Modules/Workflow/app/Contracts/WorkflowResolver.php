<?php

namespace Modules\Workflow\Contracts;

use Modules\Workflow\Models\Workflow;

interface WorkflowResolver
{
    public function resolveForSubject(?string $subjectType, mixed $subject, int $tenantId, ?int $organizationId): Workflow;
}
