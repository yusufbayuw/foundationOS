<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Modules\Core\Models\Organization;
use Modules\Core\Models\SubscriptionPlan;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\TenantRole;
use Modules\Core\Models\User;
use Modules\Core\Models\UserTenantRole;
use Modules\Workflow\Contracts\WorkflowEngine;
use Modules\Workflow\Contracts\WorkflowInstanceStarter;
use Modules\Workflow\Contracts\WorkflowResolver;
use Modules\Workflow\Contracts\WorkflowSlaService;
use Modules\Workflow\Models\Workflow;
use Modules\Workflow\Models\WorkflowAssignment;
use Modules\Workflow\Models\WorkflowStep;
use Modules\Workflow\Models\WorkflowTransition;
use Tests\TestCase;

class WorkflowEngineTest extends TestCase
{
    use RefreshDatabase;

    public function test_workflow_resolver_prefers_organization_specific_definition(): void
    {
        [$tenant, $organizationA, , $user] = $this->makeTenantContext();

        $tenantWide = Workflow::create([
            'tenant_id' => $tenant->id,
            'organization_id' => null,
            'code' => 'approval-pr',
            'name' => 'Tenant Workflow',
            'subject_type' => 'Modules\\Procurement\\Models\\PurchaseRequisition',
            'trigger_mode' => 'manual',
            'version' => 1,
            'status' => 'active',
            'is_active' => true,
            'created_by' => $user->id,
        ]);

        $organizationSpecific = Workflow::create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organizationA->id,
            'code' => 'approval-pr',
            'name' => 'Org Workflow',
            'subject_type' => 'Modules\\Procurement\\Models\\PurchaseRequisition',
            'trigger_mode' => 'manual',
            'version' => 2,
            'status' => 'active',
            'is_active' => true,
            'created_by' => $user->id,
        ]);

        $resolved = app(WorkflowResolver::class)->resolveForSubject(
            'Modules\\Procurement\\Models\\PurchaseRequisition',
            null,
            (int) $tenant->id,
            (int) $organizationA->id,
        );

        $this->assertTrue($resolved->is($organizationSpecific));
        $this->assertFalse($resolved->is($tenantWide));
    }

    public function test_instance_starter_creates_snapshot_and_assignments(): void
    {
        [$tenant, $organizationA, , $user] = $this->makeTenantContext();

        $workflow = $this->makeWorkflow($tenant, $organizationA, $user);

        $instance = app(WorkflowInstanceStarter::class)->start(
            $workflow,
            $user,
            ['requested_total' => 1500000],
        );

        $instance->refresh();

        $this->assertSame('running', $instance->status->value);
        $this->assertSame('Request Approval Flow', $instance->workflow_snapshot['workflow']['name']);
        $this->assertNotNull($instance->due_at);
        $this->assertDatabaseHas('workflow_assignments', [
            'workflow_instance_id' => $instance->id,
            'assigned_to_id' => $user->id,
            'status' => 'pending',
        ]);
        $this->assertDatabaseHas('workflow_instance_logs', [
            'workflow_instance_id' => $instance->id,
            'log_type' => 'started',
        ]);
    }

    public function test_engine_advances_to_terminal_step_and_completes_assignment(): void
    {
        [$tenant, $organizationA, , $user] = $this->makeTenantContext();

        $workflow = $this->makeWorkflow($tenant, $organizationA, $user);
        $instance = app(WorkflowInstanceStarter::class)->start($workflow, $user, ['requested_total' => 2500000]);

        $result = app(WorkflowEngine::class)->advance(
            $instance,
            'approve',
            ['approval_note' => 'Looks good and aligned with policy.'],
            $user,
            'Approved',
        );

        $this->assertSame('completed', $result->status->value);
        $this->assertNotNull($result->completed_at);
        $this->assertDatabaseHas('workflow_assignments', [
            'workflow_instance_id' => $instance->id,
            'assigned_to_id' => $user->id,
            'status' => 'completed',
        ]);
        $this->assertDatabaseHas('workflow_instance_logs', [
            'workflow_instance_id' => $instance->id,
            'log_type' => 'advanced',
            'action_taken' => 'approve',
        ]);
    }

    public function test_engine_denies_advance_without_active_assignment(): void
    {
        [$tenant, $organizationA, , $user] = $this->makeTenantContext();

        $outsider = User::create([
            'name' => 'Outsider',
            'email' => 'outsider@example.com',
            'password' => 'password',
        ]);

        $workflow = $this->makeWorkflow($tenant, $organizationA, $user);
        $instance = app(WorkflowInstanceStarter::class)->start($workflow, $user);

        $this->expectException(\Modules\Workflow\Exceptions\WorkflowAuthorizationException::class);

        app(WorkflowEngine::class)->advance(
            $instance,
            'approve',
            ['approval_note' => 'Not allowed.'],
            $outsider,
        );
    }

    public function test_engine_can_reassign_pending_assignment(): void
    {
        [$tenant, $organizationA, , $user] = $this->makeTenantContext();

        $delegate = User::create([
            'name' => 'Delegate Approver',
            'email' => 'delegate-approver@example.com',
            'password' => 'password',
        ]);

        $workflow = $this->makeWorkflow($tenant, $organizationA, $user);
        $instance = app(WorkflowInstanceStarter::class)->start($workflow, $user);

        $pendingAssignment = WorkflowAssignment::query()
            ->where('workflow_instance_id', $instance->id)
            ->where('status', 'pending')
            ->firstOrFail();

        $newAssignment = app(WorkflowEngine::class)->reassign(
            $pendingAssignment,
            $user,
            $delegate,
            'Delegate for coverage',
        );

        $this->assertSame($delegate->id, $newAssignment->assigned_to_id);
        $this->assertSame('pending', $newAssignment->status->value);
        $this->assertDatabaseHas('workflow_assignments', [
            'id' => $pendingAssignment->id,
            'status' => 'cancelled',
        ]);
    }

    public function test_workflow_snapshot_remains_stable_after_definition_changes(): void
    {
        [$tenant, $organizationA, , $user] = $this->makeTenantContext();

        $workflow = $this->makeWorkflow($tenant, $organizationA, $user);
        $instance = app(WorkflowInstanceStarter::class)->start($workflow, $user);

        $workflow->update([
            'name' => 'Request Approval Flow Updated',
            'description' => 'Updated after instance started.',
        ]);

        $instance->refresh();

        $this->assertSame('Request Approval Flow', $instance->workflow_snapshot['workflow']['name']);
        $this->assertNotSame($workflow->fresh()->name, $instance->workflow_snapshot['workflow']['name']);
    }

    public function test_sla_service_marks_breached_and_logs_it(): void
    {
        [$tenant, $organizationA, , $user] = $this->makeTenantContext();

        $workflow = $this->makeWorkflow($tenant, $organizationA, $user);
        $instance = app(WorkflowInstanceStarter::class)->start($workflow, $user);

        app(WorkflowSlaService::class)->markBreached($instance->fresh());

        $this->assertDatabaseHas('workflow_instance_logs', [
            'workflow_instance_id' => $instance->id,
            'log_type' => 'sla_breached',
        ]);
    }

    public function test_sla_breach_logging_is_idempotent(): void
    {
        [$tenant, $organizationA, , $user] = $this->makeTenantContext();

        $workflow = $this->makeWorkflow($tenant, $organizationA, $user);
        $instance = app(WorkflowInstanceStarter::class)->start($workflow, $user);

        $sla = app(WorkflowSlaService::class);

        $sla->markBreached($instance->fresh());
        $sla->markBreached($instance->fresh());

        $this->assertSame(
            1,
            $instance->logs()->where('log_type', 'sla_breached')->count(),
        );
    }

    protected function makeWorkflow(Tenant $tenant, Organization $organization, User $user): Workflow
    {
        $workflow = Workflow::create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'code' => 'request-approval',
            'name' => 'Request Approval Flow',
            'module' => 'Procurement',
            'subject_type' => 'Modules\\Procurement\\Models\\PurchaseRequisition',
            'trigger_mode' => 'manual',
            'version' => 1,
            'status' => 'active',
            'is_active' => true,
            'published_at' => now(),
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        $start = WorkflowStep::create([
            'workflow_id' => $workflow->id,
            'uuid' => (string) Str::uuid(),
            'code' => 'manager_approval',
            'name' => 'Manager Approval',
            'step_type' => 'approval',
            'assignee_type' => 'user',
            'assignee_value' => (string) $user->id,
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
            'sla_hours' => 4,
            'is_initial' => true,
            'is_terminal' => false,
            'sort_order' => 1,
        ]);

        $end = WorkflowStep::create([
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
            'from_step_id' => $start->id,
            'to_step_id' => $end->id,
            'action_name' => 'approve',
            'rule_type' => 'json_logic',
            'condition_rules' => [
                '>=' => [
                    ['var' => 'requested_total'],
                    1000000,
                ],
            ],
            'priority' => 10,
            'is_default' => true,
        ]);

        return $workflow;
    }

    protected function makeTenantContext(): array
    {
        $plan = SubscriptionPlan::create([
            'code' => 'workflow-plan',
            'name' => 'Workflow Plan',
            'included_modules' => ['core', 'workflow'],
        ]);

        $user = User::create([
            'name' => 'Workflow Admin',
            'email' => 'workflow-admin@example.com',
            'password' => 'password',
        ]);

        $tenant = Tenant::create([
            'uuid' => (string) Str::uuid(),
            'code' => 'tenant-workflow',
            'name' => 'Tenant Workflow',
            'subscription_plan_id' => $plan->id,
            'created_by' => $user->id,
        ]);

        $organizationA = Organization::create([
            'tenant_id' => $tenant->id,
            'code' => 'org-workflow-a',
            'name' => 'Workflow Organization A',
        ]);

        $organizationB = Organization::create([
            'tenant_id' => $tenant->id,
            'code' => 'org-workflow-b',
            'name' => 'Workflow Organization B',
        ]);

        $tenantRole = TenantRole::create([
            'tenant_id' => $tenant->id,
            'name' => 'Workflow Admin',
            'slug' => 'workflow-admin',
            'permissions' => ['*'],
            'is_default' => true,
        ]);

        UserTenantRole::create([
            'user_id' => $user->id,
            'tenant_id' => $tenant->id,
            'organization_id' => $organizationA->id,
            'tenant_role_id' => $tenantRole->id,
            'assigned_by' => $user->id,
            'is_primary' => true,
        ]);

        return [$tenant, $organizationA, $organizationB, $user];
    }
}
