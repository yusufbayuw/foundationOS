<?php

namespace Modules\Workflow\Contracts;

use Illuminate\Database\Eloquent\Model;
use Modules\Workflow\Models\Workflow;

interface WorkflowResolver
{
    public function resolveForSubject(?string $subjectType, mixed $subject, int $tenantId, ?int $organizationId): Workflow;
}
