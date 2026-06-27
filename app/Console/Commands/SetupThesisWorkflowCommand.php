<?php

namespace App\Console\Commands;

use App\Services\Workflow\StaticMultiUserResolver;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Modules\Campus\Models\Thesis;
use Modules\Core\Models\Organization;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\User;
use Modules\Workflow\Enums\WorkflowDefinitionStatus;
use Modules\Workflow\Models\Workflow;
use Modules\Workflow\Models\WorkflowAutomatedAction;
use Modules\Workflow\Models\WorkflowStep;
use Modules\Workflow\Models\WorkflowTransition;
use Modules\Workflow\Services\WorkflowDefinitionLifecycleService;

class SetupThesisWorkflowCommand extends Command
{
    protected $signature = 'fos:workflow:setup-thesis
        {tenant : Tenant ID}
        {--organization= : Organization ID (optional)}
        {--advisor= : User ID of the main advisor/supervisor (pembimbing utama)}
        {--supervisor2= : User ID of the second supervisor for parallel seminar review}
        {--examiner= : User ID of the thesis examiner (penguji sidang)}
        {--replace : Archive active workflow and create new version}';

    protected $description = 'Create the thesis lifecycle workflow: proposal → seminar → defense → final';

    public function handle(WorkflowDefinitionLifecycleService $lifecycle): int
    {
        $tenant = Tenant::query()->findOrFail((int) $this->argument('tenant'));
        $organizationId = $this->option('organization') !== null ? (int) $this->option('organization') : null;
        $organization = $organizationId
            ? Organization::query()->where('tenant_id', $tenant->id)->findOrFail($organizationId)
            : null;

        $advisor = $this->resolveUserOption('advisor', required: true);
        $supervisor2 = $this->resolveUserOption('supervisor2', required: true);
        $examiner = $this->resolveUserOption('examiner', required: true);

        $actorId = $advisor->id;

        $this->assertUserBelongsToScope($advisor, $tenant, $organization);
        $this->assertUserBelongsToScope($supervisor2, $tenant, $organization);
        $this->assertUserBelongsToScope($examiner, $tenant, $organization);

        $existing = Workflow::query()
            ->where('tenant_id', $tenant->id)
            ->where('code', 'thesis-lifecycle')
            ->when($organization, fn ($q) => $q->where('organization_id', $organization->id), fn ($q) => $q->whereNull('organization_id'))
            ->latest('version')
            ->first();

        if ($existing && ! $this->option('replace')) {
            $this->warn('A thesis lifecycle workflow already exists for this scope.');
            $this->line('Re-run with `--replace` to create a new version.');

            return self::SUCCESS;
        }

        if ($existing && $existing->is_active) {
            $lifecycle->archive($existing, $actorId);
        }

        $version = $existing ? ((int) $existing->version + 1) : 1;

        $workflow = Workflow::query()->create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization?->id,
            'code' => 'thesis-lifecycle',
            'name' => 'Thesis / Tugas Akhir Lifecycle',
            'description' => 'Full thesis lifecycle: proposal review → seminar (parallel) → defense → final.',
            'module' => 'Campus',
            'subject_type' => Thesis::class,
            'trigger_mode' => 'manual',
            'version' => $version,
            'status' => WorkflowDefinitionStatus::Draft,
            'is_active' => false,
            'created_by' => $actorId,
            'updated_by' => $actorId,
        ]);

        // ── Step 1: Proposal Review (sequential, advisor) ─────────────────────
        $proposalReview = $this->makeStep($workflow, [
            'code' => 'proposal_review',
            'name' => 'Proposal Review',
            'description' => 'Advisor reviews and approves the thesis proposal.',
            'assignee_type' => 'user',
            'assignee_value' => (string) $advisor->id,
            'sort_order' => 1,
            'is_initial' => true,
            'form_schema' => [[
                'name' => 'proposal_feedback',
                'label' => 'Proposal Feedback',
                'type' => 'textarea',
                'required' => true,
                'validation' => ['min:20'],
                'placeholder' => 'Provide feedback on the proposal.',
                'column_span' => 'full',
            ]],
            'action_schema' => $this->defaultApprovalActions(),
        ]);

        // ── Step 2: Seminar Review (parallel join, both supervisors) ──────────
        $seminarReview = $this->makeStep($workflow, [
            'code' => 'seminar_review',
            'name' => 'Seminar / Progress Review',
            'description' => 'Both supervisors review progress in parallel. Majority quorum required.',
            'assignee_type' => 'resolver',
            'assignee_value' => StaticMultiUserResolver::class,
            'assignee_config' => [
                'resolver_class' => StaticMultiUserResolver::class,
                'user_ids' => [$advisor->id, $supervisor2->id],
            ],
            'gateway_type' => 'parallel_join',
            'quorum_strategy' => 'majority',
            'sort_order' => 2,
            'form_schema' => [[
                'name' => 'seminar_notes',
                'label' => 'Seminar Review Notes',
                'type' => 'textarea',
                'required' => true,
                'validation' => ['min:10'],
                'column_span' => 'full',
            ]],
            'action_schema' => $this->defaultApprovalActions(),
        ]);

        // ── Step 3: Defense Review (sequential, examiner) ─────────────────────
        $defenseReview = $this->makeStep($workflow, [
            'code' => 'defense_review',
            'name' => 'Defense / Sidang Review',
            'description' => 'Examiner evaluates thesis during sidang.',
            'assignee_type' => 'user',
            'assignee_value' => (string) $examiner->id,
            'sort_order' => 3,
            'form_schema' => [
                [
                    'name' => 'defense_notes',
                    'label' => 'Defense Notes',
                    'type' => 'textarea',
                    'required' => true,
                    'validation' => ['min:10'],
                    'column_span' => 'full',
                ],
                [
                    'name' => 'grade_letter',
                    'label' => 'Grade Letter',
                    'type' => 'select',
                    'required' => false,
                    'options_source' => ['kind' => 'static', 'options' => [
                        ['value' => 'A', 'label' => 'A (Sangat Baik)'],
                        ['value' => 'B', 'label' => 'B (Baik)'],
                        ['value' => 'C', 'label' => 'C (Cukup)'],
                    ]],
                ],
            ],
            'action_schema' => [
                ['name' => 'pass', 'label' => 'Pass', 'style' => ['success']],
                ['name' => 'revision', 'label' => 'Minor Revision', 'style' => ['warning']],
                ['name' => 'fail', 'label' => 'Fail', 'style' => ['danger']],
            ],
        ]);

        // ── Step 4: Revision Review (sequential, advisor) ─────────────────────
        $revisionReview = $this->makeStep($workflow, [
            'code' => 'revision_review',
            'name' => 'Revision Review',
            'description' => 'Advisor reviews and approves post-defense revisions.',
            'assignee_type' => 'user',
            'assignee_value' => (string) $advisor->id,
            'sort_order' => 4,
            'form_schema' => [[
                'name' => 'revision_notes',
                'label' => 'Revision Review Notes',
                'type' => 'textarea',
                'required' => true,
                'validation' => ['min:10'],
                'column_span' => 'full',
            ]],
            'action_schema' => $this->defaultApprovalActions(),
        ]);

        // ── Terminal steps ─────────────────────────────────────────────────────
        $finalStep = $this->makeStep($workflow, [
            'code' => 'final',
            'name' => 'Final / Completed',
            'step_type' => 'end',
            'sort_order' => 5,
            'is_terminal' => true,
            'form_schema' => [],
            'action_schema' => [],
            'assignee_type' => null,
            'assignee_value' => null,
        ]);

        $rejectedStep = $this->makeStep($workflow, [
            'code' => 'rejected',
            'name' => 'Rejected',
            'step_type' => 'end',
            'sort_order' => 6,
            'is_terminal' => true,
            'form_schema' => [],
            'action_schema' => [],
            'assignee_type' => null,
            'assignee_value' => null,
        ]);

        // ── Transitions ────────────────────────────────────────────────────────
        // Proposal → Seminar or Rejected
        $this->makeTransition($workflow, $proposalReview, $seminarReview, 'approve', null, 0, true);
        $this->makeTransition($workflow, $proposalReview, $rejectedStep, 'reject', null, 0, true);

        // Seminar → Defense or Rejected
        $this->makeTransition($workflow, $seminarReview, $defenseReview, 'approve', null, 0, true);
        $this->makeTransition($workflow, $seminarReview, $rejectedStep, 'reject', null, 0, true);

        // Defense → Final, Revision, or Rejected
        $this->makeTransition($workflow, $defenseReview, $finalStep, 'pass', null, 0, true);
        $this->makeTransition($workflow, $defenseReview, $revisionReview, 'revision', null, 10, false);
        $this->makeTransition($workflow, $defenseReview, $rejectedStep, 'fail', null, 20, false);

        // Revision → Final or Rejected
        $this->makeTransition($workflow, $revisionReview, $finalStep, 'approve', null, 0, true);
        $this->makeTransition($workflow, $revisionReview, $rejectedStep, 'reject', null, 0, false);

        // ── Automated actions ──────────────────────────────────────────────────
        WorkflowAutomatedAction::query()->create([
            'workflow_id' => $workflow->id,
            'step_id' => null,
            'trigger_event' => 'started',
            'action_type' => 'audit_note',
            'name' => 'Thesis Workflow Started',
            'config' => ['action' => 'thesis_workflow_started', 'description' => 'Thesis lifecycle workflow started.'],
            'is_active' => true,
            'sort_order' => 1,
        ]);

        WorkflowAutomatedAction::query()->create([
            'workflow_id' => $workflow->id,
            'step_id' => null,
            'trigger_event' => 'completed',
            'action_type' => 'audit_note',
            'name' => 'Thesis Workflow Completed',
            'config' => ['action' => 'thesis_workflow_completed', 'description' => 'Thesis lifecycle workflow completed successfully.'],
            'is_active' => true,
            'sort_order' => 2,
        ]);

        $workflow = $lifecycle->publish($workflow, $actorId);

        $this->info('Thesis lifecycle workflow created successfully.');
        $this->table(
            ['Workflow ID', 'Tenant', 'Organization', 'Version', 'Advisor', 'Supervisor 2', 'Examiner'],
            [[
                $workflow->id,
                $tenant->name,
                $organization->name ?? 'Tenant-wide',
                $workflow->version,
                $advisor->name,
                $supervisor2->name,
                $examiner->name,
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
            'gateway_type' => $attributes['gateway_type'] ?? 'none',
            'quorum_strategy' => $attributes['quorum_strategy'] ?? null,
            'quorum_value' => $attributes['quorum_value'] ?? null,
            'assignee_type' => $attributes['assignee_type'] ?? 'user',
            'assignee_value' => $attributes['assignee_value'] ?? null,
            'assignee_config' => $attributes['assignee_config'] ?? null,
            'form_schema' => $attributes['form_schema'] ?? [[
                'name' => 'note',
                'label' => 'Note',
                'type' => 'textarea',
                'required' => true,
                'validation' => ['min:10'],
                'column_span' => 'full',
            ]],
            'action_schema' => $attributes['action_schema'] ?? [],
            'sla_hours' => $attributes['sla_hours'] ?? 72,
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
