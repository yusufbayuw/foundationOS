<?php

namespace Modules\Workflow\Services;

use Filament\Facades\Filament;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\User;
use Modules\Workflow\Contracts\ProvidesWorkflowContext;
use Modules\Workflow\Contracts\StartsWorkflow;
use Modules\Workflow\Contracts\WorkflowInstanceStarter;
use Modules\Workflow\Contracts\WorkflowResolver;
use Modules\Workflow\Enums\WorkflowInstanceStatus;
use Modules\Workflow\Models\WorkflowInstance;
use Throwable;

class WorkflowSubjectPageService
{
    public function __construct(
        private WorkflowResolver $workflowResolver,
        private WorkflowInstanceStarter $workflowInstanceStarter,
    ) {}

    public function hasActiveInstance(Model $subject): bool
    {
        return $this->activeInstanceQuery($subject)->exists();
    }

    public function findActiveInstance(Model $subject): ?WorkflowInstance
    {
        return $this->activeInstanceQuery($subject)
            ->latest('started_at')
            ->first();
    }

    /**
     * @throws Throwable
     */
    public function startApprovalWorkflow(Model $subject, User $user, ?Tenant $tenant = null): WorkflowInstance
    {
        if (! $subject instanceof ProvidesWorkflowContext || ! $subject instanceof StartsWorkflow) {
            throw new \InvalidArgumentException('Subject must implement workflow contracts.');
        }

        $tenant ??= Filament::getTenant();

        $this->assertTenantScope($subject, $tenant);
        Gate::forUser($user)->authorize('update', $subject);

        if (method_exists($subject, 'isLockedForMutation') && $subject->isLockedForMutation()) {
            throw new AuthorizationException('This record cannot start a workflow in its current status.');
        }

        $workflow = $this->workflowResolver->resolveForSubject(
            $subject->workflowSubjectType(),
            $subject,
            (int) ($tenant?->getKey() ?? $subject->tenant_id),
            data_get($subject, 'organization_id'),
        );

        return $this->workflowInstanceStarter->start(
            $workflow,
            $user,
            $subject->workflowContext(),
            $subject,
            $user,
        );
    }

    /**
     * @param  Model&ProvidesWorkflowContext  $subject
     */
    private function activeInstanceQuery(Model $subject)
    {
        return WorkflowInstance::query()
            ->where('subject_type', $subject::class)
            ->where('subject_id', $subject->getKey())
            ->where('status', WorkflowInstanceStatus::Running);
    }

    private function assertTenantScope(Model $subject, ?Tenant $tenant): void
    {
        if ($tenant === null) {
            return;
        }

        $subjectTenantId = data_get($subject, 'tenant_id');

        if ($subjectTenantId !== null && (int) $subjectTenantId !== (int) $tenant->getKey()) {
            throw new AuthorizationException('Record does not belong to the current tenant.');
        }
    }
}
