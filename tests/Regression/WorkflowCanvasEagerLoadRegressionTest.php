<?php

namespace Tests\Regression;

use App\Support\CurrentTenant;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Modules\Core\Models\Organization;
use Modules\Core\Models\SubscriptionPlan;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\User;
use Modules\Workflow\Enums\WorkflowDefinitionStatus;
use Modules\Workflow\Livewire\WorkflowCanvas;
use Modules\Workflow\Models\Workflow;
use Modules\Workflow\Models\WorkflowStep;
use Modules\Workflow\Models\WorkflowTransition;
use Tests\TestCase;

/**
 * Regression: WorkflowCanvas eager-loads steps and transitions without type errors.
 */
class WorkflowCanvasEagerLoadRegressionTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_canvas_loads_workflow_with_ordered_steps_and_transitions(): void
    {
        [$tenant, $user, $workflow] = $this->seedWorkflow();

        app(CurrentTenant::class)->set($tenant);
        $this->actingAs($user);

        Livewire::test(WorkflowCanvas::class, ['workflowId' => $workflow->id])
            ->assertSet('workflowCode', $workflow->code)
            ->assertSet('workflowName', $workflow->name)
            ->assertCount('steps', 2)
            ->assertCount('transitions', 1);
    }

    /**
     * @return array{0: Tenant, 1: User, 2: Workflow}
     */
    private function seedWorkflow(): array
    {
        $plan = SubscriptionPlan::create([
            'code' => 'wf-regression',
            'name' => 'Workflow Regression',
            'included_modules' => ['core', 'workflow'],
        ]);

        $tenant = Tenant::create([
            'uuid' => (string) Str::uuid(),
            'code' => 'wf-regression-tenant',
            'name' => 'Workflow Regression Tenant',
            'subscription_plan_id' => $plan->id,
        ]);

        Organization::create([
            'tenant_id' => $tenant->id,
            'code' => 'main',
            'name' => 'Main',
            'is_active' => true,
            'is_main' => true,
        ]);

        $user = User::factory()->create();

        $workflow = Workflow::create([
            'tenant_id' => $tenant->id,
            'code' => 'regression-flow',
            'name' => 'Regression Flow',
            'status' => WorkflowDefinitionStatus::Draft,
            'version' => 1,
            'is_active' => false,
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
            'sort_order' => 1,
        ]);

        $review = WorkflowStep::create([
            'workflow_id' => $workflow->id,
            'uuid' => (string) Str::uuid(),
            'code' => 'review',
            'name' => 'Review',
            'step_type' => 'task',
            'gateway_type' => 'none',
            'assignee_type' => 'user',
            'sort_order' => 2,
        ]);

        WorkflowTransition::create([
            'workflow_id' => $workflow->id,
            'from_step_id' => $start->id,
            'to_step_id' => $review->id,
            'action_name' => 'submit',
            'priority' => 1,
            'is_default' => true,
        ]);

        return [$tenant, $user, $workflow];
    }
}
