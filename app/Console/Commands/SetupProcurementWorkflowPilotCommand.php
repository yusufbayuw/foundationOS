<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Modules\Core\Models\Organization;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\User;
use Modules\Procurement\Models\PurchaseRequisition;
use Modules\Workflow\Enums\WorkflowDefinitionStatus;
use Modules\Workflow\Models\Workflow;
use Modules\Workflow\Models\WorkflowAutomatedAction;
use Modules\Workflow\Models\WorkflowStep;
use Modules\Workflow\Models\WorkflowTransition;
use Modules\Workflow\Services\WorkflowDefinitionLifecycleService;

class SetupProcurementWorkflowPilotCommand extends Command
{
    protected $signature = 'fos:workflow:setup-procurement-pilot
        {tenant : Tenant ID}
        {--organization= : Organization ID (opsional)}
        {--manager= : User ID approver manager}
        {--finance= : User ID approver finance}
        {--executive= : User ID approver executive (opsional)}
        {--finance-threshold=10000000 : Threshold amount untuk step finance}
        {--executive-threshold=50000000 : Threshold amount untuk step executive}
        {--replace : Archive active workflow sebelumnya dan buat versi baru}';

    protected $description = 'Create or refresh the tenant-scoped procurement approval pilot workflow';

    public function handle(WorkflowDefinitionLifecycleService $lifecycle): int
    {
        $tenant = Tenant::query()->findOrFail((int) $this->argument('tenant'));
        $organizationId = $this->option('organization') !== null ? (int) $this->option('organization') : null;
        $organization = $organizationId ? Organization::query()->where('tenant_id', $tenant->id)->findOrFail($organizationId) : null;

        $manager = $this->resolveUserOption('manager', required: true);
        $finance = $this->resolveUserOption('finance', required: true);
        $executive = $this->resolveUserOption('executive', required: false);

        $financeThreshold = (float) $this->option('finance-threshold');
        $executiveThreshold = (float) $this->option('executive-threshold');
        $actorId = (int) ($manager->id);

        $this->assertUserBelongsToScope($manager, $tenant, $organization);
        $this->assertUserBelongsToScope($finance, $tenant, $organization);

        if ($executive) {
            $this->assertUserBelongsToScope($executive, $tenant, $organization);
        }

        if ($executive && $executiveThreshold < $financeThreshold) {
            $this->error('Executive threshold must be greater than or equal to finance threshold.');

            return self::FAILURE;
        }

        $existing = Workflow::query()
            ->where('tenant_id', $tenant->id)
            ->where('code', 'purchase-requisition-approval')
            ->when($organization, fn ($query) => $query->where($query->getModel()->qualifyColumn('organization_id'), $organization->id), fn ($query) => $query->whereNull('organization_id'))
            ->latest('version')
            ->first();

        if ($existing && ! $this->option('replace')) {
            $this->warn('A procurement pilot workflow already exists for this scope.');
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
            'code' => 'purchase-requisition-approval',
            'name' => 'Purchase Requisition Approval',
            'description' => 'Default amount-based approval workflow for procurement purchase requisitions.',
            'module' => 'Procurement',
            'subject_type' => PurchaseRequisition::class,
            'trigger_mode' => 'manual',
            'version' => $version,
            'status' => WorkflowDefinitionStatus::Draft,
            'is_active' => false,
            'created_by' => $actorId,
            'updated_by' => $actorId,
        ]);

        $managerStep = $this->makeStep($workflow, [
            'code' => 'manager_approval',
            'name' => 'Manager Approval',
            'assignee_user_id' => $manager->id,
            'sort_order' => 1,
            'is_initial' => true,
            'action_schema' => $this->defaultApprovalActions(),
        ]);

        $financeStep = $this->makeStep($workflow, [
            'code' => 'finance_approval',
            'name' => 'Finance Approval',
            'assignee_user_id' => $finance->id,
            'sort_order' => 2,
            'action_schema' => $this->defaultApprovalActions(),
        ]);

        $executiveStep = $executive ? $this->makeStep($workflow, [
            'code' => 'executive_approval',
            'name' => 'Executive Approval',
            'assignee_user_id' => $executive->id,
            'sort_order' => 3,
            'action_schema' => $this->defaultApprovalActions(),
        ]) : null;

        $completedStep = $this->makeStep($workflow, [
            'code' => 'approved',
            'name' => 'Approved',
            'sort_order' => $executive ? 4 : 3,
            'step_type' => 'end',
            'is_terminal' => true,
            'form_schema' => [],
            'action_schema' => [],
            'assignee_type' => null,
            'assignee_value' => null,
        ]);

        $this->makeTransition($workflow, $managerStep, $financeStep, 'approve', [
            '>=' => [
                ['var' => 'total_estimated_amount'],
                $financeThreshold,
            ],
        ], 20);
        $this->makeTransition($workflow, $managerStep, $completedStep, 'approve', null, 0, true);
        $this->makeTransition($workflow, $managerStep, null, 'reject', null, 0, true);

        if ($executiveStep) {
            $this->makeTransition($workflow, $financeStep, $executiveStep, 'approve', [
                '>=' => [
                    ['var' => 'total_estimated_amount'],
                    $executiveThreshold,
                ],
            ], 20);
            $this->makeTransition($workflow, $financeStep, $completedStep, 'approve', null, 0, true);
        } else {
            $this->makeTransition($workflow, $financeStep, $completedStep, 'approve', null, 0, true);
        }

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
                'action' => 'procurement_workflow_started',
                'description' => 'Procurement approval workflow started.',
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
                'action' => 'procurement_workflow_completed',
                'description' => 'Procurement approval workflow completed.',
            ],
            'is_active' => true,
            'sort_order' => 2,
        ]);

        $workflow = $lifecycle->publish($workflow, $actorId);

        $this->info('Procurement pilot workflow created successfully.');
        $this->table(
            ['Workflow ID', 'Tenant', 'Organization', 'Version', 'Manager', 'Finance', 'Executive'],
            [[
                $workflow->id,
                $tenant->name,
                $organization->name ?? 'Tenant-wide',
                $workflow->version,
                $manager->name,
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
                    $inner->where($inner->getModel()->qualifyColumn('organization_id'), $organization->id)
                        ->orWhereNull('organization_id');
                });
            })
            ->exists();

        if (! $isValid) {
            $scope = $organization->name ?? 'tenant-wide scope';
            $this->fail("User [{$user->email}] is not a member of tenant [{$tenant->name}] for scope [{$scope}].");
        }
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
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
                'placeholder' => 'Explain the approval decision.',
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

    /**
     * @param  array<string, mixed>  $rules
     */
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

    /**
     * @return list<array<string, list<string>|string>>
     */
    protected function defaultApprovalActions(): array
    {
        return [
            ['name' => 'approve', 'label' => 'Approve', 'style' => ['success']],
            ['name' => 'reject', 'label' => 'Reject', 'style' => ['danger']],
        ];
    }
}
