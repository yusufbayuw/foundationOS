<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Modules\Workflow\Enums\WorkflowDefinitionStatus;
use Modules\Workflow\Livewire\WorkflowCanvas;
use Modules\Workflow\Models\Workflow;
use Tests\Concerns\CreatesWorkflowDesignerContext;
use Tests\TestCase;

class WorkflowDesignerSavesValidDefinitionTest extends TestCase
{
    use CreatesWorkflowDesignerContext;
    use LazilyRefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpWorkflowDesignerContext();
    }

    protected function tearDown(): void
    {
        $this->tearDownWorkflowDesignerContext();
        parent::tearDown();
    }

    public function test_save_draft_persists_three_steps_and_two_transitions(): void
    {
        $startUuid = (string) Str::uuid();
        $reviewUuid = (string) Str::uuid();
        $endUuid = (string) Str::uuid();

        Livewire::test(WorkflowCanvas::class)
            ->set('workflowCode', 'ac5_three_step_wf')
            ->set('workflowName', 'AC5 Three Step Workflow')
            ->set('steps', [
                $this->designerStepPayload($startUuid, 'start', 'Start', 'start', 'none', true, false),
                $this->designerStepPayload($reviewUuid, 'review', 'Review', 'approval'),
                $this->designerStepPayload($endUuid, 'end', 'End', 'end', 'none', false, true),
            ])
            ->set('transitions', [
                ['from_uuid' => $startUuid, 'to_uuid' => $reviewUuid, 'action_name' => 'submit', 'priority' => 0, 'is_default' => true, 'condition_rules' => []],
                ['from_uuid' => $reviewUuid, 'to_uuid' => $endUuid, 'action_name' => 'approve', 'priority' => 1, 'is_default' => true, 'condition_rules' => []],
            ])
            ->call('saveDraft');

        $workflow = Workflow::query()
            ->where('code', 'ac5_three_step_wf')
            ->where('tenant_id', $this->designerTenant->id)
            ->first();

        $this->assertNotNull($workflow);
        $this->assertSame(WorkflowDefinitionStatus::Draft, $workflow->status);
        $this->assertCount(3, $workflow->steps);
        $this->assertCount(2, $workflow->transitions);
    }
}
