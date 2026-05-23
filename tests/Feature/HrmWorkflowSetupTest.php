<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Modules\Core\Models\SubscriptionPlan;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\TenantRole;
use Modules\Core\Models\User;
use Modules\Core\Models\UserTenantRole;
use Modules\Employee\Models\LeaveRequest;
use Modules\Employee\Models\SalarySlip;
use Modules\Workflow\Enums\WorkflowDefinitionStatus;
use Modules\Workflow\Models\Workflow;
use Tests\TestCase;

class HrmWorkflowSetupTest extends TestCase
{
    use RefreshDatabase;

    public function test_setup_hrm_workflows_creates_active_leave_and_salary_slip_definitions(): void
    {
        [$tenant, $manager, $hr, $finance] = $this->makeTenantContext('hrm-workflow');

        $this->artisan('fos:workflow:setup-hrm', [
            'tenant' => $tenant->id,
            '--manager' => $manager->id,
            '--hr' => $hr->id,
            '--finance' => $finance->id,
        ])->assertSuccessful();

        $leaveWorkflow = Workflow::query()
            ->where('tenant_id', $tenant->id)
            ->where('code', 'leave-request-approval')
            ->firstOrFail();

        $salaryWorkflow = Workflow::query()
            ->where('tenant_id', $tenant->id)
            ->where('code', 'salary-slip-approval')
            ->firstOrFail();

        $this->assertSame(WorkflowDefinitionStatus::Active, $leaveWorkflow->status);
        $this->assertSame(WorkflowDefinitionStatus::Active, $salaryWorkflow->status);
        $this->assertTrue((bool) $leaveWorkflow->is_active);
        $this->assertTrue((bool) $salaryWorkflow->is_active);
        $this->assertSame(LeaveRequest::class, $leaveWorkflow->subject_type);
        $this->assertSame(SalarySlip::class, $salaryWorkflow->subject_type);

        $this->assertDatabaseHas('workflow_steps', [
            'workflow_id' => $leaveWorkflow->id,
            'code' => 'manager_approval',
            'assignee_value' => (string) $manager->id,
            'is_initial' => true,
        ]);

        $this->assertDatabaseHas('workflow_transitions', [
            'workflow_id' => $leaveWorkflow->id,
            'action_name' => 'approve',
            'condition_rules' => json_encode(['>' => [['var' => 'total_days'], 3]]),
        ]);

        $this->assertDatabaseHas('workflow_steps', [
            'workflow_id' => $salaryWorkflow->id,
            'code' => 'finance_approval',
            'assignee_value' => (string) $finance->id,
        ]);
    }

    public function test_setup_hrm_workflows_rejects_approvers_outside_the_tenant(): void
    {
        [$tenant, $manager, $hr] = $this->makeTenantContext('hrm-guardrail');
        [, $outsideFinance] = $this->makeTenantContext('hrm-outside');

        $this->artisan('fos:workflow:setup-hrm', [
            'tenant' => $tenant->id,
            '--manager' => $manager->id,
            '--hr' => $hr->id,
            '--finance' => $outsideFinance->id,
        ])
            ->expectsOutputToContain('is not a member of tenant')
            ->assertFailed();

        $this->assertSame(0, Workflow::query()->count());
    }

    /**
     * @return array{0: Tenant, 1: User, 2: User, 3: User}
     */
    private function makeTenantContext(string $code): array
    {
        $plan = SubscriptionPlan::query()->create([
            'code' => "{$code}-plan",
            'name' => Str::title(str_replace('-', ' ', $code)).' Plan',
            'included_modules' => ['core', 'workflow', 'employee'],
        ]);

        $tenant = Tenant::query()->create([
            'uuid' => (string) Str::uuid(),
            'code' => $code,
            'name' => Str::title(str_replace('-', ' ', $code)),
            'subscription_plan_id' => $plan->id,
        ]);

        $manager = User::query()->create([
            'name' => "{$code} Manager",
            'email' => "{$code}-manager@example.test",
            'password' => 'password',
        ]);

        $hr = User::query()->create([
            'name' => "{$code} HR",
            'email' => "{$code}-hr@example.test",
            'password' => 'password',
        ]);

        $finance = User::query()->create([
            'name' => "{$code} Finance",
            'email' => "{$code}-finance@example.test",
            'password' => 'password',
        ]);

        $role = TenantRole::query()->create([
            'tenant_id' => $tenant->id,
            'name' => "{$code} Workflow User",
            'slug' => "{$code}-workflow-user",
            'permissions' => ['*'],
            'is_default' => false,
        ]);

        foreach ([$manager, $hr, $finance] as $user) {
            UserTenantRole::query()->create([
                'user_id' => $user->id,
                'tenant_id' => $tenant->id,
                'tenant_role_id' => $role->id,
                'assigned_by' => $hr->id,
                'assigned_at' => now(),
                'is_primary' => $user->is($hr),
            ]);
        }

        return [$tenant, $manager, $hr, $finance];
    }
}
