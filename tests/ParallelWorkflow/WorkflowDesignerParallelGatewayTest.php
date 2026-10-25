<?php

namespace Tests\ParallelWorkflow;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Modules\Workflow\Enums\WorkflowDefinitionStatus;
use Modules\Workflow\Livewire\WorkflowCanvas;
use Modules\Workflow\Models\Workflow;
use Tests\Concerns\CreatesWorkflowDesignerContext;
use Tests\TestCase;

class WorkflowDesignerParallelGatewayTest extends TestCase
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

    public function test_save_draft_persists_five_steps_with_two_parallel_gateways(): void
    {
        $startUuid = (string) Str::uuid();
        $splitUuid = (string) Str::uuid();
        $financeUuid = (string) Str::uuid();
        $legalUuid = (string) Str::uuid();
        $joinUuid = (string) Str::uuid();
        $endUuid = (string) Str::uuid();

        Livewire::test(WorkflowCanvas::class)
            ->set('workflowCode', 'ac1_parallel_wf')
            ->set('workflowName', 'Parallel Approval Workflow')
            ->set('steps', [
                $this->designerStepPayload($startUuid, 'start', 'Start', 'start', 'none', true, false),
                $this->designerStepPayload($splitUuid, 'split', 'Parallel Split', 'gateway', 'parallel_split'),
                $this->designerStepPayload($financeUuid, 'finance', 'Finance Review', 'approval'),
                $this->designerStepPayload($legalUuid, 'legal', 'Legal Review', 'approval'),
                $this->designerStepPayload($joinUuid, 'join', 'Parallel Join', 'gateway', 'parallel_join', false, false, 'majority', 2),
                $this->designerStepPayload($endUuid, 'end', 'End', 'end', 'none', false, true),
            ])
            ->set('transitions', [
                ['from_uuid' => $startUuid, 'to_uuid' => $splitUuid, 'action_name' => 'start', 'priority' => 0, 'is_default' => true, 'condition_rules' => []],
                ['from_uuid' => $splitUuid, 'to_uuid' => $financeUuid, 'action_name' => 'to_finance', 'priority' => 0, 'is_default' => false, 'condition_rules' => []],
                ['from_uuid' => $splitUuid, 'to_uuid' => $legalUuid, 'action_name' => 'to_legal', 'priority' => 1, 'is_default' => false, 'condition_rules' => []],
                ['from_uuid' => $financeUuid, 'to_uuid' => $joinUuid, 'action_name' => 'finance_done', 'priority' => 0, 'is_default' => true, 'condition_rules' => []],
                ['from_uuid' => $legalUuid, 'to_uuid' => $joinUuid, 'action_name' => 'legal_done', 'priority' => 0, 'is_default' => true, 'condition_rules' => []],
                ['from_uuid' => $joinUuid, 'to_uuid' => $endUuid, 'action_name' => 'finish', 'priority' => 0, 'is_default' => true, 'condition_rules' => []],
            ])
            ->call('saveDraft');

        $workflow = Workflow::query()
            ->where('code', 'ac1_parallel_wf')
            ->where('tenant_id', $this->designerTenant->id)
            ->first();

        $this->assertNotNull($workflow);
        $this->assertSame(WorkflowDefinitionStatus::Draft, $workflow->status);
        $this->assertCount(6, $workflow->steps);
        $this->assertCount(6, $workflow->transitions);

        $gatewayTypes = $workflow->steps->pluck('gateway_type')->map(fn ($g) => $g?->value ?? $g)->all();
        $this->assertContains('parallel_split', $gatewayTypes);
        $this->assertContains('parallel_join', $gatewayTypes);

        $join = $workflow->steps->firstWhere('code', 'join');
        $this->assertNotNull($join);
        $this->assertSame('majority', $join->quorum_strategy?->value ?? $join->quorum_strategy);
        $this->assertSame(2, $join->quorum_value);
    }
}
