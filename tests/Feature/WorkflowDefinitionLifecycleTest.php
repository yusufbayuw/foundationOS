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
use Modules\Workflow\Enums\WorkflowDefinitionStatus;
use Modules\Workflow\Models\Workflow;
use Modules\Workflow\Models\WorkflowStep;
use Modules\Workflow\Models\WorkflowTransition;
use Modules\Workflow\Services\WorkflowDefinitionLifecycleService;
use Tests\TestCase;

class WorkflowDefinitionLifecycleTest extends TestCase
{
    use RefreshDatabase;

    public function test_publish_marks_current_version_active_and_deactivates_siblings(): void
    {
        [$tenant, $organization, $user] = $this->makeTenantContext();

        $v1 = $this->makeWorkflowDefinition($tenant, $organization, $user, version: 1, status: 'active', isActive: true);
        $v2 = $this->makeWorkflowDefinition($tenant, $organization, $user, version: 2, status: 'draft', isActive: false);

        $published = app(WorkflowDefinitionLifecycleService::class)->publish($v2, $user->id);

        $this->assertSame(WorkflowDefinitionStatus::Active->value, $published->status->value);
        $this->assertTrue($published->is_active);
        $this->assertNotNull($published->published_at);

        $this->assertDatabaseHas('workflows', [
            'id' => $v1->id,
            'is_active' => false,
        ]);
        $this->assertDatabaseHas('workflows', [
            'id' => $v2->id,
            'is_active' => true,
            'status' => 'active',
        ]);
    }

    public function test_archive_marks_workflow_archived_and_inactive(): void
    {
        [$tenant, $organization, $user] = $this->makeTenantContext();

        $workflow = $this->makeWorkflowDefinition($tenant, $organization, $user, version: 1, status: 'active', isActive: true);

        $archived = app(WorkflowDefinitionLifecycleService::class)->archive($workflow, $user->id);

        $this->assertSame(WorkflowDefinitionStatus::Archived->value, $archived->status->value);
        $this->assertFalse($archived->is_active);
    }

    public function test_duplicate_as_new_version_clones_steps_and_transitions(): void
    {
        [$tenant, $organization, $user] = $this->makeTenantContext();

        $workflow = $this->makeWorkflowDefinition($tenant, $organization, $user, version: 1, status: 'active', isActive: true);
        [$firstStep, $secondStep] = $this->attachStepsAndTransition($workflow, $user);

        $clone = app(WorkflowDefinitionLifecycleService::class)->duplicateAsNewVersion($workflow, $user->id);

        $this->assertSame(2, $clone->version);
        $this->assertSame(WorkflowDefinitionStatus::Draft->value, $clone->status->value);
        $this->assertFalse($clone->is_active);
        $this->assertCount(2, $clone->steps);
        $this->assertCount(1, $clone->transitions);

        $clonedTransition = $clone->transitions->first();
        $this->assertNotSame($workflow->id, $clone->id);
        $this->assertNotSame($firstStep->id, $clone->steps->firstWhere('code', 'start_review')->id);
        $this->assertTrue($clone->steps->pluck('id')->contains($clonedTransition->from_step_id));
        $this->assertTrue($clone->steps->pluck('id')->contains($clonedTransition->to_step_id));
        $this->assertFalse(in_array($firstStep->id, [$clonedTransition->from_step_id, $clonedTransition->to_step_id], true));
        $this->assertFalse(in_array($secondStep->id, [$clonedTransition->from_step_id, $clonedTransition->to_step_id], true));
    }

    protected function makeWorkflowDefinition(
        Tenant $tenant,
        Organization $organization,
        User $user,
        int $version,
        string $status,
        bool $isActive,
    ): Workflow {
        return Workflow::query()->create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'code' => 'approval-pr',
            'name' => 'Approval Purchase Requisition',
            'module' => 'Procurement',
            'subject_type' => 'Modules\\Procurement\\Models\\PurchaseRequisition',
            'trigger_mode' => 'manual',
            'version' => $version,
            'status' => $status,
            'is_active' => $isActive,
            'published_at' => $isActive ? now() : null,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);
    }

    protected function attachStepsAndTransition(Workflow $workflow, User $user): array
    {
        $first = WorkflowStep::query()->create([
            'workflow_id' => $workflow->id,
            'uuid' => (string) Str::uuid(),
            'code' => 'start_review',
            'name' => 'Start Review',
            'step_type' => 'task',
            'assignee_type' => 'user',
            'assignee_value' => (string) $user->id,
            'form_schema' => [],
            'action_schema' => [['name' => 'submit', 'label' => 'Submit']],
            'is_initial' => true,
            'sort_order' => 1,
        ]);

        $second = WorkflowStep::query()->create([
            'workflow_id' => $workflow->id,
            'uuid' => (string) Str::uuid(),
            'code' => 'final_approval',
            'name' => 'Final Approval',
            'step_type' => 'approval',
            'assignee_type' => 'user',
            'assignee_value' => (string) $user->id,
            'form_schema' => [],
            'action_schema' => [['name' => 'approve', 'label' => 'Approve']],
            'is_terminal' => true,
            'sort_order' => 2,
        ]);

        WorkflowTransition::query()->create([
            'workflow_id' => $workflow->id,
            'from_step_id' => $first->id,
            'to_step_id' => $second->id,
            'action_name' => 'submit',
            'rule_type' => 'json_logic',
            'condition_rules' => null,
            'priority' => 0,
            'is_default' => true,
        ]);

        return [$first, $second];
    }

    protected function makeTenantContext(): array
    {
        $plan = SubscriptionPlan::create([
            'code' => 'workflow-lifecycle-plan',
            'name' => 'Workflow Lifecycle Plan',
            'included_modules' => ['core', 'workflow'],
        ]);

        $user = User::create([
            'name' => 'Workflow Owner',
            'email' => 'workflow-owner@example.com',
            'password' => 'password',
        ]);

        $tenant = Tenant::create([
            'uuid' => (string) Str::uuid(),
            'code' => 'tenant-workflow-lifecycle',
            'name' => 'Tenant Workflow Lifecycle',
            'subscription_plan_id' => $plan->id,
            'created_by' => $user->id,
        ]);

        $organization = Organization::create([
            'tenant_id' => $tenant->id,
            'code' => 'org-workflow-lifecycle',
            'name' => 'Workflow Organization',
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
            'organization_id' => $organization->id,
            'tenant_role_id' => $tenantRole->id,
            'assigned_by' => $user->id,
            'is_primary' => true,
        ]);

        return [$tenant, $organization, $user];
    }
}
