<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
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
use Modules\Workflow\Models\Workflow;
use Modules\Workflow\Models\WorkflowAssignment;
use Modules\Workflow\Models\WorkflowInstance;
use Modules\Workflow\Models\WorkflowStep;
use Modules\Workflow\Models\WorkflowTransition;
use Tests\TestCase;

class WorkflowParallelGatewayTest extends TestCase
{
    use RefreshDatabase;

    public function test_majority_quorum_advances_after_two_of_three_approve(): void
    {
        [$tenant, $organization, $manager1, $manager2, $manager3] = $this->makeParallelContext();

        $workflow = $this->makeMajorityWorkflow($tenant, $organization, [$manager1, $manager2, $manager3]);
        $instance = app(WorkflowInstanceStarter::class)->start($workflow, $manager1);

        $this->assertSame(3, $instance->assignments()->count());

        // Manager 1 approves — quorum not yet reached.
        $afterFirst = app(WorkflowEngine::class)->advance($instance, 'approve', [], $manager1);
        $this->assertSame('running', $afterFirst->status->value);
        $this->assertSame($afterFirst->current_step_id, $instance->current_step_id);

        // Manager 3 rejects.
        app(WorkflowEngine::class)->advance($instance, 'reject', [], $manager3);

        // Manager 2 approves — now 2 approvals, majority (>=2 of 3) reached.
        $afterThird = app(WorkflowEngine::class)->advance($instance, 'approve', [], $manager2);

        $this->assertSame('completed', $afterThird->status->value);

        $this->assertSame(2, WorkflowAssignment::query()
            ->where('workflow_instance_id', $instance->id)
            ->where('outcome', 'approve')
            ->count());
        $this->assertSame(1, WorkflowAssignment::query()
            ->where('workflow_instance_id', $instance->id)
            ->where('outcome', 'reject')
            ->count());
    }

    public function test_majority_outcome_flips_to_reject_when_two_of_three_reject(): void
    {
        [$tenant, $organization, $m1, $m2, $m3] = $this->makeParallelContext('reject-flow');
        $workflow = $this->makeMajorityWorkflow($tenant, $organization, [$m1, $m2, $m3], includeRejectTransition: true);
        $instance = app(WorkflowInstanceStarter::class)->start($workflow, $m1);

        app(WorkflowEngine::class)->advance($instance, 'reject', [], $m1);
        $afterSecond = app(WorkflowEngine::class)->advance($instance, 'reject', [], $m2);

        $this->assertSame('rejected', $afterSecond->status->value);
        // Manager 3's pending assignment should be cancelled, since the
        // verdict is already final.
        $this->assertDatabaseHas('workflow_assignments', [
            'workflow_instance_id' => $instance->id,
            'assigned_to_id' => $m3->id,
            'status' => 'cancelled',
        ]);
    }

    public function test_count_quorum_requires_n_approvals(): void
    {
        [$tenant, $organization, $m1, $m2, $m3] = $this->makeParallelContext('count-flow');
        $workflow = $this->makeMajorityWorkflow(
            $tenant, $organization, [$m1, $m2, $m3],
            quorumStrategy: 'count',
            quorumValue: 1,
        );

        $instance = app(WorkflowInstanceStarter::class)->start($workflow, $m1);
        $after = app(WorkflowEngine::class)->advance($instance, 'approve', [], $m1);

        $this->assertSame('completed', $after->status->value, 'a single approval is enough when quorum=count(1)');
    }

    protected function makeMajorityWorkflow(
        Tenant $tenant,
        Organization $organization,
        array $managers,
        string $quorumStrategy = 'majority',
        ?int $quorumValue = null,
        bool $includeRejectTransition = true,
    ): Workflow {
        $workflow = Workflow::create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'code' => 'parallel-approval-'.Str::random(4),
            'name' => 'Parallel Approval',
            'module' => 'Workflow',
            'subject_type' => 'Modules\\Procurement\\Models\\PurchaseRequisition',
            'trigger_mode' => 'manual',
            'version' => 1,
            'status' => 'active',
            'is_active' => true,
            'published_at' => now(),
            'created_by' => $managers[0]->id,
            'updated_by' => $managers[0]->id,
        ]);

        $approval = WorkflowStep::create([
            'workflow_id' => $workflow->id,
            'uuid' => (string) Str::uuid(),
            'code' => 'parallel_approval',
            'name' => 'Parallel Approval',
            'step_type' => 'approval',
            'gateway_type' => 'parallel_join',
            'quorum_strategy' => $quorumStrategy,
            'quorum_value' => $quorumValue,
            'assignee_type' => 'resolver',
            'assignee_value' => InlineMultiUserResolver::class,
            'assignee_config' => [
                'resolver_class' => InlineMultiUserResolver::class,
                'user_ids' => array_map(fn ($u) => $u->id, $managers),
            ],
            'form_schema' => [],
            'action_schema' => [
                ['name' => 'approve', 'label' => 'Approve'],
                ['name' => 'reject', 'label' => 'Reject'],
            ],
            'sla_hours' => 24,
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
            'is_initial' => false,
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

        if ($includeRejectTransition) {
            $rejected = WorkflowStep::create([
                'workflow_id' => $workflow->id,
                'uuid' => (string) Str::uuid(),
                'code' => 'rejected',
                'name' => 'Rejected',
                'step_type' => 'end',
                'is_initial' => false,
                'is_terminal' => true,
                'sort_order' => 3,
            ]);

            WorkflowTransition::create([
                'workflow_id' => $workflow->id,
                'from_step_id' => $approval->id,
                'to_step_id' => $rejected->id,
                'action_name' => 'reject',
                'rule_type' => 'json_logic',
                'priority' => 20,
                'is_default' => false,
            ]);
        }

        return $workflow;
    }

    /**
     * @return array{0:Tenant,1:Organization,2:User,3:User,4:User}
     */
    protected function makeParallelContext(string $suffix = 'a'): array
    {
        $plan = SubscriptionPlan::firstOrCreate(
            ['code' => 'parallel-test'],
            ['name' => 'Parallel Test', 'included_modules' => ['core', 'workflow']],
        );

        $rand = Str::random(4);

        $tenant = Tenant::create([
            'uuid' => (string) Str::uuid(),
            'code' => "parallel-tenant-{$suffix}-{$rand}",
            'name' => "Parallel Tenant {$suffix}",
            'subscription_plan_id' => $plan->id,
        ]);

        $organization = Organization::create([
            'tenant_id' => $tenant->id,
            'code' => "parallel-org-{$suffix}-{$rand}",
            'name' => 'Parallel Org',
        ]);

        $role = TenantRole::create([
            'tenant_id' => $tenant->id,
            'name' => 'Approver',
            'slug' => "approver-{$suffix}-{$rand}",
            'permissions' => ['*'],
            'is_default' => true,
        ]);

        $managers = [];
        foreach ([1, 2, 3] as $i) {
            $manager = User::create([
                'name' => "Manager {$i} {$suffix}",
                'email' => "manager-{$i}-{$suffix}-{$rand}@example.com",
                'password' => 'password',
            ]);

            UserTenantRole::create([
                'user_id' => $manager->id,
                'tenant_id' => $tenant->id,
                'organization_id' => $organization->id,
                'tenant_role_id' => $role->id,
                'assigned_by' => $manager->id,
                'is_primary' => $i === 1,
            ]);

            $managers[] = $manager;
        }

        config()->set('workflow.allowed_assignee_resolvers', [
            InlineMultiUserResolver::class,
        ]);

        return [$tenant, $organization, $managers[0], $managers[1], $managers[2]];
    }
}

class InlineMultiUserResolver implements WorkflowDynamicAssigneeResolver
{
    public function resolve(WorkflowInstance $instance, WorkflowStep $step): Collection
    {
        $ids = (array) data_get($step->assignee_config, 'user_ids', []);

        return User::query()
            ->whereIn('id', $ids)
            ->get();
    }
}
