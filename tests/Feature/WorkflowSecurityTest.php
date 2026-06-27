<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Modules\Core\Models\Organization;
use Modules\Core\Models\SubscriptionPlan;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\TenantRole;
use Modules\Core\Models\User;
use Modules\Core\Models\UserTenantRole;
use Modules\Workflow\Contracts\WorkflowEngine;
use Modules\Workflow\Contracts\WorkflowInstanceStarter;
use Modules\Workflow\Enums\WorkflowAutomationActionType;
use Modules\Workflow\Exceptions\WorkflowAuthorizationException;
use Modules\Workflow\Models\Workflow;
use Modules\Workflow\Models\WorkflowInstance;
use Modules\Workflow\Models\WorkflowStep;
use Modules\Workflow\Models\WorkflowTransition;
use Modules\Workflow\Notifications\InternalWorkflowNotification;
use Modules\Workflow\Services\WorkflowAutomatedActionRunner;
use Modules\Workflow\Support\JsonLogicEvaluator;
use Tests\Support\AllowlistedWorkflowAutomationTestJob;
use Tests\TestCase;

class WorkflowSecurityTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        AllowlistedWorkflowAutomationTestJob::reset();
    }

    public function test_engine_rejects_advance_action_not_allowed_for_current_step(): void
    {
        [$tenant, $organization, $user] = $this->makeTenantContext();
        $workflow = $this->makeWorkflow($tenant, $organization, $user);
        $instance = app(WorkflowInstanceStarter::class)->start($workflow, $user);

        $this->expectException(WorkflowAuthorizationException::class);
        $this->expectExceptionMessage('Action [escalate] is not permitted');

        app(WorkflowEngine::class)->advance(
            $instance->fresh(),
            'escalate',
            ['approval_note' => 'Attempting an unauthorized action name.'],
            $user,
        );
    }

    public function test_automation_dispatch_job_is_blocked_when_not_allowlisted(): void
    {
        Bus::fake([AllowlistedWorkflowAutomationTestJob::class]);

        [$tenant, $organization, $user] = $this->makeTenantContext();
        $instance = $this->makeInstanceWithAutomation($tenant, $organization, $user, [
            'job_class' => AllowlistedWorkflowAutomationTestJob::class,
        ]);

        config()->set('workflow.allowed_automation_jobs', []);

        app(WorkflowAutomatedActionRunner::class)->run($instance, 'started', [
            'trigger_event' => 'started',
        ]);

        Bus::assertNotDispatched(AllowlistedWorkflowAutomationTestJob::class);
        $this->assertDatabaseHas('audit_logs', [
            'tenant_id' => $tenant->id,
            'action' => 'workflow_automation_skipped',
            'status' => 'failed',
        ]);
    }

    public function test_automation_dispatch_job_runs_only_for_allowlisted_classes(): void
    {
        Bus::fake([AllowlistedWorkflowAutomationTestJob::class]);

        [$tenant, $organization, $user] = $this->makeTenantContext();
        $instance = $this->makeInstanceWithAutomation($tenant, $organization, $user, [
            'job_class' => AllowlistedWorkflowAutomationTestJob::class,
            'payload' => ['source' => 'security-test'],
        ]);

        config()->set('workflow.allowed_automation_jobs', [
            AllowlistedWorkflowAutomationTestJob::class,
        ]);

        app(WorkflowAutomatedActionRunner::class)->run($instance, 'started', [
            'trigger_event' => 'started',
        ]);

        Bus::assertDispatched(AllowlistedWorkflowAutomationTestJob::class);
    }

    public function test_automation_notification_only_targets_tenant_members(): void
    {
        Notification::fake();

        [$tenant, $organization, $user] = $this->makeTenantContext();
        $foreignUser = User::factory()->create();

        $instance = $this->makeInstanceWithAutomation($tenant, $organization, $user, [
            'user_ids' => [$user->id, $foreignUser->id],
            'title' => 'Security scoped notification',
            'body' => 'Only tenant members should receive this.',
        ], WorkflowAutomationActionType::InternalNotification->value);

        app(WorkflowAutomatedActionRunner::class)->run($instance, 'started', [
            'trigger_event' => 'started',
        ]);

        $tenantMember = User::query()->find($user->id);
        $foreignMember = User::query()->find($foreignUser->id);

        Notification::assertSentTo($tenantMember, InternalWorkflowNotification::class);
        Notification::assertNotSentTo($foreignMember, InternalWorkflowNotification::class);
    }

    public function test_json_logic_evaluator_stops_on_excessive_recursion_depth(): void
    {
        $logic = ['and' => [['var' => 'ok']]];

        for ($i = 0; $i < 40; $i++) {
            $logic = ['and' => [$logic]];
        }

        $this->assertFalse(JsonLogicEvaluator::apply($logic, ['ok' => true]));
    }

    public function test_unsupported_automation_action_type_is_audited_and_skipped(): void
    {
        [$tenant, $organization, $user] = $this->makeTenantContext();
        $instance = $this->makeInstanceWithAutomation($tenant, $organization, $user, [], 'execute_shell');

        app(WorkflowAutomatedActionRunner::class)->run($instance, 'started', [
            'trigger_event' => 'started',
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'tenant_id' => $tenant->id,
            'action' => 'workflow_automation_skipped',
            'error_message' => 'unsupported_action_type',
        ]);
    }

    /**
     * @param  array<string, mixed>  $config
     */
    protected function makeInstanceWithAutomation(
        Tenant $tenant,
        Organization $organization,
        User $user,
        array $config,
        string $actionType = 'dispatch_job',
    ): WorkflowInstance {
        $workflow = $this->makeWorkflow($tenant, $organization, $user);
        $instance = app(WorkflowInstanceStarter::class)->start($workflow, $user);
        $snapshot = $instance->workflow_snapshot ?? [];

        $snapshot['automated_actions'] = [[
            'action_type' => $actionType,
            'trigger_event' => 'started',
            'is_active' => true,
            'sort_order' => 1,
            'config' => $config,
        ]];

        $instance->forceFill(['workflow_snapshot' => $snapshot])->save();

        return $instance->fresh();
    }

    protected function makeWorkflow(Tenant $tenant, Organization $organization, User $user): Workflow
    {
        $workflow = Workflow::create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'code' => 'security-flow-'.Str::random(4),
            'name' => 'Security Flow',
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
            'is_terminal' => true,
            'sort_order' => 2,
        ]);

        WorkflowTransition::create([
            'workflow_id' => $workflow->id,
            'from_step_id' => $start->id,
            'to_step_id' => $end->id,
            'action_name' => 'approve',
            'rule_type' => 'json_logic',
            'priority' => 10,
            'is_default' => true,
        ]);

        return $workflow;
    }

    /**
     * @return array{0: Tenant, 1: Organization, 2: User}
     */
    protected function makeTenantContext(): array
    {
        $plan = SubscriptionPlan::create([
            'code' => 'security-plan',
            'name' => 'Security Plan',
            'included_modules' => ['core', 'workflow'],
        ]);

        $user = User::factory()->create();

        $tenant = Tenant::create([
            'uuid' => (string) Str::uuid(),
            'code' => 'tenant-security-'.Str::random(4),
            'name' => 'Security Tenant',
            'subscription_plan_id' => $plan->id,
            'created_by' => $user->id,
        ]);

        $organization = Organization::create([
            'tenant_id' => $tenant->id,
            'code' => 'org-security',
            'name' => 'Security Organization',
        ]);

        $tenantRole = TenantRole::create([
            'tenant_id' => $tenant->id,
            'name' => 'Member',
            'slug' => 'member',
            'permissions' => ['*'],
            'is_default' => true,
        ]);

        UserTenantRole::create([
            'user_id' => $user->id,
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'tenant_role_id' => $tenantRole->id,
            'assigned_by' => $user->id,
            'is_primary' => true,
        ]);

        return [$tenant, $organization, $user];
    }
}
