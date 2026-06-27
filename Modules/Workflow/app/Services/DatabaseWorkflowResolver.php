<?php

namespace Modules\Workflow\Services;

use App\Support\TypedValue;
use Illuminate\Database\Eloquent\Model;
use Modules\Workflow\Contracts\StartsWorkflow;
use Modules\Workflow\Contracts\WorkflowResolver;
use Modules\Workflow\Enums\WorkflowDefinitionStatus;
use Modules\Workflow\Exceptions\WorkflowConfigurationException;
use Modules\Workflow\Models\Workflow;

class DatabaseWorkflowResolver implements WorkflowResolver
{
    public function resolveForSubject(?string $subjectType, mixed $subject, int $tenantId, ?int $organizationId): Workflow
    {
        $resolvedSubjectType = $subjectType ?: ($subject instanceof Model ? $subject::class : null);
        $workflowCode = $subject instanceof StartsWorkflow ? $subject->workflowCode() : null;

        $workflows = Workflow::query()
            ->where('tenant_id', $tenantId)
            ->where('is_active', true)
            ->where('status', WorkflowDefinitionStatus::Active)
            ->when($resolvedSubjectType, function ($query) use ($resolvedSubjectType): void {
                $query->where(function ($inner) use ($resolvedSubjectType): void {
                    $inner->where('subject_type', $resolvedSubjectType)
                        ->orWhereNull('subject_type');
                });
            })
            ->when(! $resolvedSubjectType, fn ($query) => $query->whereNull('subject_type'))
            ->when($organizationId, function ($query) use ($organizationId): void {
                $query->where(function ($inner) use ($organizationId): void {
                    $inner->where($inner->getModel()->qualifyColumn('organization_id'), $organizationId)
                        ->orWhereNull('organization_id');
                });
            }, fn ($query) => $query->whereNull('organization_id'))
            ->get()
            ->sortByDesc(fn (Workflow $workflow) => [
                $workflowCode !== null && $workflow->code === $workflowCode ? 1 : 0,
                $workflow->organization_id === $organizationId ? 1 : 0,
                $workflow->subject_type === $resolvedSubjectType ? 1 : 0,
                $workflow->version,
                TypedValue::int(data_get($workflow, 'published_at.timestamp'), 0),
            ])
            ->values();

        $workflow = $workflows->first();

        if (! $workflow) {
            throw new WorkflowConfigurationException('No active workflow definition matches the provided subject and scope.');
        }

        return $workflow;
    }
}
