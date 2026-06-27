<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Modules\Core\Models\Organization;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\User;
use Modules\Finance\Models\Budget;
use Modules\Workflow\Enums\WorkflowDefinitionStatus;
use Modules\Workflow\Models\Workflow;
use Modules\Workflow\Models\WorkflowAutomatedAction;
use Modules\Workflow\Models\WorkflowStep;
use Modules\Workflow\Models\WorkflowTransition;
use Modules\Workflow\Services\WorkflowDefinitionLifecycleService;

class SetupBudgetWorkflowCommand extends Command
{
    protected $signature = 'fos:workflow:setup-budget-workflow
        {tenant : Tenant ID}
        {--organization= : Organization ID}
        {--finance= : User ID approver finance}
        {--executive= : User ID approver executive (optional)}
        {--executive-threshold=50000000 : Threshold amount for executive approval}
        {--replace : Archive active workflow and create a new version}';

    protected $description = 'Create or refresh the tenant-scoped budget approval workflow';

    public function handle(WorkflowDefinitionLifecycleService $lifecycle): int
    {
        $tenant = Tenant::query()->findOrFail((int) $this->argument('tenant'));
        $organizationId = $this->option('organization') !== null ? (int) $this->option('organization') : null;
        $organization = $organizationId ? Organization::query()->where('tenant_id', $tenant->id)->findOrFail($organizationId) : null;

        $finance = $this->resolveUserOption('finance', required: true);
        $executive = $this->resolveUserOption('executive', required: false);
        $executiveThreshold = (float) $this->option('executive-threshold');
        $actorId = (int) $finance->id;

        $this->assertUserBelongsToScope($finance, $tenant, $organization);

        if ($executive) {
            $this->assertUserBelongsToScope($executive, $tenant, $organization);
        }

        $existing = Workflow::query()
            ->where('tenant_id', $tenant->id)
            ->where('code', 'budget-approval')
            ->where('organization_id', $organization?->id)
            ->latest('version')
            ->first();

        if ($existing && ! $this->option('replace')) {
            $this->warn('A budget approval workflow already exists for this scope.');
            $this->line('Re-run with `--replace` if you want a new version.');

            return self::SUCCESS;
        }

        if ($existing && $existing->is_active) {
            $lifecycle->archive($existing, $actorId);
        }

        $version = $existing ? ((int) $existing->version + 1) : 1;

        $workflow = Workflow::query()->create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization?->id,
            'code' => 'budget-approval',
            'name' => 'Budget Approval',
            'description' => 'Default budget approval workflow for Finance budgets.',
            'module' => 'Finance',
            'subject_type' => Budget::class,
            'trigger_mode' => 'manual',
            'version' => $version,
            'status' => WorkflowDefinitionStatus::Draft,
            'is_active' => false,
            'created_by' => $actorId,
            'updated_by' => $actorId,
        ]);

        $financeStep = $this->makeStep($workflow, [
            'code' => 'finance_approval',
            'name' => 'Finance Approval',
            'assignee_user_id' => $finance->id,
            'sort_order' => 1,
            'is_initial' => true,
            'action_schema' => $this->defaultApprovalActions(),
        ]);

        $executiveStep = $executive ? $this->makeStep($workflow, [
            'code' => 'executive_approval',
            'name' => 'Executive Approval',
            'assignee_user_id' => $executive->id,
            'sort_order' => 2,
            'action_schema' => $this->defaultApprovalActions(),
        ]) : null;

        $completedStep = $this->makeStep($workflow, [
            'code' => 'approved',
            'name' => 'Approved',
            'sort_order' => $executive ? 3 : 2,
            'step_type' => 'end',
            'is_terminal' => true,
            'form_schema' => [],
            'action_schema' => [],
            'assignee_type' => null,
            'assignee_value' => null,
        ]);

        if ($executiveStep) {
            $this->makeTransition($workflow, $financeStep, $executiveStep, 'approve', [
                '>=' => [
                    ['var' => 'allocated_amount'],
                    $executiveThreshold,
                ],
            ], 20);
        }

        $this->makeTransition($workflow, $financeStep, $completedStep, 'approve', null, 0, true);
        $this->makeTransition($workflow, $financeStep, null, 'reject', null, 0, true);

        if ($executiveStep) {
            $this->makeTransition($workflow, $executiveStep, $completedStep, 'approve', null, 0, true);
            $this->makeTransition($workflow, $executiveStep, null, 'reject', null, 0, true);
        }

        WorkflowAutomatedAction::query()->create([
            'workflow_id' => $workflow->id,
            'step_id' => null,
            'trigger_event' => 'started',
            'action_type' => 'audit_note',
            'name' => 'Audit Start',
            'config' => [
                'action' => 'finance_budget_workflow_started',
                'description' => 'Budget approval workflow started.',
            ],
            'is_active' => true,
            'sort_order' => 1,
        ]);

        WorkflowAutomatedAction::query()->create([
            'workflow_id' => $workflow->id,
            'step_id' => null,
            'trigger_event' => 'completed',
            'action_type' => 'audit_note',
            'name' => 'Audit Complete',
            'config' => [
                'action' => 'finance_budget_workflow_completed',
                'description' => 'Budget approval workflow completed.',
            ],
            'is_active' => true,
            'sort_order' => 2,
        ]);

        $workflow = $lifecycle->publish($workflow, $actorId);

        $this->info('Budget approval workflow created successfully.');
        $this->table(
            ['Workflow ID', 'Tenant', 'Organization', 'Version', 'Finance', 'Executive'],
            [[
                $workflow->id,
                $tenant->name,
                $organization->name ?? '-',
                $workflow->version,
                $finance->name,
                $executive->name ?? '-',
            ]]
        );

        return self::SUCCESS;
    }

    protected function resolveUserOption(string $option, bool $required): ?User
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

    protected function assertUserBelongsToScope(User $user, Tenant $tenant, ?Organization $organization): void
    {
        $isValid = $user->userTenantRoles()
            ->where('tenant_id', $tenant->id)
            ->when($organization, function ($query) use ($organization): void {
                $query->where(function ($inner) use ($organization): void {
                    $inner->where('organization_id', $organization->id)
                        ->orWhereNull('organization_id');
                });
            })
            ->exists();

        if (! $isValid) {
            $scope = $organization->name ?? 'tenant-wide scope';
            $this->fail("User [{$user->email}] is not a member of tenant [{$tenant->name}] for scope [{$scope}].");
        }
    }

    protected function makeStep(Workflow $workflow, array $attributes): WorkflowStep
    {
        return WorkflowStep::query()->create([
            'workflow_id' => $workflow->id,
            'uuid' => (string) Str::uuid(),
            'code' => $attributes['code'],
            'name' => $attributes['name'],
            'description' => $attributes['description'] ?? null,
            'step_type' => $attributes['step_type'] ?? 'approval',
            'assignee_type' => $attributes['assignee_type'] ?? 'user',
            'assignee_value' => isset($attributes['assignee_user_id']) ? (string) $attributes['assignee_user_id'] : ($attributes['assignee_value'] ?? null),
            'assignee_config' => $attributes['assignee_config'] ?? null,
            'form_schema' => $attributes['form_schema'] ?? [[
                'name' => 'approval_note',
                'label' => 'Approval Note',
                'type' => 'textarea',
                'required' => true,
                'validation' => ['min:10'],
                'placeholder' => 'Explain the budget approval decision.',
                'help_text' => 'This note is stored in the workflow audit log.',
                'column_span' => 'full',
            ]],
            'action_schema' => $attributes['action_schema'] ?? [],
            'sla_hours' => $attributes['sla_hours'] ?? 24,
            'allow_reassign' => $attributes['allow_reassign'] ?? true,
            'allow_delegate' => $attributes['allow_delegate'] ?? false,
            'is_initial' => $attributes['is_initial'] ?? false,
            'is_terminal' => $attributes['is_terminal'] ?? false,
            'sort_order' => $attributes['sort_order'],
        ]);
    }

    protected function makeTransition(
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

    protected function defaultApprovalActions(): array
    {
        return [
            ['name' => 'approve', 'label' => 'Approve', 'style' => ['success']],
            ['name' => 'reject', 'label' => 'Reject', 'style' => ['danger']],
        ];
    }
}
