<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Str;
use Modules\Core\Models\Organization;
use Modules\Core\Models\SubscriptionPlan;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\TenantRole;
use Modules\Core\Models\User;
use Modules\Core\Models\UserTenantRole;
use Modules\Workflow\Contracts\WorkflowEngine;
use Modules\Workflow\Contracts\WorkflowInstanceStarter;
use Modules\Workflow\Models\Workflow;
use Modules\Workflow\Models\WorkflowStep;
use Modules\Workflow\Models\WorkflowTransition;
use Tests\TestCase;

/**
 * Verifies a parallel-join step (quorum=all) only advances after every
 * branch (here: every assignee) has voted.
 */
class WorkflowParallelJoinTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_join_step_waits_for_all_branches_before_advancing(): void
    {
        config()->set('workflow.allowed_assignee_resolvers', [
            InlineMultiUserResolver::class,
        ]);

        [$tenant, $organization, $u1, $u2, $u3] = $this->makeContext();
        $workflow = $this->buildJoinWorkflow($tenant, $organization, [$u1, $u2, $u3]);

        $instance = app(WorkflowInstanceStarter::class)->start($workflow, $u1);

        // First vote — still waiting on two.
        $after1 = app(WorkflowEngine::class)->advance($instance, 'approve', [], $u1);
        $this->assertSame('running', $after1->status->value);

        // Second vote — still waiting on one.
        $after2 = app(WorkflowEngine::class)->advance($instance, 'approve', [], $u2);
        $this->assertSame('running', $after2->status->value);

        // Final vote — quorum=all satisfied, instance advances.
        $after3 = app(WorkflowEngine::class)->advance($instance, 'approve', [], $u3);
        $this->assertSame('completed', $after3->status->value);
    }

    public function test_join_rejects_when_quorum_all_and_one_branch_rejects(): void
    {
        config()->set('workflow.allowed_assignee_resolvers', [
            InlineMultiUserResolver::class,
        ]);

        [$tenant, $organization, $u1, $u2, $u3] = $this->makeContext('reject');
        $workflow = $this->buildJoinWorkflow($tenant, $organization, [$u1, $u2, $u3]);
        $instance = app(WorkflowInstanceStarter::class)->start($workflow, $u1);

        app(WorkflowEngine::class)->advance($instance, 'approve', [], $u1);
        $after2 = app(WorkflowEngine::class)->advance($instance, 'reject', [], $u2);

        // With quorum=all, a single rejection makes the all-approve threshold
        // unreachable: coordinator finalizes as rejected immediately.
        $this->assertSame('rejected', $after2->status->value);
    }

    protected function buildJoinWorkflow(Tenant $tenant, Organization $organization, array $approvers): Workflow
    {
        $workflow = Workflow::create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'code' => 'join-'.Str::random(4),
            'name' => 'Parallel Join',
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

        $join = WorkflowStep::create([
            'workflow_id' => $workflow->id,
            'uuid' => (string) Str::uuid(),
            'code' => 'parallel_join',
            'name' => 'Parallel Join (all approvers)',
            'step_type' => 'approval',
            'gateway_type' => 'parallel_join',
            'quorum_strategy' => 'all',
            'assignee_type' => 'resolver',
            'assignee_value' => InlineMultiUserResolver::class,
            'assignee_config' => [
                'resolver_class' => InlineMultiUserResolver::class,
                'user_ids' => array_map(fn ($u) => $u->id, $approvers),
            ],
            'action_schema' => [
                ['name' => 'approve', 'label' => 'Approve'],
                ['name' => 'reject', 'label' => 'Reject'],
            ],
            'is_initial' => true,
            'sort_order' => 1,
        ]);

        $approved = WorkflowStep::create([
            'workflow_id' => $workflow->id,
            'uuid' => (string) Str::uuid(),
            'code' => 'approved',
            'name' => 'Approved',
            'step_type' => 'end',
            'is_terminal' => true,
            'sort_order' => 2,
        ]);

        $rejected = WorkflowStep::create([
            'workflow_id' => $workflow->id,
            'uuid' => (string) Str::uuid(),
            'code' => 'rejected',
            'name' => 'Rejected',
            'step_type' => 'end',
            'is_terminal' => true,
            'sort_order' => 3,
        ]);

        WorkflowTransition::create([
            'workflow_id' => $workflow->id,
            'from_step_id' => $join->id,
            'to_step_id' => $approved->id,
            'action_name' => 'approve',
            'rule_type' => 'json_logic',
            'priority' => 10,
            'is_default' => true,
        ]);

        WorkflowTransition::create([
            'workflow_id' => $workflow->id,
            'from_step_id' => $join->id,
            'to_step_id' => $rejected->id,
            'action_name' => 'reject',
            'rule_type' => 'json_logic',
            'priority' => 20,
            'is_default' => false,
        ]);

        return $workflow;
    }

    /**
     * @return array{0:Tenant,1:Organization,2:User,3:User,4:User}
     */
    protected function makeContext(string $suffix = 'join'): array
    {
        $plan = SubscriptionPlan::firstOrCreate(
            ['code' => 'parallel-join-test'],
            ['name' => 'Parallel Join Test', 'included_modules' => ['core', 'workflow']],
        );

        $rand = Str::random(4);

        $tenant = Tenant::create([
            'uuid' => (string) Str::uuid(),
            'code' => "join-tenant-{$suffix}-{$rand}",
            'name' => 'Join Tenant',
            'subscription_plan_id' => $plan->id,
        ]);

        $organization = Organization::create([
            'tenant_id' => $tenant->id,
            'code' => "join-org-{$suffix}-{$rand}",
            'name' => 'Join Organization',
        ]);

        $role = TenantRole::create([
            'tenant_id' => $tenant->id,
            'name' => 'Approver',
            'slug' => "approver-join-{$suffix}-{$rand}",
            'permissions' => ['*'],
            'is_default' => true,
        ]);

        $users = [];
        foreach ([1, 2, 3] as $i) {
            $u = User::create([
                'name' => "Join User {$i}",
                'email' => "join-{$i}-{$suffix}-{$rand}@example.com",
                'password' => 'password',
            ]);

            UserTenantRole::create([
                'user_id' => $u->id,
                'tenant_id' => $tenant->id,
                'organization_id' => $organization->id,
                'tenant_role_id' => $role->id,
                'assigned_by' => $u->id,
                'is_primary' => $i === 1,
            ]);

            $users[] = $u;
        }

        return [$tenant, $organization, $users[0], $users[1], $users[2]];
    }
}
