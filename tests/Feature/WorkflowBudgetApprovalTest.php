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
use Modules\Finance\Models\Budget;
use Modules\Finance\Models\ChartOfAccount;
use Modules\Workflow\Contracts\WorkflowEngine;
use Modules\Workflow\Contracts\WorkflowInstanceStarter;
use Modules\Workflow\Contracts\WorkflowResolver;
use Tests\TestCase;

class WorkflowBudgetApprovalTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_budget_workflow_approves_budget_and_syncs_subject_state(): void
    {
        [$tenant, $organization, $requester, $finance, $executive] = $this->makeTenantContext();

        $this->artisan('fos:workflow:setup-budget-workflow', [
            'tenant' => $tenant->id,
            '--organization' => $organization->id,
            '--finance' => $finance->id,
            '--executive' => $executive->id,
            '--executive-threshold' => 50000000,
        ])->assertSuccessful();

        $budget = $this->makeBudget($tenant, $organization, 75000000);
        $workflow = app(WorkflowResolver::class)->resolveForSubject($budget->workflowSubjectType(), $budget, (int) $tenant->id, (int) $organization->id);
        $instance = app(WorkflowInstanceStarter::class)->start($workflow, $requester, $budget->workflowContext(), $budget, $requester);

        $budget->refresh();
        $this->assertSame('submitted', $budget->status);

        app(WorkflowEngine::class)->advance($instance, 'approve', [
            'approval_note' => 'Finance approves and escalates for executive review.',
        ], $finance);

        $instance->refresh();
        $budget->refresh();

        $this->assertSame('executive_approval', $instance->currentStep?->code);
        $this->assertSame('in_review', $budget->status);

        app(WorkflowEngine::class)->advance($instance, 'approve', [
            'approval_note' => 'Executive gives final budget approval.',
        ], $executive);

        $instance->refresh();
        $budget->refresh();

        $this->assertSame('completed', $instance->status->value);
        $this->assertSame('approved', $budget->status);
        $this->assertSame($executive->id, $budget->approved_by);
        $this->assertNotNull($budget->approved_at);
        $this->assertTrue($budget->isLockedForMutation());
    }

    public function test_budget_workflow_return_sets_revision_required(): void
    {
        [$tenant, $organization, $requester, $finance, $executive] = $this->makeTenantContext();

        $this->artisan('fos:workflow:setup-budget-workflow', [
            'tenant' => $tenant->id,
            '--organization' => $organization->id,
            '--finance' => $finance->id,
            '--executive' => $executive->id,
            '--executive-threshold' => 50000000,
        ])->assertSuccessful();

        $budget = $this->makeBudget($tenant, $organization, 80000000);
        $workflow = app(WorkflowResolver::class)->resolveForSubject($budget->workflowSubjectType(), $budget, (int) $tenant->id, (int) $organization->id);
        $instance = app(WorkflowInstanceStarter::class)->start($workflow, $requester, $budget->workflowContext(), $budget, $requester);

        app(WorkflowEngine::class)->advance($instance, 'approve', [
            'approval_note' => 'Finance forwards to executive.',
        ], $finance);

        $instance->refresh();

        app(WorkflowEngine::class)->returnToStep($instance, (int) $instance->workflow->steps()->where('code', 'finance_approval')->value('id'), [
            'approval_note' => 'Need revised budget assumptions.',
        ], $executive, 'Please revise the cost assumptions.');

        $instance->refresh();
        $budget->refresh();

        $this->assertSame('running', $instance->status->value);
        $this->assertSame('finance_approval', $instance->currentStep?->code);
        $this->assertSame('revision_required', $budget->status);
        $this->assertFalse($budget->isLockedForMutation());
    }

    protected function makeTenantContext(): array
    {
        $plan = SubscriptionPlan::query()->create([
            'code' => 'workflow-budget-plan',
            'name' => 'Workflow Budget Plan',
            'included_modules' => ['core', 'workflow', 'finance'],
        ]);

        $requester = User::query()->create([
            'name' => 'Budget Requester',
            'email' => 'budget-requester-'.Str::lower(Str::random(6)).'@example.com',
            'password' => 'password',
        ]);

        $tenant = Tenant::query()->create([
            'uuid' => (string) Str::uuid(),
            'code' => 'tenant-budget-'.Str::lower(Str::random(5)),
            'name' => 'Budget Workflow Tenant',
            'subscription_plan_id' => $plan->id,
            'created_by' => $requester->id,
        ]);

        $organization = Organization::query()->create([
            'tenant_id' => $tenant->id,
            'code' => 'org-budget-'.Str::lower(Str::random(4)),
            'name' => 'Budget Workflow Organization',
        ]);

        $finance = User::query()->create([
            'name' => 'Finance Budget Approver',
            'email' => 'budget-finance-'.Str::lower(Str::random(6)).'@example.com',
            'password' => 'password',
        ]);

        $executive = User::query()->create([
            'name' => 'Executive Budget Approver',
            'email' => 'budget-executive-'.Str::lower(Str::random(6)).'@example.com',
            'password' => 'password',
        ]);

        $role = TenantRole::query()->create([
            'tenant_id' => $tenant->id,
            'name' => 'Budget Workflow User',
            'slug' => 'budget-workflow-user',
            'permissions' => ['*'],
            'is_default' => false,
        ]);

        foreach ([$requester, $finance, $executive] as $user) {
            UserTenantRole::query()->create([
                'user_id' => $user->id,
                'tenant_id' => $tenant->id,
                'organization_id' => $organization->id,
                'tenant_role_id' => $role->id,
                'assigned_by' => $requester->id,
                'assigned_at' => now(),
                'is_primary' => $user->is($requester),
            ]);
        }

        return [$tenant, $organization, $requester, $finance, $executive];
    }

    protected function makeBudget(Tenant $tenant, Organization $organization, float $allocatedAmount): Budget
    {
        $account = ChartOfAccount::query()->create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'code' => '6100-'.Str::upper(Str::random(3)),
            'name' => 'Budget Expense Account',
            'level' => 1,
            'type' => 'expense',
            'category' => 'operating',
            'normal_balance' => 'debit',
            'is_bank_account' => false,
            'is_active' => true,
            'is_locked' => false,
            'opening_balance' => 0,
            'current_balance' => 0,
        ]);

        return Budget::query()->create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'chart_of_account_id' => $account->id,
            'fiscal_year' => '2026',
            'name' => 'Operations Budget '.Str::upper(Str::random(4)),
            'code' => 'BGT-'.Str::upper(Str::random(6)),
            'allocated_amount' => $allocatedAmount,
            'used_amount' => 0,
            'remaining_amount' => $allocatedAmount,
            'description' => 'Budget workflow test.',
            'status' => 'draft',
        ]);
    }
}
