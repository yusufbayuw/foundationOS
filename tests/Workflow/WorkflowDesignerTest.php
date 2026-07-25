<?php

namespace Tests\Workflow;

use App\Support\CurrentTenant;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Modules\Core\Models\SubscriptionPlan;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\User;
use Modules\Workflow\Enums\WorkflowDefinitionStatus;
use Modules\Workflow\Livewire\WorkflowCanvas;
use Modules\Workflow\Models\Workflow;
use Modules\Workflow\Models\WorkflowStep;
use Modules\Workflow\Models\WorkflowTransition;
use Tests\TestCase;

class WorkflowDesignerTest extends TestCase
{
    use LazilyRefreshDatabase;

    private Tenant $tenant;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $plan = SubscriptionPlan::create([
            'code' => 'designer-plan',
            'name' => 'Designer Plan',
            'included_modules' => ['core', 'workflow'],
        ]);

        $this->user = User::create([
            'name' => 'Designer User',
            'email' => 'designer@example.com',
            'password' => 'password',
        ]);

        $this->tenant = Tenant::create([
            'uuid' => (string) Str::uuid(),
            'code' => 'designer-tenant',
            'name' => 'Designer Tenant',
            'subscription_plan_id' => $plan->id,
            'created_by' => $this->user->id,
        ]);

        app(CurrentTenant::class)->set($this->tenant);
        $this->actingAs($this->user);
    }

    protected function tearDown(): void
    {
        app(CurrentTenant::class)->forget();
        parent::tearDown();
    }

    private function makeWorkflow(string $code = 'test_designer_wf', string $status = 'draft'): Workflow
    {
        $workflow = Workflow::create([
            'tenant_id' => $this->tenant->id,
            'code' => $code,
            'name' => 'Designer Test Workflow',
            'version' => 1,
            'status' => $status === 'active' ? WorkflowDefinitionStatus::Active : WorkflowDefinitionStatus::Draft,
            'is_active' => $status === 'active',
        ]);

        $start = WorkflowStep::create([
            'workflow_id' => $workflow->id,
            'uuid' => (string) Str::uuid(),
            'code' => 'start',
            'name' => 'Start',
            'step_type' => 'start',
            'gateway_type' => 'none',
            'assignee_type' => 'user',
            'is_initial' => true,
            'is_terminal' => false,
            'sort_order' => 1,
        ]);

        $end = WorkflowStep::create([
            'workflow_id' => $workflow->id,
            'uuid' => (string) Str::uuid(),
            'code' => 'end',
            'name' => 'End',
            'step_type' => 'end',
            'gateway_type' => 'none',
            'assignee_type' => 'user',
            'is_initial' => false,
            'is_terminal' => true,
            'sort_order' => 2,
        ]);

        WorkflowTransition::create([
            'workflow_id' => $workflow->id,
            'from_step_id' => $start->id,
            'to_step_id' => $end->id,
            'action_name' => 'finish',
            'priority' => 1,
            'is_default' => true,
        ]);

        return $workflow->fresh(['steps', 'transitions']);
    }

    private function stepPayload(string $uuid, string $code, string $name, string $type = 'task', bool $initial = false, bool $terminal = false): array
    {
        return [
            'uuid' => $uuid,
            'code' => $code,
            'name' => $name,
            'step_type' => $type,
            'gateway_type' => 'none',
            'assignee_type' => 'user',
            'assignee_value' => '',
            'assignee_config' => [],
            'form_schema' => [],
            'quorum_strategy' => null,
            'quorum_value' => null,
            'sla_hours' => null,
            'description' => '',
            'is_initial' => $initial,
            'is_terminal' => $terminal,
            'sort_order' => 0,
            'canvas_position' => null,
        ];
    }

    // ─── AC5: saveDraft persists to DB ────────────────────────────────────────

    public function test_save_draft_creates_new_workflow_with_steps(): void
    {
        $startUuid = (string) Str::uuid();
        $endUuid = (string) Str::uuid();

        Livewire::test(WorkflowCanvas::class)
            ->set('workflowCode', 'livewire_test_wf')
            ->set('workflowName', 'Livewire Test Workflow')
            ->set('steps', [
                $this->stepPayload($startUuid, 'start', 'Start', 'start', true, false),
                $this->stepPayload($endUuid, 'end', 'End', 'end', false, true),
            ])
            ->set('transitions', [
                ['from_uuid' => $startUuid, 'to_uuid' => $endUuid, 'action_name' => 'proceed', 'priority' => 0, 'is_default' => true, 'condition_rules' => []],
            ])
            ->callAction('saveDraftAction');

        $workflow = Workflow::where('code', 'livewire_test_wf')
            ->where('tenant_id', $this->tenant->id)
            ->first();

        $this->assertNotNull($workflow);
        $this->assertSame(WorkflowDefinitionStatus::Draft, $workflow->status);
        $this->assertCount(2, $workflow->steps);
        $this->assertCount(1, $workflow->transitions);
    }

    public function test_save_draft_updates_existing_draft_in_place(): void
    {
        $workflow = $this->makeWorkflow();

        Livewire::test(WorkflowCanvas::class, ['workflowId' => $workflow->id])
            ->set('workflowName', 'Updated Name')
            ->call('saveDraft');

        $workflow->refresh();
        $this->assertSame('Updated Name', $workflow->name);
        $this->assertSame(WorkflowDefinitionStatus::Draft, $workflow->status);
    }

    // ─── AC6: versioning — editing active workflow creates new draft ──────────

    public function test_save_draft_on_active_workflow_creates_new_draft_version(): void
    {
        $activeWorkflow = $this->makeWorkflow('active_versioning_wf', 'active');

        Livewire::test(WorkflowCanvas::class, ['workflowId' => $activeWorkflow->id])
            ->set('workflowName', 'Modified Name')
            ->call('saveDraft');

        // Active version must remain active
        $activeWorkflow->refresh();
        $this->assertSame(WorkflowDefinitionStatus::Active, $activeWorkflow->status);
        $this->assertTrue($activeWorkflow->is_active);

        // A new draft version should exist
        $draftVersion = Workflow::where('code', 'active_versioning_wf')
            ->where('status', WorkflowDefinitionStatus::Draft)
            ->where('tenant_id', $this->tenant->id)
            ->first();

        $this->assertNotNull($draftVersion);
        $this->assertGreaterThan($activeWorkflow->version, $draftVersion->version);
    }

    // ─── AC2: draft isolation ─────────────────────────────────────────────────

    public function test_saving_draft_does_not_affect_active_workflow(): void
    {
        $active = $this->makeWorkflow('draft_isolation_wf', 'active');
        $originalStepCount = $active->steps()->count();

        Livewire::test(WorkflowCanvas::class, ['workflowId' => $active->id])
            ->call('saveDraft');

        $active->refresh();
        $this->assertSame(WorkflowDefinitionStatus::Active, $active->status);
        $this->assertCount($originalStepCount, $active->steps);
    }

    public function test_publish_action_keeps_draft_when_configuration_is_invalid(): void
    {
        $workflow = Workflow::create([
            'tenant_id' => $this->tenant->id,
            'code' => 'invalid_publish_wf',
            'name' => 'Invalid Publish Workflow',
            'version' => 1,
            'status' => WorkflowDefinitionStatus::Draft,
            'is_active' => false,
        ]);

        WorkflowStep::create([
            'workflow_id' => $workflow->id,
            'uuid' => (string) Str::uuid(),
            'code' => 'only_step',
            'name' => 'Only Step',
            'step_type' => 'task',
            'gateway_type' => 'none',
            'assignee_type' => 'user',
            'is_initial' => false,
            'is_terminal' => false,
            'sort_order' => 1,
        ]);

        Livewire::test(WorkflowCanvas::class, ['workflowId' => $workflow->id])
            ->callAction('publishAction')
            ->assertSet('workflowStatus', 'draft');

        $workflow->refresh();

        $this->assertSame(WorkflowDefinitionStatus::Draft, $workflow->status);
        $this->assertFalse($workflow->is_active);
    }

    // ─── addStep / deleteStep ────────────────────────────────────────────────

    public function test_add_step_appends_to_steps_array(): void
    {
        Livewire::test(WorkflowCanvas::class)
            ->call('addStep', ['name' => 'Review', 'code' => 'review', 'step_type' => 'approval'])
            ->assertSet('isDirty', true)
            ->assertCount('steps', 1);
    }

    public function test_delete_step_removes_step_and_its_transitions(): void
    {
        $uuid1 = (string) Str::uuid();
        $uuid2 = (string) Str::uuid();

        Livewire::test(WorkflowCanvas::class)
            ->set('steps', [
                $this->stepPayload($uuid1, 's1', 'Step 1', 'start', true, false),
                $this->stepPayload($uuid2, 's2', 'Step 2', 'end', false, true),
            ])
            ->set('transitions', [
                ['from_uuid' => $uuid1, 'to_uuid' => $uuid2, 'action_name' => 'proceed', 'priority' => 0, 'is_default' => true, 'condition_rules' => []],
            ])
            ->mountAction('deleteStepAction', ['uuid' => $uuid1])
            ->assertCount('steps', 2)
            ->callMountedAction()
            ->assertCount('steps', 1)
            ->assertCount('transitions', 0);
    }

    public function test_update_step_position_stores_canvas_coordinates(): void
    {
        $uuid = (string) Str::uuid();

        Livewire::test(WorkflowCanvas::class)
            ->set('steps', [$this->stepPayload($uuid, 's1', 'Step 1', 'start', true, false)])
            ->call('updateStepPosition', $uuid, 250.0, 350.0)
            ->assertSet('isDirty', true);
    }

    // ─── Mount loads workflow ────────────────────────────────────────────────

    public function test_mount_loads_existing_workflow(): void
    {
        $workflow = $this->makeWorkflow();

        Livewire::test(WorkflowCanvas::class, ['workflowId' => $workflow->id])
            ->assertSet('workflowCode', 'test_designer_wf')
            ->assertCount('steps', 2)
            ->assertCount('transitions', 1);
    }
}
