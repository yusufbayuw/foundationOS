<?php

namespace Modules\Workflow\Console\Commands;

use App\Support\TypedValue;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\User;
use Modules\Procurement\Models\PurchaseOrder;
use Modules\Workflow\Enums\WorkflowDefinitionStatus;
use Modules\Workflow\Models\Workflow;
use Modules\Workflow\Models\WorkflowStep;
use Modules\Workflow\Models\WorkflowTransition;
use Modules\Workflow\Services\WorkflowDefinitionLifecycleService;

/**
 * Seeds default Procurement approval workflows with nominal limits:
 *  - po-approval-limit : PO > 10 jt → Direktur; PO > 50 jt → Direktur + Komisaris
 */
class SetupApprovalLimitsCommand extends Command
{
    protected $signature = 'fos:workflow:setup-approval-limits
        {tenant : Tenant ID}
        {--manager= : User ID Manager (approver step 1)}
        {--direktur= : User ID Direktur (approver step 2)}
        {--komisaris= : User ID Komisaris (approver step 3)}
        {--replace : Archive active workflow and create new version}';

    protected $description = 'Seed default Workflow Approval Limits (PO > 10jt -> Direktur, > 50jt -> Direktur + Komisaris)';

    public function handle(WorkflowDefinitionLifecycleService $lifecycle): int
    {
        $tenant = Tenant::query()->findOrFail((int) $this->argument('tenant'));

        $manager = TypedValue::model($this->resolveUserOption('manager', required: true));
        $direktur = TypedValue::model($this->resolveUserOption('direktur', required: true));
        $komisaris = TypedValue::model($this->resolveUserOption('komisaris', required: true));

        $actorId = $manager->id;

        $this->assertUserBelongsToTenant($manager, $tenant);
        $this->assertUserBelongsToTenant($direktur, $tenant);
        $this->assertUserBelongsToTenant($komisaris, $tenant);

        $this->info("Setting up Approval Limit workflows for tenant [{$tenant->name}]...");

        $this->setupPurchaseOrderWorkflow($tenant, $manager, $direktur, $komisaris, $lifecycle, $actorId);

        $this->info('Done. Approval Limit workflows activated.');

        return self::SUCCESS;
    }

    private function setupPurchaseOrderWorkflow(
        Tenant $tenant,
        User $manager,
        User $direktur,
        User $komisaris,
        WorkflowDefinitionLifecycleService $lifecycle,
        int $actorId,
    ): void {
        $code = 'po-approval-limit';

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
            'name' => 'Purchase Order Approval (Limit)',
            'description' => 'PO > 10 jt: Direktur. PO > 50 jt: Direktur + Komisaris.',
            'module' => 'Procurement',
            'subject_type' => PurchaseOrder::class,
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

        $direkturStep = $this->makeStep($workflow, [
            'code' => 'direktur_approval',
            'name' => 'Direktur Approval',
            'assignee_user_id' => $direktur->id,
            'sort_order' => 2,
            'form_schema' => $formSchema,
            'action_schema' => $approvalActions,
        ]);

        $komisarisStep = $this->makeStep($workflow, [
            'code' => 'komisaris_approval',
            'name' => 'Komisaris Approval',
            'assignee_user_id' => $komisaris->id,
            'sort_order' => 3,
            'form_schema' => $formSchema,
            'action_schema' => $approvalActions,
        ]);

        $approvedStep = $this->makeStep($workflow, [
            'code' => 'approved',
            'name' => 'Approved',
            'sort_order' => 4,
            'step_type' => 'end',
            'is_terminal' => true,
            'form_schema' => [],
            'action_schema' => [],
            'assignee_type' => null,
            'assignee_value' => null,
        ]);

        // Manager Approval Transitions
        // If PO > 50 jt -> Direktur (because it still needs Direktur first, then Komisaris)
        // If PO > 10 jt -> Direktur
        // So anything > 10 jt goes to Direktur!
        $this->makeTransition($workflow, $managerStep, $direkturStep, 'approve', [
            '>' => [['var' => 'total_amount'], 10000000],
        ], 10);
        // Default (PO <= 10 jt) -> Approved
        $this->makeTransition($workflow, $managerStep, $approvedStep, 'approve', null, 0, true);
        $this->makeTransition($workflow, $managerStep, null, 'reject', null, 0, true);

        // Direktur Approval Transitions
        // If PO > 50 jt -> Komisaris
        $this->makeTransition($workflow, $direkturStep, $komisarisStep, 'approve', [
            '>' => [['var' => 'total_amount'], 50000000],
        ], 10);
        // Default (10 jt < PO <= 50 jt) -> Approved
        $this->makeTransition($workflow, $direkturStep, $approvedStep, 'approve', null, 0, true);
        $this->makeTransition($workflow, $direkturStep, null, 'reject', null, 0, true);

        // Komisaris Approval Transitions
        $this->makeTransition($workflow, $komisarisStep, $approvedStep, 'approve', null, 0, true);
        $this->makeTransition($workflow, $komisarisStep, null, 'reject', null, 0, true);

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

    /**
     * @param  array<string, mixed>  $attributes
     */
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
                ? TypedValue::string($attributes['assignee_user_id'])
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

    /**
     * @param  array<string, mixed>  $rules
     */
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
