<?php

namespace App\Queue\Middleware;

use App\Exceptions\MissingTenantContextException;
use App\Support\CurrentTenant;
use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Events\CallQueuedListener;
use Modules\Core\Models\Tenant;
use Modules\Enrollment\Events\ApplicantAccepted;
use Modules\Finance\Listeners\CreateInitialInvoiceFromAcceptedApplicant;
use Modules\Procurement\Events\PurchaseRequisitionApproved;
use Modules\Procurement\Listeners\CreateRfqFromApprovedPurchaseRequisition;
use Modules\Workflow\Events\WorkflowAdvanced;
use Modules\Workflow\Events\WorkflowCancelled;
use Modules\Workflow\Events\WorkflowReturned;
use Modules\Workflow\Events\WorkflowSlaBreached;
use Modules\Workflow\Events\WorkflowStarted;
use Modules\Workflow\Listeners\RunWorkflowAutomatedActions;

class WithTenantContext
{
    public function __construct(public int|string|null $tenantId) {}

    public function handle(object $job, Closure $next): mixed
    {
        $resolvedTenantId = $this->tenantId;

        if ($job instanceof CallQueuedListener) {
            // All queued listeners use the same framework wrapper class. Never
            // authorize that wrapper by name: verify the actual listener and its
            // event type, then bind the tenant belonging to the event's model.
            $eventTenantId = $this->queuedListenerTenantId($job);

            if ($eventTenantId === null
                || ($resolvedTenantId !== null && (string) $resolvedTenantId !== (string) $eventTenantId)) {
                throw new MissingTenantContextException($job->class);
            }

            $resolvedTenantId = $eventTenantId;
        }

        // Two legacy jobs are independently reviewed system dispatches. A
        // tenantless listener is never a system job, regardless of its wrapper.
        $isTenantlessSystemJob = $resolvedTenantId === null
            && in_array($job::class, config('tenancy.queue_tenantless_job_types', []), true);

        if ($resolvedTenantId === null && ! $isTenantlessSystemJob) {
            throw new MissingTenantContextException($job::class);
        }

        $tenant = $resolvedTenantId === null ? null : Tenant::query()->find($resolvedTenantId);

        if ($resolvedTenantId !== null && $tenant === null) {
            throw new \RuntimeException('Queued tenant is missing or deleted.');
        }

        // Suspended tenants may still require recovery/cleanup jobs. Even for
        // explicitly global jobs, reset the Spatie permission team for the call.
        $previousTeamId = getPermissionsTeamId();

        return app(CurrentTenant::class)->forTenant($tenant, function () use ($job, $next, $previousTeamId, $tenant): mixed {
            setPermissionsTeamId($tenant?->getKey() ?? 0);

            try {
                return $next($job);
            } finally {
                setPermissionsTeamId($previousTeamId);
            }
        });
    }

    /**
     * Resolve only explicitly reviewed, domain-specific queued listeners.
     * The event's persisted model carries the destination tenant; an
     * arbitrary listener cannot inherit that tenant from a previous job.
     */
    private function queuedListenerTenantId(CallQueuedListener $job): int|string|null
    {
        if ($job->method !== 'handle') {
            return null;
        }

        $event = $job->data[0] ?? null;

        $subject = match (true) {
            $job->class === RunWorkflowAutomatedActions::class
                && ($event instanceof WorkflowStarted
                    || $event instanceof WorkflowAdvanced
                    || $event instanceof WorkflowReturned
                    || $event instanceof WorkflowCancelled
                    || $event instanceof WorkflowSlaBreached) => $event->instance,
            $job->class === CreateInitialInvoiceFromAcceptedApplicant::class
                && $event instanceof ApplicantAccepted => $event->applicant,
            $job->class === CreateRfqFromApprovedPurchaseRequisition::class
                && $event instanceof PurchaseRequisitionApproved => $event->requisition,
            default => null,
        };

        if (! $subject instanceof Model || ! $subject->exists) {
            return null;
        }

        $tenantId = $subject->getAttribute('tenant_id');

        return is_int($tenantId) || is_string($tenantId) ? $tenantId : null;
    }
}
