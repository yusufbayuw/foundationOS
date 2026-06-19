<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Modules\Core\Models\Organization;
use Modules\Core\Models\SubscriptionPlan;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\TenantRole;
use Modules\Core\Models\User;
use Modules\Core\Models\UserTenantRole;
use Modules\Procurement\Models\PurchaseRequisition;
use Modules\Workflow\Contracts\WorkflowAssigneeResolver;
use Modules\Workflow\Contracts\WorkflowDynamicAssigneeResolver;
use Modules\Workflow\Contracts\WorkflowInstanceStarter;
use Modules\Workflow\Enums\WorkflowDefinitionStatus;
use Modules\Workflow\Models\Workflow;
use Modules\Workflow\Models\WorkflowInstance;
use Modules\Workflow\Models\WorkflowStep;
use Tests\TestCase;

class WorkflowGuardrailsTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_requester_manager_assignee_resolves_from_workflow_context(): void
    {
        [$tenant, $organization, $requester] = $this->makeTenantContext();
        $manager = $this->makeTenantUser($tenant, $organization, 'Manager Resolver');
        $workflow = $this->makeWorkflow($tenant, $organization, $requester);

        $step = WorkflowStep::query()->create([
            'workflow_id' => $workflow->id,
            'uuid' => (string) Str::uuid(),
            'code' => 'manager_review',
            'name' => 'Manager Review',
            'step_type' => 'approval',
            'assignee_type' => 'requester_manager',
            'assignee_value' => null,
            'assignee_config' => ['manager_field' => 'requester_manager_id'],
            'form_schema' => [],
            'action_schema' => [],
            'is_initial' => true,
            'sort_order' => 1,
        ]);

        $requisition = $this->makePurchaseRequisition($tenant, $requester, 4500000);
        $instance = app(WorkflowInstanceStarter::class)->start($workflow, $requester, [
            ...$requisition->workflowContext(),
            'requester_manager_id' => $manager->id,
        ], $requisition, $requester);

        $resolvedUsers = app(WorkflowAssigneeResolver::class)->resolveUsers($instance, $step);

        $this->assertCount(1, $resolvedUsers);
        $this->assertTrue($resolvedUsers->first()->is($manager));
    }

    public function test_custom_resolver_must_be_whitelisted_and_can_resolve_users(): void
    {
        [$tenant, $organization, $requester] = $this->makeTenantContext();
        $resolverTarget = $this->makeTenantUser($tenant, $organization, 'Resolver Target');
        $workflow = $this->makeWorkflow($tenant, $organization, $requester);

        config()->set('workflow.allowed_assignee_resolvers', [TestWorkflowDynamicResolver::class]);

        $step = WorkflowStep::query()->create([
            'workflow_id' => $workflow->id,
            'uuid' => (string) Str::uuid(),
            'code' => 'custom_route',
            'name' => 'Custom Route',
            'step_type' => 'approval',
            'assignee_type' => 'resolver',
            'assignee_value' => TestWorkflowDynamicResolver::class,
            'assignee_config' => ['user_id' => $resolverTarget->id],
            'form_schema' => [],
            'action_schema' => [],
            'is_initial' => true,
            'sort_order' => 1,
        ]);

        $requisition = $this->makePurchaseRequisition($tenant, $requester, 5000000);
        $instance = app(WorkflowInstanceStarter::class)->start($workflow, $requester, $requisition->workflowContext(), $requisition, $requester);

        $resolvedUsers = app(WorkflowAssigneeResolver::class)->resolveUsers($instance, $step);

        $this->assertCount(1, $resolvedUsers);
        $this->assertTrue($resolvedUsers->first()->is($resolverTarget));
    }

    public function test_procurement_setup_command_rejects_approver_outside_tenant_scope(): void
    {
        [$tenant, $organization, $requester] = $this->makeTenantContext();
        $manager = $this->makeTenantUser($tenant, $organization, 'Tenant Manager');
        $outsider = User::query()->create([
            'name' => 'Outside Finance',
            'email' => 'outside-finance-'.Str::lower(Str::random(6)).'@example.com',
            'password' => 'password',
        ]);

        $this->artisan('fos:workflow:setup-procurement-pilot', [
            'tenant' => $tenant->id,
            '--organization' => $organization->id,
            '--manager' => $manager->id,
            '--finance' => $outsider->id,
        ])->assertFailed();
    }

    protected function makeWorkflow(Tenant $tenant, Organization $organization, User $actor): Workflow
    {
        return Workflow::query()->create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'code' => 'workflow-guardrails',
            'name' => 'Workflow Guardrails',
            'module' => 'Workflow',
            'subject_type' => PurchaseRequisition::class,
            'trigger_mode' => 'manual',
            'version' => 1,
            'status' => WorkflowDefinitionStatus::Active,
            'is_active' => true,
            'published_at' => now(),
            'created_by' => $actor->id,
            'updated_by' => $actor->id,
        ]);
    }

    protected function makePurchaseRequisition(Tenant $tenant, User $requester, float $amount): PurchaseRequisition
    {
        return PurchaseRequisition::query()->create([
            'tenant_id' => $tenant->id,
            'user_id' => $requester->id,
            'requested_by' => $requester->id,
            'request_number' => 'PR-'.strtoupper(Str::random(8)),
            'request_date' => now()->toDateString(),
            'required_date' => now()->addDays(5)->toDateString(),
            'priority' => 'medium',
            'justification' => 'Workflow guardrails procurement request.',
            'total_items' => 2,
            'total_estimated_amount' => $amount,
            'status' => 'draft',
        ]);
    }

    protected function makeTenantContext(): array
    {
        $plan = SubscriptionPlan::query()->create([
            'code' => 'workflow-guardrails-plan',
            'name' => 'Workflow Guardrails Plan',
            'included_modules' => ['core', 'workflow', 'procurement'],
        ]);

        $requester = User::query()->create([
            'name' => 'Workflow Guardrails Requester',
            'email' => 'workflow-guardrails-'.Str::lower(Str::random(6)).'@example.com',
            'password' => 'password',
        ]);

        $tenant = Tenant::query()->create([
            'uuid' => (string) Str::uuid(),
            'code' => 'tenant-guardrails-'.Str::lower(Str::random(5)),
            'name' => 'Workflow Guardrails Tenant',
            'subscription_plan_id' => $plan->id,
            'created_by' => $requester->id,
        ]);

        $organization = Organization::query()->create([
            'tenant_id' => $tenant->id,
            'code' => 'org-guardrails-'.Str::lower(Str::random(4)),
            'name' => 'Workflow Guardrails Organization',
        ]);

        $role = TenantRole::query()->create([
            'tenant_id' => $tenant->id,
            'name' => 'Workflow Tenant User',
            'slug' => 'workflow-tenant-user',
            'permissions' => ['*'],
            'is_default' => true,
        ]);

        UserTenantRole::query()->create([
            'user_id' => $requester->id,
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'tenant_role_id' => $role->id,
            'assigned_by' => $requester->id,
            'assigned_at' => now(),
            'is_primary' => true,
        ]);

        return [$tenant, $organization, $requester];
    }

    protected function makeTenantUser(Tenant $tenant, Organization $organization, string $name): User
    {
        $user = User::query()->create([
            'name' => $name,
            'email' => Str::slug($name).'-'.Str::lower(Str::random(6)).'@example.com',
            'password' => 'password',
        ]);

        $role = TenantRole::query()->firstOrCreate([
            'tenant_id' => $tenant->id,
            'slug' => 'tenant-member',
        ], [
            'name' => 'Tenant Member',
            'permissions' => ['*'],
            'is_default' => false,
        ]);

        UserTenantRole::query()->create([
            'user_id' => $user->id,
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'tenant_role_id' => $role->id,
            'assigned_by' => $user->id,
            'assigned_at' => now(),
            'is_primary' => false,
        ]);

        return $user;
    }
}

class TestWorkflowDynamicResolver implements WorkflowDynamicAssigneeResolver
{
    public function resolve(WorkflowInstance $instance, WorkflowStep $step): Collection
    {
        $userId = (int) data_get($step->assignee_config, 'user_id');
        $user = User::query()->find($userId);

        return $user ? collect([$user]) : collect();
    }
}
