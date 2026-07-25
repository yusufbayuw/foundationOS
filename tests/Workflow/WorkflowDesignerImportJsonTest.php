<?php

namespace Tests\Workflow;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Livewire\Livewire;
use Modules\Workflow\Enums\WorkflowDefinitionStatus;
use Modules\Workflow\Livewire\WorkflowCanvas;
use Modules\Workflow\Models\Workflow;
use Modules\Workflow\Services\WorkflowDefinitionPorter;
use Tests\Concerns\CreatesWorkflowDesignerContext;
use Tests\TestCase;

class WorkflowDesignerImportJsonTest extends TestCase
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

    public function test_import_from_json_creates_draft_and_loads_canvas(): void
    {
        $workflow = $this->makeDesignerWorkflow('import_source_wf');
        $payload = app(WorkflowDefinitionPorter::class)->export($workflow);
        $json = json_encode($payload, JSON_THROW_ON_ERROR);

        Livewire::test(WorkflowCanvas::class)
            ->callAction('importJsonAction', ['payload' => $json]);

        $imported = Workflow::query()
            ->where('code', 'import_source_wf')
            ->where('tenant_id', $this->designerTenant->id)
            ->where('status', WorkflowDefinitionStatus::Draft)
            ->orderByDesc('id')
            ->first();

        $this->assertNotNull($imported);
        $this->assertCount(2, $imported->steps);
    }

    public function test_import_json_action_rejects_invalid_json(): void
    {
        Livewire::test(WorkflowCanvas::class)
            ->callAction('importJsonAction', ['payload' => '{not valid json']);

        $this->assertDatabaseMissing('workflows', [
            'tenant_id' => $this->designerTenant->id,
            'code' => 'not valid json',
        ]);
    }
}
