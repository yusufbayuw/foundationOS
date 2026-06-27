<?php

namespace Modules\Workflow\Services;

use App\Support\TypedValue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Modules\Core\Models\User;
use Modules\Workflow\Contracts\ProvidesWorkflowContext;
use Modules\Workflow\Contracts\WorkflowAuditLogger;
use Modules\Workflow\Contracts\WorkflowInstanceStarter;
use Modules\Workflow\Contracts\WorkflowSlaService;
use Modules\Workflow\Enums\WorkflowInstanceStatus;
use Modules\Workflow\Enums\WorkflowLogType;
use Modules\Workflow\Events\WorkflowStarted;
use Modules\Workflow\Exceptions\WorkflowConfigurationException;
use Modules\Workflow\Models\Workflow;
use Modules\Workflow\Models\WorkflowAutomatedAction;
use Modules\Workflow\Models\WorkflowInstance;
use Modules\Workflow\Models\WorkflowStep;
use Modules\Workflow\Models\WorkflowTransition;

class DatabaseWorkflowInstanceStarter implements WorkflowInstanceStarter
{
    public function __construct(
        private readonly WorkflowAuditLogger $auditLogger,
        private readonly WorkflowSlaService $slaService,
    ) {}

    /**
     * @param  array<string, mixed>  $context
     */
    public function start(Workflow $workflow, User $requester, array $context = [], ?Model $subject = null, ?User $startedBy = null): WorkflowInstance
    {
        $instance = DB::transaction(function () use ($workflow, $requester, $context, $subject, $startedBy): WorkflowInstance {
            $workflow = $workflow->fresh(['steps.outgoingTransitions', 'transitions', 'automatedActions']);

            if ($workflow === null) {
                throw new WorkflowConfigurationException('Workflow definition could not be loaded.');
            }

            $initialStep = $workflow->steps()->where('is_initial', true)->orderBy('sort_order')->first();

            if (! $initialStep instanceof WorkflowStep) {
                throw new WorkflowConfigurationException('Workflow does not have an initial step.');
            }

            if ($subject instanceof ProvidesWorkflowContext) {
                $context = array_replace_recursive($subject->workflowContext(), $context);
            }

            $organizationId = data_get($subject, 'organization_id', $context['organization_id'] ?? $workflow->organization_id);

            $instance = WorkflowInstance::query()->create([
                'tenant_id' => $workflow->tenant_id,
                'organization_id' => $organizationId,
                'workflow_id' => $workflow->getKey(),
                'workflow_version' => (int) $workflow->version,
                'workflow_snapshot' => $this->snapshotWorkflow($workflow),
                'current_step_id' => $initialStep->getKey(),
                'requester_id' => $requester->getKey(),
                'started_by' => $startedBy?->getKey() ?? $requester->getKey(),
                'subject_type' => $subject ? $subject::class : null,
                'subject_id' => $subject?->getKey(),
                'subject_label' => $subject instanceof ProvidesWorkflowContext
                    ? $subject->workflowSubjectLabel()
                    : TypedValue::string(data_get($subject, 'name') ?? data_get($subject, 'title') ?? data_get($subject, 'code')),
                'context_data' => $context,
                'form_data' => [],
                'computed_data' => [],
                'status' => $initialStep->is_terminal ? WorkflowInstanceStatus::Completed : WorkflowInstanceStatus::Running,
                'current_assignees' => [],
                'started_at' => now(),
                'due_at' => $this->slaService->computeDueAt($initialStep, now()),
                'completed_at' => $initialStep->is_terminal ? now() : null,
            ]);

            $this->auditLogger->log($instance, WorkflowLogType::Started->value, [
                'actor_id' => $startedBy?->getKey() ?? $requester->getKey(),
                'action_taken' => 'start',
                'status_before' => null,
                'status_after' => $instance->status->value,
                'payload_before' => null,
                'payload_after' => [
                    'context_data' => $instance->context_data,
                    'form_data' => $instance->form_data,
                    'computed_data' => $instance->computed_data,
                ],
            ]);

            return $instance;
        });

        $fresh = $instance->fresh(['currentStep', 'assignments', 'logs']);

        if ($fresh === null) {
            throw new \RuntimeException('Workflow instance could not be loaded after start.');
        }

        WorkflowStarted::dispatch($fresh, $startedBy ?? $requester);

        return $fresh;
    }

    /**
     * @return array<string, mixed>
     */
    protected function snapshotWorkflow(Workflow $workflow): array
    {
        return [
            'workflow' => $workflow->only([
                'id',
                'tenant_id',
                'organization_id',
                'code',
                'name',
                'description',
                'module',
                'subject_type',
                'trigger_mode',
                'version',
                'status',
                'is_active',
                'published_at',
            ]),
            'steps' => $workflow->steps
                ->sortBy('sort_order')
                ->map(fn (WorkflowStep $step) => $step->only([
                    'id',
                    'uuid',
                    'code',
                    'name',
                    'description',
                    'step_type',
                    'gateway_type',
                    'quorum_strategy',
                    'quorum_value',
                    'assignee_type',
                    'assignee_value',
                    'assignee_config',
                    'form_schema',
                    'action_schema',
                    'sla_hours',
                    'allow_reassign',
                    'allow_delegate',
                    'is_initial',
                    'is_terminal',
                    'sort_order',
                ]))
                ->values()
                ->all(),
            'transitions' => $workflow->transitions
                ->map(fn (WorkflowTransition $transition) => $transition->only([
                    'id',
                    'from_step_id',
                    'to_step_id',
                    'action_name',
                    'rule_type',
                    'condition_rules',
                    'priority',
                    'is_default',
                    'transition_meta',
                ]))
                ->values()
                ->all(),
            'automated_actions' => $workflow->automatedActions
                ->sortBy('sort_order')
                ->map(fn (WorkflowAutomatedAction $action) => $action->only([
                    'id',
                    'step_id',
                    'trigger_event',
                    'action_type',
                    'name',
                    'config',
                    'is_active',
                    'sort_order',
                ]))
                ->values()
                ->all(),
        ];
    }
}
