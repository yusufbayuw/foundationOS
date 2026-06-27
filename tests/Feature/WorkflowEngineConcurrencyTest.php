<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use Modules\Core\Models\Organization;
use Modules\Core\Models\SubscriptionPlan;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\TenantRole;
use Modules\Core\Models\User;
use Modules\Core\Models\UserTenantRole;
use Modules\Workflow\Contracts\WorkflowDynamicAssigneeResolver;
use Modules\Workflow\Contracts\WorkflowEngine;
use Modules\Workflow\Contracts\WorkflowInstanceStarter;
use Modules\Workflow\Enums\WorkflowAssignmentStatus;
use Modules\Workflow\Events\WorkflowAdvanced;
use Modules\Workflow\Models\Workflow;
use Modules\Workflow\Models\WorkflowAssignment;
use Modules\Workflow\Models\WorkflowInstance;
use Modules\Workflow\Models\WorkflowStep;
use Modules\Workflow\Models\WorkflowTransition;
use Tests\TestCase;

class WorkflowEngineConcurrencyTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_parallel_approvers_only_complete_their_own_assignment_before_quorum(): void
    {
        [$tenant, $organization, $approverA, $approverB] = $this->makeContext();

        $workflow = $this->makeAllQuorumWorkflow($tenant, $organization, [$approverA, $approverB]);
        $instance = app(WorkflowInstanceStarter::class)->start($workflow, $approverA);

        app(WorkflowEngine::class)->advance($instance, 'approve', [], $approverA);

        $completedByA = WorkflowAssignment::query()
            ->where('workflow_instance_id', $instance->id)
            ->where('assigned_to_id', $approverA->id)
            ->first();

        $pendingForB = WorkflowAssignment::query()
            ->where('workflow_instance_id', $instance->id)
            ->where('assigned_to_id', $approverB->id)
            ->first();

        $this->assertSame(WorkflowAssignmentStatus::Completed->value, $completedByA->status->value);
        $this->assertSame('approve', $completedByA->outcome);
        $this->assertSame(WorkflowAssignmentStatus::Pending->value, $pendingForB->status->value);
        $this->assertSame('running', $instance->fresh()->status->value);
    }

    public function test_workflow_advanced_event_dispatches_after_engine_advance(): void
    {
        Event::fake([WorkflowAdvanced::class]);

        [$tenant, $organization, , $approver] = $this->makeContext();
        $workflow = $this->makeLinearWorkflow($tenant, $organization, $approver);
        $instance = app(WorkflowInstanceStarter::class)->start($workflow, $approver);

        app(WorkflowEngine::class)->advance(
            $instance,
            'approve',
            ['approval_note' => 'Approved in concurrency test.'],
            $approver,
        );

        Event::assertDispatched(WorkflowAdvanced::class);
    }

    /**
     * @return array{0: Tenant, 1: Organization, 2: User, 3: User}
     */
    protected function makeContext(): array
    {
        $plan = SubscriptionPlan::firstOrCreate(
            ['code' => 'wf-concurrency'],
            ['name' => 'WF Concurrency', 'included_modules' => ['core', 'workflow']],
        );

        $tenant = Tenant::create([
            'uuid' => (string) Str::uuid(),
            'code' => 'wf-conc-'.Str::random(4),
            'name' => 'WF Concurrency Tenant',
            'subscription_plan_id' => $plan->id,
        ]);

        $organization = Organization::create([
            'tenant_id' => $tenant->id,
            'code' => 'main',
            'name' => 'Main Org',
        ]);

        $requester = User::factory()->create();
        $approverA = User::factory()->create();
        $approverB = User::factory()->create();

        foreach ([$requester, $approverA, $approverB] as $user) {
            $role = TenantRole::create([
                'tenant_id' => $tenant->id,
                'name' => 'Member',
                'slug' => 'member-'.$user->id,
                'permissions' => [],
            ]);

            UserTenantRole::create([
                'user_id' => $user->id,
                'tenant_id' => $tenant->id,
                'organization_id' => $organization->id,
                'tenant_role_id' => $role->id,
                'assigned_by' => $user->id,
                'is_primary' => true,
            ]);
        }

        config()->set('workflow.allowed_assignee_resolvers', [
            ConcurrencyTestMultiUserResolver::class,
        ]);

        return [$tenant, $organization, $approverA, $approverB];
    }

    /**
     * @param  list<User>  $approvers
     */
    protected function makeAllQuorumWorkflow(Tenant $tenant, Organization $organization, array $approvers): Workflow
    {
        $workflow = Workflow::create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'code' => 'parallel-'.Str::random(4),
            'name' => 'Parallel',
            'module' => 'Workflow',
            'subject_type' => 'Modules\\Procurement\\Models\\PurchaseRequisition',
            'trigger_mode' => 'manual',
            'version' => 1,
            'status' => 'active',
            'is_active' => true,
            'published_at' => now(),
            'created_by' => $approvers[0]->id,
            'updated_by' => $approvers[0]->id,
        ]);

        $approval = WorkflowStep::create([
            'workflow_id' => $workflow->id,
            'uuid' => (string) Str::uuid(),
            'code' => 'parallel_approval',
            'name' => 'Parallel Approval',
            'step_type' => 'approval',
            'gateway_type' => 'parallel_join',
            'quorum_strategy' => 'all',
            'assignee_type' => 'resolver',
            'assignee_value' => ConcurrencyTestMultiUserResolver::class,
            'assignee_config' => [
                'resolver_class' => ConcurrencyTestMultiUserResolver::class,
                'user_ids' => array_map(fn (User $user) => $user->id, $approvers),
            ],
            'form_schema' => [],
            'action_schema' => [
                ['name' => 'approve', 'label' => 'Approve'],
            ],
            'is_initial' => true,
            'is_terminal' => false,
            'sort_order' => 1,
        ]);

        $completed = WorkflowStep::create([
            'workflow_id' => $workflow->id,
            'uuid' => (string) Str::uuid(),
            'code' => 'completed',
            'name' => 'Completed',
            'step_type' => 'end',
            'is_terminal' => true,
            'sort_order' => 2,
        ]);

        WorkflowTransition::create([
            'workflow_id' => $workflow->id,
            'from_step_id' => $approval->id,
            'to_step_id' => $completed->id,
            'action_name' => 'approve',
            'rule_type' => 'json_logic',
            'priority' => 10,
            'is_default' => true,
        ]);

        return $workflow;
    }

    protected function makeLinearWorkflow(Tenant $tenant, Organization $organization, User $approver): Workflow
    {
        $workflow = Workflow::create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'code' => 'linear-'.Str::random(4),
            'name' => 'Linear',
            'module' => 'Procurement',
            'subject_type' => 'Modules\\Procurement\\Models\\PurchaseRequisition',
            'trigger_mode' => 'manual',
            'version' => 1,
            'status' => 'active',
            'is_active' => true,
            'published_at' => now(),
            'created_by' => $approver->id,
            'updated_by' => $approver->id,
        ]);

        $approval = WorkflowStep::create([
            'workflow_id' => $workflow->id,
            'uuid' => (string) Str::uuid(),
            'code' => 'manager_approval',
            'name' => 'Manager Approval',
            'step_type' => 'approval',
            'assignee_type' => 'user',
            'assignee_value' => (string) $approver->id,
            'form_schema' => [
                [
                    'name' => 'approval_note',
                    'label' => 'Approval Note',
                    'type' => 'textarea',
                    'required' => true,
                    'validation' => ['min:10'],
                ],
            ],
            'action_schema' => [
                ['name' => 'approve', 'label' => 'Approve'],
            ],
            'is_initial' => true,
            'is_terminal' => false,
            'sort_order' => 1,
        ]);

        $completed = WorkflowStep::create([
            'workflow_id' => $workflow->id,
            'uuid' => (string) Str::uuid(),
            'code' => 'completed',
            'name' => 'Completed',
            'step_type' => 'end',
            'is_terminal' => true,
            'sort_order' => 2,
        ]);

        WorkflowTransition::create([
            'workflow_id' => $workflow->id,
            'from_step_id' => $approval->id,
            'to_step_id' => $completed->id,
            'action_name' => 'approve',
            'rule_type' => 'json_logic',
            'priority' => 10,
            'is_default' => true,
        ]);

        return $workflow;
    }
}

class ConcurrencyTestMultiUserResolver implements WorkflowDynamicAssigneeResolver
{
    public function resolve(WorkflowInstance $instance, WorkflowStep $step): Collection
    {
        $ids = (array) data_get($step->assignee_config, 'user_ids', []);

        return User::query()
            ->whereIn('id', $ids)
            ->get();
    }
}
