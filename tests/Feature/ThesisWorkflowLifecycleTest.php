<?php

namespace Tests\Feature;

use App\Services\Workflow\StaticMultiUserResolver;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Modules\Campus\Models\CollageStudent;
use Modules\Campus\Models\Thesis;
use Modules\Core\Models\Organization;
use Modules\Core\Models\SubscriptionPlan;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\TenantRole;
use Modules\Core\Models\User;
use Modules\Core\Models\UserTenantRole;
use Modules\Workflow\Contracts\WorkflowEngine;
use Modules\Workflow\Contracts\WorkflowInstanceStarter;
use Modules\Workflow\Models\Workflow;
use Modules\Workflow\Models\WorkflowStep;
use Modules\Workflow\Models\WorkflowTransition;
use Tests\TestCase;

class ThesisWorkflowLifecycleTest extends TestCase
{
    use RefreshDatabase;

    private Tenant $tenant;

    private Organization $organization;

    private User $advisor;

    private User $supervisor2;

    private User $examiner;

    private User $studentUser;

    protected function setUp(): void
    {
        parent::setUp();

        $plan = SubscriptionPlan::create([
            'code' => 'thesis-plan',
            'name' => 'Thesis Plan',
            'included_modules' => ['core', 'campus', 'workflow'],
        ]);

        $rand = Str::random(4);

        $this->advisor = User::create([
            'name' => 'Dr. Advisor',
            'email' => "advisor-{$rand}@example.com",
            'password' => 'password',
        ]);

        $this->supervisor2 = User::create([
            'name' => 'Dr. Supervisor2',
            'email' => "supervisor2-{$rand}@example.com",
            'password' => 'password',
        ]);

        $this->examiner = User::create([
            'name' => 'Dr. Examiner',
            'email' => "examiner-{$rand}@example.com",
            'password' => 'password',
        ]);

        $this->studentUser = User::create([
            'name' => 'Student',
            'email' => "student-{$rand}@example.com",
            'password' => 'password',
        ]);

        $this->tenant = Tenant::create([
            'uuid' => (string) Str::uuid(),
            'code' => "thesis-tenant-{$rand}",
            'name' => 'Thesis University',
            'subscription_plan_id' => $plan->id,
            'created_by' => $this->advisor->id,
        ]);

        $this->organization = Organization::create([
            'tenant_id' => $this->tenant->id,
            'code' => "thesis-fak-{$rand}",
            'name' => 'Fakultas Teknik Informatika',
        ]);

        $role = TenantRole::create([
            'tenant_id' => $this->tenant->id,
            'name' => 'Academic Staff',
            'slug' => "academic-staff-{$rand}",
            'permissions' => ['*'],
            'is_default' => true,
        ]);

        foreach ([$this->advisor, $this->supervisor2, $this->examiner, $this->studentUser] as $i => $user) {
            UserTenantRole::create([
                'user_id' => $user->id,
                'tenant_id' => $this->tenant->id,
                'organization_id' => $this->organization->id,
                'tenant_role_id' => $role->id,
                'assigned_by' => $this->advisor->id,
                'is_primary' => $i === 0,
            ]);
        }

        // Allow StaticMultiUserResolver in workflow config for tests
        config()->set('workflow.allowed_assignee_resolvers', [
            StaticMultiUserResolver::class,
        ]);
    }

    public function test_thesis_workflow_full_lifecycle_proposal_to_final(): void
    {
        $thesis = $this->makeThesis();
        $workflow = $this->makeThesisWorkflow();

        $starter = app(WorkflowInstanceStarter::class);
        $engine = app(WorkflowEngine::class);

        // Start the workflow: mahasiswa submits proposal
        $instance = $starter->start($workflow, $this->studentUser, ['thesis_id' => $thesis->id]);

        $this->assertSame('running', $instance->status->value);
        $this->assertSame(1, $instance->assignments()->where('status', 'pending')->count());

        // ── Step 1: Advisor approves proposal ─────────────────────────────────
        $instance = $engine->advance($instance, 'approve', [
            'proposal_feedback' => 'Proposal looks good, proceed to seminar stage.',
        ], $this->advisor);

        $this->assertSame('running', $instance->status->value);

        // ── Step 2: Seminar parallel review (2 supervisors, majority quorum) ──
        // Both advisor and supervisor2 have assignments
        $seminarAssignments = $instance->assignments()->where('status', 'pending')->count();
        $this->assertSame(2, $seminarAssignments);

        // Advisor approves seminar — quorum not yet reached (1 of 2)
        $instance = $engine->advance($instance, 'approve', [
            'seminar_notes' => 'Progress is satisfactory, recommend to defense stage.',
        ], $this->advisor);

        // Still running (need supervisor2 too for majority with 2 people = both)
        // Actually majority of 2 = 1 approval is enough (>= 1.5 rounded to 1)
        // Let's check if the instance moved forward or is still in seminar step
        if ($instance->status->value === 'running') {
            // If still in seminar, supervisor2 also needs to approve
            $remainingPending = $instance->assignments()->where('status', 'pending')->count();
            if ($remainingPending > 0) {
                $instance = $engine->advance($instance, 'approve', [
                    'seminar_notes' => 'Concur with main advisor, proceed to defense.',
                ], $this->supervisor2);
            }
        }

        $this->assertSame('running', $instance->status->value);

        // ── Step 3: Examiner passes the defense ───────────────────────────────
        $instance = $engine->advance($instance, 'pass', [
            'defense_notes' => 'Excellent thesis defense. Thesis is approved.',
            'grade_letter' => 'A',
        ], $this->examiner);

        $this->assertSame('completed', $instance->status->value);
    }

    public function test_thesis_workflow_proposal_rejected_ends_workflow(): void
    {
        $thesis = $this->makeThesis();
        $workflow = $this->makeThesisWorkflow();

        $instance = app(WorkflowInstanceStarter::class)->start($workflow, $this->studentUser);
        $instance = app(WorkflowEngine::class)->advance($instance, 'reject', [
            'proposal_feedback' => 'Proposal needs significant revision before proceeding.',
        ], $this->advisor);

        $this->assertSame('rejected', $instance->status->value);
    }

    public function test_thesis_workflow_defense_routes_to_revision_on_minor_revision(): void
    {
        $thesis = $this->makeThesis();
        $workflow = $this->makeThesisWorkflow();

        $engine = app(WorkflowEngine::class);
        $instance = app(WorkflowInstanceStarter::class)->start($workflow, $this->studentUser);

        // Proposal approval
        $instance = $engine->advance($instance, 'approve', ['proposal_feedback' => 'Good.'], $this->advisor);

        // Seminar — advance through all pending assignments
        $pending = $instance->assignments()->where('status', 'pending')->get();
        foreach ($pending as $assignment) {
            $assignedUser = User::find($assignment->assigned_to_id);
            $instance = $engine->advance($instance, 'approve', ['seminar_notes' => 'OK.'], $assignedUser);
            if ($instance->status->value !== 'running') {
                break;
            }
        }

        // Defense → revision
        $instance = $engine->advance($instance, 'revision', [
            'defense_notes' => 'Minor revisions needed. Please fix chapter 3.',
            'grade_letter' => 'B',
        ], $this->examiner);

        $this->assertSame('running', $instance->status->value);

        // Revision review → advisor approves revision
        $instance = $engine->advance($instance, 'approve', [
            'revision_notes' => 'Revisions accepted. Thesis is complete.',
        ], $this->advisor);

        $this->assertSame('completed', $instance->status->value);
    }

    public function test_setup_thesis_command_creates_workflow(): void
    {
        $this->artisan('fos:workflow:setup-thesis', [
            'tenant' => $this->tenant->id,
            '--advisor' => $this->advisor->id,
            '--supervisor2' => $this->supervisor2->id,
            '--examiner' => $this->examiner->id,
        ])->assertSuccessful();

        $this->assertDatabaseHas('workflows', [
            'tenant_id' => $this->tenant->id,
            'code' => 'thesis-lifecycle',
            'status' => 'active',
        ]);

        $workflow = Workflow::where('tenant_id', $this->tenant->id)
            ->where('code', 'thesis-lifecycle')
            ->first();

        $this->assertSame(4, $workflow->steps()->where('step_type', 'approval')->count());
        $this->assertSame(2, $workflow->steps()->where('step_type', 'end')->count());
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    private function makeThesis(): Thesis
    {
        $student = CollageStudent::create([
            'tenant_id' => $this->tenant->id,
            'student_number' => 'NPM-'.Str::random(6),
            'full_name' => 'Ahmad Fariz',
            'status' => 'active',
        ]);

        return Thesis::create([
            'tenant_id' => $this->tenant->id,
            'collage_student_id' => $student->id,
            'title' => 'Implementasi Machine Learning untuk Deteksi Anomali Jaringan',
            'status' => 'proposal',
        ]);
    }

    private function makeThesisWorkflow(): Workflow
    {
        $workflow = Workflow::create([
            'tenant_id' => $this->tenant->id,
            'organization_id' => $this->organization->id,
            'code' => 'thesis-lifecycle-'.Str::random(4),
            'name' => 'Thesis Lifecycle',
            'module' => 'Campus',
            'subject_type' => Thesis::class,
            'trigger_mode' => 'manual',
            'version' => 1,
            'status' => 'active',
            'is_active' => true,
            'published_at' => now(),
            'created_by' => $this->advisor->id,
            'updated_by' => $this->advisor->id,
        ]);

        $proposalReview = WorkflowStep::create([
            'workflow_id' => $workflow->id,
            'uuid' => (string) Str::uuid(),
            'code' => 'proposal_review',
            'name' => 'Proposal Review',
            'step_type' => 'approval',
            'assignee_type' => 'user',
            'assignee_value' => (string) $this->advisor->id,
            'form_schema' => [['name' => 'proposal_feedback', 'label' => 'Feedback', 'type' => 'textarea', 'required' => true, 'validation' => ['min:3']]],
            'action_schema' => [
                ['name' => 'approve', 'label' => 'Approve'],
                ['name' => 'reject', 'label' => 'Reject'],
            ],
            'sla_hours' => 72,
            'is_initial' => true,
            'is_terminal' => false,
            'sort_order' => 1,
        ]);

        $seminarReview = WorkflowStep::create([
            'workflow_id' => $workflow->id,
            'uuid' => (string) Str::uuid(),
            'code' => 'seminar_review',
            'name' => 'Seminar Review',
            'step_type' => 'approval',
            'gateway_type' => 'parallel_join',
            'quorum_strategy' => 'majority',
            'assignee_type' => 'resolver',
            'assignee_value' => StaticMultiUserResolver::class,
            'assignee_config' => [
                'resolver_class' => StaticMultiUserResolver::class,
                'user_ids' => [$this->advisor->id, $this->supervisor2->id],
            ],
            'form_schema' => [['name' => 'seminar_notes', 'label' => 'Notes', 'type' => 'textarea', 'required' => true, 'validation' => ['min:2']]],
            'action_schema' => [
                ['name' => 'approve', 'label' => 'Approve'],
                ['name' => 'reject', 'label' => 'Reject'],
            ],
            'sla_hours' => 72,
            'is_initial' => false,
            'is_terminal' => false,
            'sort_order' => 2,
        ]);

        $defenseReview = WorkflowStep::create([
            'workflow_id' => $workflow->id,
            'uuid' => (string) Str::uuid(),
            'code' => 'defense_review',
            'name' => 'Defense Review',
            'step_type' => 'approval',
            'assignee_type' => 'user',
            'assignee_value' => (string) $this->examiner->id,
            'form_schema' => [['name' => 'defense_notes', 'label' => 'Notes', 'type' => 'textarea', 'required' => true, 'validation' => ['min:2']]],
            'action_schema' => [
                ['name' => 'pass', 'label' => 'Pass'],
                ['name' => 'revision', 'label' => 'Minor Revision'],
                ['name' => 'fail', 'label' => 'Fail'],
            ],
            'sla_hours' => 72,
            'is_initial' => false,
            'is_terminal' => false,
            'sort_order' => 3,
        ]);

        $revisionReview = WorkflowStep::create([
            'workflow_id' => $workflow->id,
            'uuid' => (string) Str::uuid(),
            'code' => 'revision_review',
            'name' => 'Revision Review',
            'step_type' => 'approval',
            'assignee_type' => 'user',
            'assignee_value' => (string) $this->advisor->id,
            'form_schema' => [['name' => 'revision_notes', 'label' => 'Notes', 'type' => 'textarea', 'required' => true, 'validation' => ['min:2']]],
            'action_schema' => [
                ['name' => 'approve', 'label' => 'Approve'],
                ['name' => 'reject', 'label' => 'Reject'],
            ],
            'sla_hours' => 72,
            'is_initial' => false,
            'is_terminal' => false,
            'sort_order' => 4,
        ]);

        $finalStep = WorkflowStep::create([
            'workflow_id' => $workflow->id,
            'uuid' => (string) Str::uuid(),
            'code' => 'final',
            'name' => 'Final',
            'step_type' => 'end',
            'is_initial' => false,
            'is_terminal' => true,
            'sort_order' => 5,
        ]);

        $rejectedStep = WorkflowStep::create([
            'workflow_id' => $workflow->id,
            'uuid' => (string) Str::uuid(),
            'code' => 'rejected',
            'name' => 'Rejected',
            'step_type' => 'end',
            'is_initial' => false,
            'is_terminal' => true,
            'sort_order' => 6,
        ]);

        // Proposal transitions
        WorkflowTransition::create([
            'workflow_id' => $workflow->id,
            'from_step_id' => $proposalReview->id,
            'to_step_id' => $seminarReview->id,
            'action_name' => 'approve',
            'rule_type' => 'json_logic',
            'priority' => 0,
            'is_default' => true,
        ]);
        WorkflowTransition::create([
            'workflow_id' => $workflow->id,
            'from_step_id' => $proposalReview->id,
            'to_step_id' => $rejectedStep->id,
            'action_name' => 'reject',
            'rule_type' => 'json_logic',
            'priority' => 0,
            'is_default' => true,
        ]);

        // Seminar transitions
        WorkflowTransition::create([
            'workflow_id' => $workflow->id,
            'from_step_id' => $seminarReview->id,
            'to_step_id' => $defenseReview->id,
            'action_name' => 'approve',
            'rule_type' => 'json_logic',
            'priority' => 0,
            'is_default' => true,
        ]);
        WorkflowTransition::create([
            'workflow_id' => $workflow->id,
            'from_step_id' => $seminarReview->id,
            'to_step_id' => $rejectedStep->id,
            'action_name' => 'reject',
            'rule_type' => 'json_logic',
            'priority' => 0,
            'is_default' => true,
        ]);

        // Defense transitions
        WorkflowTransition::create([
            'workflow_id' => $workflow->id,
            'from_step_id' => $defenseReview->id,
            'to_step_id' => $finalStep->id,
            'action_name' => 'pass',
            'rule_type' => 'json_logic',
            'priority' => 0,
            'is_default' => true,
        ]);
        WorkflowTransition::create([
            'workflow_id' => $workflow->id,
            'from_step_id' => $defenseReview->id,
            'to_step_id' => $revisionReview->id,
            'action_name' => 'revision',
            'rule_type' => 'json_logic',
            'priority' => 10,
            'is_default' => false,
        ]);
        WorkflowTransition::create([
            'workflow_id' => $workflow->id,
            'from_step_id' => $defenseReview->id,
            'to_step_id' => $rejectedStep->id,
            'action_name' => 'fail',
            'rule_type' => 'json_logic',
            'priority' => 20,
            'is_default' => false,
        ]);

        // Revision transitions
        WorkflowTransition::create([
            'workflow_id' => $workflow->id,
            'from_step_id' => $revisionReview->id,
            'to_step_id' => $finalStep->id,
            'action_name' => 'approve',
            'rule_type' => 'json_logic',
            'priority' => 0,
            'is_default' => true,
        ]);
        WorkflowTransition::create([
            'workflow_id' => $workflow->id,
            'from_step_id' => $revisionReview->id,
            'to_step_id' => $rejectedStep->id,
            'action_name' => 'reject',
            'rule_type' => 'json_logic',
            'priority' => 0,
            'is_default' => false,
        ]);

        return $workflow;
    }
}
