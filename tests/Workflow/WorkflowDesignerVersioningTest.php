<?php

namespace Tests\Workflow;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Livewire\Livewire;
use Modules\Workflow\Enums\WorkflowDefinitionStatus;
use Modules\Workflow\Livewire\WorkflowCanvas;
use Modules\Workflow\Models\Workflow;
use Tests\Concerns\CreatesWorkflowDesignerContext;
use Tests\TestCase;

class WorkflowDesignerVersioningTest extends TestCase
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

    public function test_saving_draft_on_active_workflow_creates_new_draft_version(): void
    {
        $activeWorkflow = $this->makeDesignerWorkflow('active_versioning_wf', 'active');

        Livewire::test(WorkflowCanvas::class, ['workflowId' => $activeWorkflow->id])
            ->set('workflowName', 'Modified Name')
            ->call('saveDraft');

        $activeWorkflow->refresh();
        $this->assertSame(WorkflowDefinitionStatus::Active, $activeWorkflow->status);
        $this->assertTrue($activeWorkflow->is_active);

        $draftVersion = Workflow::query()
            ->where('code', 'active_versioning_wf')
            ->where('status', WorkflowDefinitionStatus::Draft)
            ->where('tenant_id', $this->designerTenant->id)
            ->first();

        $this->assertNotNull($draftVersion);
        $this->assertGreaterThan($activeWorkflow->version, $draftVersion->version);
    }

    public function test_saving_draft_does_not_mutate_active_workflow_steps(): void
    {
        $active = $this->makeDesignerWorkflow('draft_isolation_wf', 'active');
        $originalStepCount = $active->steps()->count();

        Livewire::test(WorkflowCanvas::class, ['workflowId' => $active->id])
            ->call('saveDraft');

        $active->refresh();
        $this->assertSame(WorkflowDefinitionStatus::Active, $active->status);
        $this->assertCount($originalStepCount, $active->steps);
    }

    public function test_publish_archives_previous_active_version(): void
    {
        $first = $this->makeDesignerWorkflow('publish_archive_wf', 'active');
        $first->update(['version' => 1]);

        $draft = Livewire::test(WorkflowCanvas::class, ['workflowId' => $first->id])
            ->set('workflowName', 'Version 2 draft')
            ->call('saveDraft')
            ->get('workflowId');

        $this->assertNotNull($draft);

        Livewire::test(WorkflowCanvas::class, ['workflowId' => $draft])
            ->call('publish');

        $first->refresh();
        $this->assertSame(WorkflowDefinitionStatus::Archived, $first->status);
        $this->assertFalse($first->is_active);

        $published = Workflow::findOrFail($draft);
        $this->assertSame(WorkflowDefinitionStatus::Active, $published->status);
        $this->assertTrue($published->is_active);
    }
}
