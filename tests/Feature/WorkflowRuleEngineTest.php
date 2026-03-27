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
use Modules\Procurement\Models\PurchaseRequisition;
use Modules\Workflow\Contracts\RuleEngine;
use Modules\Workflow\Contracts\WorkflowFormSchemaValidator;
use Modules\Workflow\Contracts\WorkflowInstanceStarter;
use Modules\Workflow\Contracts\WorkflowResolver;
use Modules\Workflow\Models\Workflow;
use Modules\Workflow\Models\WorkflowAutomatedAction;
use Modules\Workflow\Models\WorkflowStep;
use Tests\TestCase;

class WorkflowRuleEngineTest extends TestCase
{
    use RefreshDatabase;

    public function test_rule_engine_evaluates_dynamic_field_state(): void
    {
        $fields = app(RuleEngine::class)->evaluateFieldState([
            [
                'name' => 'amount',
                'type' => 'number',
                'visibility_rules' => ['>' => [['var' => 'requested_total'], 1000]],
                'required_rules' => ['===' => [['var' => 'priority'], 'high']],
            ],
            [
                'name' => 'internal_note',
                'type' => 'textarea',
                'disabled_rules' => ['===' => [['var' => 'status'], 'locked']],
            ],
        ], [
            'requested_total' => 2500,
            'priority' => 'high',
            'status' => 'locked',
        ]);

        $this->assertTrue($fields[0]['_visible']);
        $this->assertTrue($fields[0]['_required']);
        $this->assertTrue($fields[1]['_disabled']);
    }

    public function test_validator_ignores_hidden_and_disabled_fields(): void
    {
        $step = new WorkflowStep([
            'form_schema' => [
                [
                    'name' => 'approval_note',
                    'type' => 'textarea',
                    'required' => true,
                    'validation' => ['min:10'],
                ],
                [
                    'name' => 'secret_field',
                    'type' => 'text',
                    'visibility_rules' => ['===' => [['var' => 'show_secret'], true]],
                    'validation' => ['required'],
                ],
                [
                    'name' => 'locked_amount',
                    'type' => 'number',
                    'disabled_rules' => ['===' => [['var' => 'status'], 'locked']],
                    'validation' => ['numeric'],
                ],
            ],
        ]);

        $validated = app(WorkflowFormSchemaValidator::class)->validate($step, [
            'approval_note' => 'Approved with complete supporting rationale.',
            'secret_field' => 'should be ignored',
            'locked_amount' => 999,
        ], [
            'show_secret' => false,
            'status' => 'locked',
        ]);

        $this->assertArrayHasKey('approval_note', $validated);
        $this->assertArrayNotHasKey('secret_field', $validated);
        $this->assertArrayNotHasKey('locked_amount', $validated);
    }

    public function test_resolver_prefers_subject_workflow_code_when_available(): void
    {
        [$tenant, $organization, $user] = $this->makeTenantContext();
        $requisition = $this->makePurchaseRequisition($tenant, $user);

        Workflow::query()->create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'code' => 'generic-procurement-flow',
            'name' => 'Generic Procurement Workflow',
            'module' => 'Procurement',
            'subject_type' => PurchaseRequisition::class,
            'trigger_mode' => 'manual',
            'version' => 2,
            'status' => 'active',
            'is_active' => true,
            'published_at' => now(),
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        $expected = Workflow::query()->create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'code' => 'purchase-requisition-approval',
            'name' => 'Purchase Requisition Approval',
            'module' => 'Procurement',
            'subject_type' => PurchaseRequisition::class,
            'trigger_mode' => 'manual',
            'version' => 1,
            'status' => 'active',
            'is_active' => true,
            'published_at' => now(),
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        $resolved = app(WorkflowResolver::class)->resolveForSubject(
            $requisition->workflowSubjectType(),
            $requisition,
            (int) $tenant->id,
            (int) $organization->id,
        );

        $this->assertTrue($resolved->is($expected));
    }

    public function test_instance_starter_snapshots_automated_actions_and_subject_context(): void
    {
        [$tenant, $organization, $user] = $this->makeTenantContext();
        $requisition = $this->makePurchaseRequisition($tenant, $user);

        $workflow = Workflow::query()->create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'code' => $requisition->workflowCode(),
            'name' => 'Purchase Requisition Approval',
            'module' => 'Procurement',
            'subject_type' => PurchaseRequisition::class,
            'trigger_mode' => 'manual',
            'version' => 1,
            'status' => 'active',
            'is_active' => true,
            'published_at' => now(),
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        $step = WorkflowStep::query()->create([
            'workflow_id' => $workflow->id,
            'uuid' => (string) Str::uuid(),
            'code' => 'manager_review',
            'name' => 'Manager Review',
            'step_type' => 'approval',
            'assignee_type' => 'user',
            'assignee_value' => (string) $user->id,
            'form_schema' => [],
            'action_schema' => [['name' => 'approve', 'label' => 'Approve']],
            'is_initial' => true,
            'sort_order' => 1,
        ]);

        WorkflowAutomatedAction::query()->create([
            'workflow_id' => $workflow->id,
            'step_id' => $step->id,
            'trigger_event' => 'started',
            'action_type' => 'audit_note',
            'name' => 'Audit Start',
            'config' => ['description' => 'Started'],
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $instance = app(WorkflowInstanceStarter::class)->start($workflow, $user, [], $requisition, $user);

        $this->assertSame($requisition->workflowSubjectLabel(), $instance->subject_label);
        $this->assertSame((float) $requisition->total_estimated_amount, (float) data_get($instance->context_data, 'total_estimated_amount'));
        $this->assertCount(1, data_get($instance->workflow_snapshot, 'automated_actions', []));
    }

    protected function makePurchaseRequisition(Tenant $tenant, User $user): PurchaseRequisition
    {
        return PurchaseRequisition::query()->create([
            'tenant_id' => $tenant->id,
            'user_id' => $user->id,
            'requested_by' => $user->id,
            'request_number' => 'PR-'.strtoupper(Str::random(6)),
            'request_date' => now()->toDateString(),
            'required_date' => now()->addWeek()->toDateString(),
            'priority' => 'high',
            'justification' => 'Need urgent procurement for operational continuity.',
            'total_items' => 3,
            'total_estimated_amount' => 2500000,
            'status' => 'draft',
        ]);
    }

    protected function makeTenantContext(): array
    {
        $plan = SubscriptionPlan::query()->create([
            'code' => 'workflow-v2-plan',
            'name' => 'Workflow V2 Plan',
            'included_modules' => ['core', 'workflow', 'procurement'],
        ]);

        $user = User::query()->create([
            'name' => 'Workflow V2 Admin',
            'email' => 'workflow-v2-admin@example.com',
            'password' => 'password',
        ]);

        $tenant = Tenant::query()->create([
            'uuid' => (string) Str::uuid(),
            'code' => 'tenant-workflow-v2',
            'name' => 'Tenant Workflow V2',
            'subscription_plan_id' => $plan->id,
            'created_by' => $user->id,
        ]);

        $organization = Organization::query()->create([
            'tenant_id' => $tenant->id,
            'code' => 'org-workflow-v2',
            'name' => 'Workflow V2 Organization',
        ]);

        $tenantRole = TenantRole::query()->create([
            'tenant_id' => $tenant->id,
            'name' => 'Workflow V2 Admin',
            'slug' => 'workflow-v2-admin',
            'permissions' => ['*'],
            'is_default' => true,
        ]);

        UserTenantRole::query()->create([
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
