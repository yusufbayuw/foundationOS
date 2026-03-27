<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Modules\Core\Models\SubscriptionPlan;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\TenantRole;
use Modules\Core\Models\User;
use Modules\Core\Models\UserTenantRole;
use Modules\Procurement\Models\PurchaseRequisition;
use Modules\Workflow\Contracts\WorkflowEngine;
use Modules\Workflow\Contracts\WorkflowInstanceStarter;
use Modules\Workflow\Contracts\WorkflowResolver;
use Modules\Workflow\Models\WorkflowAssignment;
use Tests\TestCase;

class WorkflowProcurementPilotTest extends TestCase
{
    use RefreshDatabase;

    public function test_procurement_pilot_branches_to_finance_and_executive_for_large_amount(): void
    {
        [$tenant, $requester, $manager, $finance, $executive] = $this->makeTenantContext();

        $this->artisan('fos:workflow:setup-procurement-pilot', [
            'tenant' => $tenant->id,
            '--manager' => $manager->id,
            '--finance' => $finance->id,
            '--executive' => $executive->id,
            '--finance-threshold' => 10000000,
            '--executive-threshold' => 50000000,
        ])->assertSuccessful();

        $requisition = $this->makePurchaseRequisition($tenant, $requester, 75000000);
        $workflow = app(WorkflowResolver::class)->resolveForSubject($requisition->workflowSubjectType(), $requisition, (int) $tenant->id, null);

        $instance = app(WorkflowInstanceStarter::class)->start($workflow, $requester, $requisition->workflowContext(), $requisition, $requester);

        $this->assertDatabaseHas('workflow_assignments', [
            'workflow_instance_id' => $instance->id,
            'assigned_to_id' => $manager->id,
            'status' => 'pending',
        ]);

        app(WorkflowEngine::class)->advance($instance, 'approve', [
            'approval_note' => 'Manager approves this request for further finance validation.',
        ], $manager);

        $instance->refresh();
        $requisition->refresh();

        $this->assertSame('in_review', $requisition->status);
        $this->assertSame('finance_approval', $instance->currentStep?->code);
        $this->assertDatabaseHas('workflow_assignments', [
            'workflow_instance_id' => $instance->id,
            'assigned_to_id' => $finance->id,
            'status' => 'pending',
        ]);

        app(WorkflowEngine::class)->advance($instance, 'approve', [
            'approval_note' => 'Finance approves and escalates to executive due to amount threshold.',
        ], $finance);

        $instance->refresh();
        $this->assertSame('executive_approval', $instance->currentStep?->code);
        $this->assertDatabaseHas('workflow_assignments', [
            'workflow_instance_id' => $instance->id,
            'assigned_to_id' => $executive->id,
            'status' => 'pending',
        ]);

        app(WorkflowEngine::class)->advance($instance, 'approve', [
            'approval_note' => 'Executive gives final approval for this procurement.',
        ], $executive);

        $instance->refresh();
        $requisition->refresh();

        $this->assertSame('completed', $instance->status->value);
        $this->assertSame('approved', $requisition->status);
        $this->assertTrue((bool) $requisition->ready_for_sourcing);
        $this->assertSame($executive->id, $requisition->approved_by);
    }

    public function test_procurement_pilot_bypasses_finance_for_small_amount(): void
    {
        [$tenant, $requester, $manager, $finance] = $this->makeTenantContext(includeExecutive: false);

        $this->artisan('fos:workflow:setup-procurement-pilot', [
            'tenant' => $tenant->id,
            '--manager' => $manager->id,
            '--finance' => $finance->id,
            '--finance-threshold' => 10000000,
        ])->assertSuccessful();

        $requisition = $this->makePurchaseRequisition($tenant, $requester, 2500000);
        $workflow = app(WorkflowResolver::class)->resolveForSubject($requisition->workflowSubjectType(), $requisition, (int) $tenant->id, null);

        $instance = app(WorkflowInstanceStarter::class)->start($workflow, $requester, $requisition->workflowContext(), $requisition, $requester);

        app(WorkflowEngine::class)->advance($instance, 'approve', [
            'approval_note' => 'Manager approves a low amount requisition.',
        ], $manager);

        $instance->refresh();
        $requisition->refresh();

        $this->assertSame('completed', $instance->status->value);
        $this->assertSame('approved', $requisition->status);
        $this->assertTrue((bool) $requisition->ready_for_sourcing);
        $this->assertDatabaseMissing('workflow_assignments', [
            'workflow_instance_id' => $instance->id,
            'assigned_to_id' => $finance->id,
            'status' => 'pending',
        ]);
    }

    public function test_procurement_return_sets_revision_required_and_not_ready_for_sourcing(): void
    {
        [$tenant, $requester, $manager, $finance, $executive] = $this->makeTenantContext();

        $this->artisan('fos:workflow:setup-procurement-pilot', [
            'tenant' => $tenant->id,
            '--manager' => $manager->id,
            '--finance' => $finance->id,
            '--executive' => $executive->id,
            '--finance-threshold' => 10000000,
            '--executive-threshold' => 50000000,
        ])->assertSuccessful();

        $requisition = $this->makePurchaseRequisition($tenant, $requester, 75000000);
        $workflow = app(WorkflowResolver::class)->resolveForSubject($requisition->workflowSubjectType(), $requisition, (int) $tenant->id, null);
        $instance = app(WorkflowInstanceStarter::class)->start($workflow, $requester, $requisition->workflowContext(), $requisition, $requester);

        app(WorkflowEngine::class)->advance($instance, 'approve', [
            'approval_note' => 'Manager approves and sends to finance.',
        ], $manager);

        $instance->refresh();

        app(WorkflowEngine::class)->returnToStep($instance, (int) $instance->workflow->steps()->where('code', 'manager_approval')->value('id'), [
            'approval_note' => 'Please revise the requested amount breakdown.',
        ], $finance, 'Need a clearer sourcing justification.');

        $instance->refresh();
        $requisition->refresh();

        $this->assertSame('running', $instance->status->value);
        $this->assertSame('manager_approval', $instance->currentStep?->code);
        $this->assertSame('revision_required', $requisition->status);
        $this->assertFalse((bool) $requisition->ready_for_sourcing);
    }

    protected function makeTenantContext(bool $includeExecutive = true): array
    {
        $plan = SubscriptionPlan::query()->create([
            'code' => 'workflow-procurement-plan',
            'name' => 'Workflow Procurement Plan',
            'included_modules' => ['core', 'workflow', 'procurement'],
        ]);

        $requester = User::query()->create([
            'name' => 'Requester',
            'email' => 'requester-'.Str::lower(Str::random(6)).'@example.com',
            'password' => 'password',
        ]);

        $tenant = Tenant::query()->create([
            'uuid' => (string) Str::uuid(),
            'code' => 'tenant-proc-'.Str::lower(Str::random(4)),
            'name' => 'Tenant Procurement Pilot',
            'subscription_plan_id' => $plan->id,
            'created_by' => $requester->id,
        ]);

        $manager = User::query()->create([
            'name' => 'Manager Approver',
            'email' => 'manager-'.Str::lower(Str::random(6)).'@example.com',
            'password' => 'password',
        ]);

        $finance = User::query()->create([
            'name' => 'Finance Approver',
            'email' => 'finance-'.Str::lower(Str::random(6)).'@example.com',
            'password' => 'password',
        ]);

        $executive = null;

        if ($includeExecutive) {
            $executive = User::query()->create([
                'name' => 'Executive Approver',
                'email' => 'executive-'.Str::lower(Str::random(6)).'@example.com',
                'password' => 'password',
            ]);
        }

        $role = TenantRole::query()->create([
            'tenant_id' => $tenant->id,
            'name' => 'Workflow Approver',
            'slug' => 'workflow-approver',
            'permissions' => ['*'],
            'is_default' => false,
        ]);

        foreach (array_filter([$requester, $manager, $finance, $executive]) as $user) {
            UserTenantRole::query()->create([
                'user_id' => $user->id,
                'tenant_id' => $tenant->id,
                'tenant_role_id' => $role->id,
                'assigned_by' => $requester->id,
                'assigned_at' => now(),
                'is_primary' => $user->is($requester),
            ]);
        }

        return [$tenant, $requester, $manager, $finance, $executive];
    }

    protected function makePurchaseRequisition(Tenant $tenant, User $requester, float $amount): PurchaseRequisition
    {
        return PurchaseRequisition::query()->create([
            'tenant_id' => $tenant->id,
            'user_id' => $requester->id,
            'requested_by' => $requester->id,
            'request_number' => 'PR-'.strtoupper(Str::random(8)),
            'request_date' => now()->toDateString(),
            'required_date' => now()->addDays(7)->toDateString(),
            'priority' => 'high',
            'justification' => 'Procurement approval pilot test requisition.',
            'total_items' => 4,
            'total_estimated_amount' => $amount,
            'status' => 'draft',
        ]);
    }
}
