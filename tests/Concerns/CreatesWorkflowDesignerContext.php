<?php

namespace Tests\Concerns;

use App\Support\CurrentTenant;
use Illuminate\Support\Str;
use Modules\Core\Models\SubscriptionPlan;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\User;
use Modules\Workflow\Enums\WorkflowDefinitionStatus;
use Modules\Workflow\Models\Workflow;
use Modules\Workflow\Models\WorkflowStep;
use Modules\Workflow\Models\WorkflowTransition;

trait CreatesWorkflowDesignerContext
{
    protected Tenant $designerTenant;

    protected User $designerUser;

    protected function setUpWorkflowDesignerContext(): void
    {
        $plan = SubscriptionPlan::create([
            'code' => 'designer-plan-'.Str::random(4),
            'name' => 'Designer Plan',
            'included_modules' => ['core', 'workflow'],
        ]);

        $this->designerUser = User::create([
            'name' => 'Designer User',
            'email' => 'designer-'.Str::random(6).'@example.com',
            'password' => 'password',
        ])->promoteToGlobalSuperAdmin();

        $this->designerTenant = Tenant::create([
            'uuid' => (string) Str::uuid(),
            'code' => 'designer-'.Str::random(4),
            'name' => 'Designer Tenant',
            'subscription_plan_id' => $plan->id,
            'created_by' => $this->designerUser->id,
        ]);

        app(CurrentTenant::class)->set($this->designerTenant);
        $this->actingAs($this->designerUser);
    }

    protected function tearDownWorkflowDesignerContext(): void
    {
        app(CurrentTenant::class)->forget();
    }

    /**
     * @return array<string, mixed>
     */
    protected function designerStepPayload(
        string $uuid,
        string $code,
        string $name,
        string $type = 'task',
        string $gatewayType = 'none',
        bool $initial = false,
        bool $terminal = false,
        ?string $quorumStrategy = null,
        ?int $quorumValue = null,
    ): array {
        return [
            'uuid' => $uuid,
            'code' => $code,
            'name' => $name,
            'step_type' => $type,
            'gateway_type' => $gatewayType,
            'assignee_type' => 'user',
            'assignee_value' => '',
            'assignee_config' => [],
            'form_schema' => [],
            'quorum_strategy' => $quorumStrategy,
            'quorum_value' => $quorumValue,
            'sla_hours' => null,
            'description' => '',
            'is_initial' => $initial,
            'is_terminal' => $terminal,
            'sort_order' => 0,
            'canvas_position' => null,
        ];
    }

    protected function makeDesignerWorkflow(string $code = 'test_designer_wf', string $status = 'draft'): Workflow
    {
        $workflow = Workflow::create([
            'tenant_id' => $this->designerTenant->id,
            'code' => $code,
            'name' => 'Designer Test Workflow',
            'version' => 1,
            'status' => $status === 'active' ? WorkflowDefinitionStatus::Active : WorkflowDefinitionStatus::Draft,
            'is_active' => $status === 'active',
        ]);

        $start = WorkflowStep::create([
            'workflow_id' => $workflow->id,
            'uuid' => (string) Str::uuid(),
            'code' => 'start',
            'name' => 'Start',
            'step_type' => 'start',
            'gateway_type' => 'none',
            'assignee_type' => 'user',
            'is_initial' => true,
            'is_terminal' => false,
            'sort_order' => 1,
        ]);

        $end = WorkflowStep::create([
            'workflow_id' => $workflow->id,
            'uuid' => (string) Str::uuid(),
            'code' => 'end',
            'name' => 'End',
            'step_type' => 'end',
            'gateway_type' => 'none',
            'assignee_type' => 'user',
            'is_initial' => false,
            'is_terminal' => true,
            'sort_order' => 2,
        ]);

        WorkflowTransition::create([
            'workflow_id' => $workflow->id,
            'from_step_id' => $start->id,
            'to_step_id' => $end->id,
            'action_name' => 'finish',
            'priority' => 1,
            'is_default' => true,
        ]);

        return $workflow->fresh(['steps', 'transitions']);
    }
}
