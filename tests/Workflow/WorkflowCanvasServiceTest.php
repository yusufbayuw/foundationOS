<?php

namespace Tests\Workflow;

use App\Support\CurrentTenant;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Str;
use Modules\Core\Models\SubscriptionPlan;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\User;
use Modules\Workflow\Enums\WorkflowDefinitionStatus;
use Modules\Workflow\Models\Workflow;
use Modules\Workflow\Models\WorkflowStep;
use Modules\Workflow\Models\WorkflowTransition;
use Modules\Workflow\Services\WorkflowCanvasService;
use Tests\TestCase;

class WorkflowCanvasServiceTest extends TestCase
{
    use LazilyRefreshDatabase;

    private Tenant $tenant;

    private WorkflowCanvasService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $plan = SubscriptionPlan::query()->create([
            'code' => 'canvas-plan',
            'name' => 'Canvas Plan',
            'included_modules' => ['core', 'workflow'],
        ]);

        $user = User::query()->create([
            'name' => 'Canvas User',
            'email' => 'canvas@example.com',
            'password' => 'password',
        ]);

        $this->tenant = Tenant::query()->create([
            'uuid' => (string) Str::uuid(),
            'code' => 'canvas-tenant',
            'name' => 'Canvas Tenant',
            'subscription_plan_id' => $plan->id,
            'created_by' => $user->id,
        ]);

        app(CurrentTenant::class)->set($this->tenant);

        $this->service = app(WorkflowCanvasService::class);
    }

    protected function tearDown(): void
    {
        app(CurrentTenant::class)->forget();

        parent::tearDown();
    }

    public function test_canvas_for_workflow_converts_definition_to_canvas_arrays(): void
    {
        $workflow = $this->makeWorkflow();

        $canvas = $this->service->canvasForWorkflow($workflow->id);

        $this->assertSame('canvas_wf', $canvas['workflow']['code']);
        $this->assertSame('Canvas Workflow', $canvas['workflow']['name']);
        $this->assertSame('draft', $canvas['workflow']['status']);
        $this->assertCount(2, $canvas['steps']);
        $this->assertSame('start', $canvas['steps'][0]['code']);
        $this->assertSame(['x' => 120, 'y' => 240], $canvas['steps'][0]['canvas_position']);
        $this->assertCount(1, $canvas['transitions']);
        $this->assertSame('submit', $canvas['transitions'][0]['action_name']);
        $this->assertSame($canvas['steps'][0]['uuid'], $canvas['transitions'][0]['from_uuid']);
        $this->assertSame($canvas['steps'][1]['uuid'], $canvas['transitions'][0]['to_uuid']);
    }

    public function test_persist_canvas_creates_workflow_steps_and_transitions(): void
    {
        $startUuid = (string) Str::uuid();
        $doneUuid = (string) Str::uuid();

        $workflow = $this->service->persistCanvas(
            tenantId: $this->tenant->id,
            workflowId: null,
            workflowCode: 'persisted_wf',
            workflowName: 'Persisted Workflow',
            workflowDescription: 'Stored from canvas',
            steps: [
                $this->canvasStep($startUuid, 'start', true, false, ['x' => 10, 'y' => 20]),
                $this->canvasStep($doneUuid, 'done', false, true),
            ],
            transitions: [
                [
                    'from_uuid' => $startUuid,
                    'to_uuid' => $doneUuid,
                    'action_name' => 'submit',
                    'priority' => 0,
                    'is_default' => true,
                    'condition_rules' => ['all' => []],
                ],
            ],
        );

        $this->assertSame('persisted_wf', $workflow->code);
        $this->assertSame(2, $workflow->steps()->count());
        $this->assertSame(1, $workflow->transitions()->count());
        $this->assertNotContains($startUuid, $workflow->steps()->pluck('uuid')->all());
        $this->assertSame(['x' => 10, 'y' => 20], $workflow->steps()->where('code', 'start')->first()->canvas_position);
        $this->assertSame('submit', $workflow->transitions()->first()->action_name);
    }

    public function test_import_validation_and_export_are_delegated_to_porter(): void
    {
        $workflow = $this->makeWorkflow('portable_wf');
        $payload = $this->service->exportPayload($workflow);

        $this->assertSame([], $this->service->validateImportPayload($payload));

        $imported = $this->service->importPayload($payload, $this->tenant->id);

        $this->assertSame(WorkflowDefinitionStatus::Draft, $imported->status);
        $this->assertNotSame($workflow->id, $imported->id);
        $this->assertSame(2, $imported->steps()->count());
        $this->assertSame(1, $imported->transitions()->count());
    }

    /**
     * @return array<string, mixed>
     */
    private function canvasStep(string $uuid, string $code, bool $isInitial, bool $isTerminal, ?array $canvasPosition = null): array
    {
        return [
            'uuid' => $uuid,
            'code' => $code,
            'name' => Str::headline($code),
            'description' => '',
            'step_type' => $isInitial ? 'start' : 'end',
            'gateway_type' => 'none',
            'quorum_strategy' => null,
            'quorum_value' => null,
            'assignee_type' => 'user',
            'assignee_value' => '',
            'assignee_config' => [],
            'form_schema' => [],
            'sla_hours' => null,
            'is_initial' => $isInitial,
            'is_terminal' => $isTerminal,
            'sort_order' => 0,
            'canvas_position' => $canvasPosition,
        ];
    }

    private function makeWorkflow(string $code = 'canvas_wf'): Workflow
    {
        $workflow = Workflow::query()->create([
            'tenant_id' => $this->tenant->id,
            'code' => $code,
            'name' => 'Canvas Workflow',
            'version' => 1,
            'status' => WorkflowDefinitionStatus::Draft,
            'is_active' => false,
        ]);

        $start = WorkflowStep::query()->create([
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
            'canvas_position' => ['x' => 120, 'y' => 240],
        ]);

        $done = WorkflowStep::query()->create([
            'workflow_id' => $workflow->id,
            'uuid' => (string) Str::uuid(),
            'code' => 'done',
            'name' => 'Done',
            'step_type' => 'end',
            'gateway_type' => 'none',
            'assignee_type' => 'user',
            'is_initial' => false,
            'is_terminal' => true,
            'sort_order' => 2,
        ]);

        WorkflowTransition::query()->create([
            'workflow_id' => $workflow->id,
            'from_step_id' => $start->id,
            'to_step_id' => $done->id,
            'action_name' => 'submit',
            'priority' => 0,
            'is_default' => true,
        ]);

        return $workflow->fresh(['steps', 'transitions']);
    }
}
