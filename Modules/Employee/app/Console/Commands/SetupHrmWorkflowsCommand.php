<?php

namespace Modules\Employee\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\User;
use Modules\Employee\Models\LeaveRequest;
use Modules\Employee\Models\SalarySlip;
use Modules\Workflow\Enums\WorkflowDefinitionStatus;
use Modules\Workflow\Models\Workflow;
use Modules\Workflow\Models\WorkflowStep;
use Modules\Workflow\Models\WorkflowTransition;
use Modules\Workflow\Services\WorkflowDefinitionLifecycleService;

/**
 * Seeds default HRM approval workflows:
 *  - leave-request-approval : cuti > 3 hari → Manager + HR
 *  - salary-slip-approval   : slip gaji → HR → Finance
 */
class SetupHrmWorkflowsCommand extends Command
{
    protected $signature = 'fos:workflow:setup-hrm
        {tenant : Tenant ID}
        {--manager= : User ID Manager (approver cuti step 1)}
        {--hr= : User ID HR (approver cuti step 2 & slip gaji step 1)}
        {--finance= : User ID Finance (approver slip gaji step 2)}
        {--replace : Archive active workflow and create new version}';

    protected $description = 'Seed default HRM approval workflows (leave request + salary slip)';

    public function handle(WorkflowDefinitionLifecycleService $lifecycle): int
    {
        $tenant = Tenant::query()->findOrFail((int) $this->argument('tenant'));

        $manager = $this->resolveUserOption('manager', required: true);
        $hr = $this->resolveUserOption('hr', required: true);
        $finance = $this->resolveUserOption('finance', required: true);
        $actorId = $hr->id;

        $this->assertUserBelongsToTenant($manager, $tenant);
        $this->assertUserBelongsToTenant($hr, $tenant);
        $this->assertUserBelongsToTenant($finance, $tenant);

        $this->info("Setting up HRM workflows for tenant [{$tenant->name}]...");

        $this->setupLeaveRequestWorkflow($tenant, $manager, $hr, $lifecycle, $actorId);
        $this->setupSalarySlipWorkflow($tenant, $hr, $finance, $lifecycle, $actorId);

        $this->info('Done. HRM workflows activated.');

        return self::SUCCESS;
    }

    private function setupLeaveRequestWorkflow(
        Tenant $tenant,
        User $manager,
        User $hr,
        WorkflowDefinitionLifecycleService $lifecycle,
        int $actorId,
    ): void {
        $code = 'leave-request-approval';

        $existing = Workflow::query()
            ->where('tenant_id', $tenant->id)
            ->where('code', $code)
            ->whereNull('organization_id')
            ->latest('version')
            ->first();

        if ($existing && ! $this->option('replace')) {
            $this->warn("  [skip] {$code} already exists (use --replace to refresh).");

            return;
        }

        if ($existing?->is_active) {
            $lifecycle->archive($existing, $actorId);
        }

        $version = $existing ? ((int) $existing->version + 1) : 1;

        $workflow = Workflow::query()->create([
            'tenant_id' => $tenant->id,
            'organization_id' => null,
            'code' => $code,
            'name' => 'Leave Request Approval',
            'description' => 'Cuti > 3 hari: Manager + HR. Cuti ≤ 3 hari: langsung Manager.',
            'module' => 'Employee',
            'subject_type' => LeaveRequest::class,
            'trigger_mode' => 'manual',
            'version' => $version,
            'status' => WorkflowDefinitionStatus::Draft,
            'is_active' => false,
            'created_by' => $actorId,
            'updated_by' => $actorId,
        ]);

        $approvalActions = [
            ['name' => 'approve', 'label' => 'Approve', 'style' => ['success']],
            ['name' => 'reject', 'label' => 'Reject', 'style' => ['danger']],
        ];

        $formSchema = [[
            'name' => 'approval_note',
            'label' => 'Catatan Persetujuan',
            'type' => 'textarea',
            'required' => true,
            'validation' => ['min:5'],
            'column_span' => 'full',
        ]];

        $managerStep = $this->makeStep($workflow, [
            'code' => 'manager_approval',
            'name' => 'Manager Approval',
            'assignee_user_id' => $manager->id,
            'sort_order' => 1,
            'is_initial' => true,
            'form_schema' => $formSchema,
            'action_schema' => $approvalActions,
        ]);

        $hrStep = $this->makeStep($workflow, [
            'code' => 'hr_approval',
            'name' => 'HR Approval',
            'assignee_user_id' => $hr->id,
            'sort_order' => 2,
            'form_schema' => $formSchema,
            'action_schema' => $approvalActions,
        ]);

        $approvedStep = $this->makeStep($workflow, [
            'code' => 'approved',
            'name' => 'Approved',
            'sort_order' => 3,
            'step_type' => 'end',
            'is_terminal' => true,
            'form_schema' => [],
            'action_schema' => [],
            'assignee_type' => null,
            'assignee_value' => null,
        ]);

        // Cuti > 3 hari → Manager → HR → Approved
        $this->makeTransition($workflow, $managerStep, $hrStep, 'approve', [
            '>' => [['var' => 'total_days'], 3],
        ], 10);

        // Cuti ≤ 3 hari → Manager → Approved (skip HR)
        $this->makeTransition($workflow, $managerStep, $approvedStep, 'approve', null, 0, true);
        $this->makeTransition($workflow, $managerStep, null, 'reject', null, 0, true);
        $this->makeTransition($workflow, $hrStep, $approvedStep, 'approve', null, 0, true);
        $this->makeTransition($workflow, $hrStep, null, 'reject', null, 0, true);

        $lifecycle->publish($workflow, $actorId);
        $this->line("  ✓ {$code} v{$version} activated.");
    }

    private function setupSalarySlipWorkflow(
        Tenant $tenant,
        User $hr,
        User $finance,
        WorkflowDefinitionLifecycleService $lifecycle,
        int $actorId,
    ): void {
        $code = 'salary-slip-approval';

        $existing = Workflow::query()
            ->where('tenant_id', $tenant->id)
            ->where('code', $code)
            ->whereNull('organization_id')
            ->latest('version')
            ->first();

        if ($existing && ! $this->option('replace')) {
            $this->warn("  [skip] {$code} already exists (use --replace to refresh).");

            return;
        }

        if ($existing?->is_active) {
            $lifecycle->archive($existing, $actorId);
        }

        $version = $existing ? ((int) $existing->version + 1) : 1;

        $workflow = Workflow::query()->create([
            'tenant_id' => $tenant->id,
            'organization_id' => null,
            'code' => $code,
            'name' => 'Salary Slip Approval',
            'description' => 'Slip Gaji: HR review → Finance approval.',
            'module' => 'Employee',
            'subject_type' => SalarySlip::class,
            'trigger_mode' => 'manual',
            'version' => $version,
            'status' => WorkflowDefinitionStatus::Draft,
            'is_active' => false,
            'created_by' => $actorId,
            'updated_by' => $actorId,
        ]);

        $approvalActions = [
            ['name' => 'approve', 'label' => 'Approve', 'style' => ['success']],
            ['name' => 'reject', 'label' => 'Reject', 'style' => ['danger']],
        ];

        $hrStep = $this->makeStep($workflow, [
            'code' => 'hr_review',
            'name' => 'HR Review',
            'assignee_user_id' => $hr->id,
            'sort_order' => 1,
            'is_initial' => true,
            'form_schema' => [[
                'name' => 'hr_note',
                'label' => 'Catatan HR',
                'type' => 'textarea',
                'required' => false,
                'column_span' => 'full',
            ]],
            'action_schema' => $approvalActions,
        ]);

        $financeStep = $this->makeStep($workflow, [
            'code' => 'finance_approval',
            'name' => 'Finance Approval',
            'assignee_user_id' => $finance->id,
            'sort_order' => 2,
            'form_schema' => [[
                'name' => 'finance_note',
                'label' => 'Catatan Finance',
                'type' => 'textarea',
                'required' => true,
                'validation' => ['min:5'],
                'column_span' => 'full',
            ]],
            'action_schema' => $approvalActions,
        ]);

        $approvedStep = $this->makeStep($workflow, [
            'code' => 'payable',
            'name' => 'Payable',
            'sort_order' => 3,
            'step_type' => 'end',
            'is_terminal' => true,
            'form_schema' => [],
            'action_schema' => [],
            'assignee_type' => null,
            'assignee_value' => null,
        ]);

        $this->makeTransition($workflow, $hrStep, $financeStep, 'approve', null, 0, true);
        $this->makeTransition($workflow, $hrStep, null, 'reject', null, 0, true);
        $this->makeTransition($workflow, $financeStep, $approvedStep, 'approve', null, 0, true);
        $this->makeTransition($workflow, $financeStep, null, 'reject', null, 0, true);

        $lifecycle->publish($workflow, $actorId);
        $this->line("  ✓ {$code} v{$version} activated.");
    }

    private function resolveUserOption(string $option, bool $required): ?User
    {
        $value = $this->option($option);

        if ($value === null || $value === '') {
            if ($required) {
                $this->fail("Option --{$option} is required.");
            }

            return null;
        }

        return User::query()->findOrFail((int) $value);
    }

    private function assertUserBelongsToTenant(User $user, Tenant $tenant): void
    {
        $isValid = $user->userTenantRoles()
            ->where('tenant_id', $tenant->id)
            ->exists();

        if (! $isValid) {
            $this->fail("User [{$user->email}] is not a member of tenant [{$tenant->name}].");
        }
    }

    private function makeStep(Workflow $workflow, array $attributes): WorkflowStep
    {
        return WorkflowStep::query()->create([
            'workflow_id' => $workflow->id,
            'uuid' => (string) Str::uuid(),
            'code' => $attributes['code'],
            'name' => $attributes['name'],
            'description' => $attributes['description'] ?? null,
            'step_type' => $attributes['step_type'] ?? 'approval',
            'assignee_type' => $attributes['assignee_type'] ?? 'user',
            'assignee_value' => isset($attributes['assignee_user_id'])
                ? (string) $attributes['assignee_user_id']
                : ($attributes['assignee_value'] ?? null),
            'assignee_config' => null,
            'form_schema' => $attributes['form_schema'] ?? [],
            'action_schema' => $attributes['action_schema'] ?? [],
            'sla_hours' => $attributes['sla_hours'] ?? 24,
            'allow_reassign' => true,
            'allow_delegate' => false,
            'is_initial' => $attributes['is_initial'] ?? false,
            'is_terminal' => $attributes['is_terminal'] ?? false,
            'sort_order' => $attributes['sort_order'],
        ]);
    }

    private function makeTransition(
        Workflow $workflow,
        WorkflowStep $fromStep,
        ?WorkflowStep $toStep,
        string $action,
        ?array $rules,
        int $priority = 0,
        bool $isDefault = false,
    ): void {
        WorkflowTransition::query()->create([
            'workflow_id' => $workflow->id,
            'from_step_id' => $fromStep->id,
            'to_step_id' => $toStep?->id,
            'action_name' => $action,
            'rule_type' => 'json_logic',
            'condition_rules' => $rules,
            'priority' => $priority,
            'is_default' => $isDefault,
            'transition_meta' => null,
        ]);
    }
}
