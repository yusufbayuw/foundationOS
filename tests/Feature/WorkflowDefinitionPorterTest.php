<?php

namespace Tests\Feature;

use App\Support\CurrentTenant;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Str;
use Modules\Core\Models\SubscriptionPlan;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\User;
use Modules\Workflow\Enums\WorkflowDefinitionStatus;
use Modules\Workflow\Exceptions\WorkflowConfigurationException;
use Modules\Workflow\Models\Workflow;
use Modules\Workflow\Models\WorkflowStep;
use Modules\Workflow\Models\WorkflowTransition;
use Modules\Workflow\Services\WorkflowDefinitionLifecycleService;
use Modules\Workflow\Services\WorkflowDefinitionPorter;
use Tests\TestCase;

class WorkflowDefinitionPorterTest extends TestCase
{
    use LazilyRefreshDatabase;

    private Tenant $tenant;

    private WorkflowDefinitionPorter $porter;

    private WorkflowDefinitionLifecycleService $lifecycle;

    protected function setUp(): void
    {
        parent::setUp();

        $plan = SubscriptionPlan::create([
            'code' => 'porter-plan',
            'name' => 'Porter Plan',
            'included_modules' => ['core', 'workflow'],
        ]);

        $user = User::create([
            'name' => 'Porter User',
            'email' => 'porter@example.com',
            'password' => 'password',
        ]);

        $this->tenant = Tenant::create([
            'uuid' => (string) Str::uuid(),
            'code' => 'porter-tenant',
            'name' => 'Porter Tenant',
            'subscription_plan_id' => $plan->id,
            'created_by' => $user->id,
        ]);

        app(CurrentTenant::class)->set($this->tenant);

        $this->porter = app(WorkflowDefinitionPorter::class);
        $this->lifecycle = app(WorkflowDefinitionLifecycleService::class);
    }

    protected function tearDown(): void
    {
        app(CurrentTenant::class)->forget();
        parent::tearDown();
    }

    private function makeWorkflow(string $code = 'test_wf'): Workflow
    {
        $workflow = Workflow::create([
            'tenant_id' => $this->tenant->id,
            'code' => $code,
            'name' => 'Test Workflow',
            'version' => 1,
            'status' => WorkflowDefinitionStatus::Draft,
            'is_active' => false,
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
            'canvas_position' => ['x' => 100, 'y' => 200],
        ]);

        $review = WorkflowStep::create([
            'workflow_id' => $workflow->id,
            'uuid' => (string) Str::uuid(),
            'code' => 'review',
            'name' => 'Review',
            'step_type' => 'approval',
            'gateway_type' => 'none',
            'assignee_type' => 'user',
            'is_initial' => false,
            'is_terminal' => false,
            'sort_order' => 2,
        ]);

        $done = WorkflowStep::create([
            'workflow_id' => $workflow->id,
            'uuid' => (string) Str::uuid(),
            'code' => 'done',
            'name' => 'Done',
            'step_type' => 'end',
            'gateway_type' => 'none',
            'assignee_type' => 'user',
            'is_initial' => false,
            'is_terminal' => true,
            'sort_order' => 3,
        ]);

        WorkflowTransition::create([
            'workflow_id' => $workflow->id,
            'from_step_id' => $start->id,
            'to_step_id' => $review->id,
            'action_name' => 'submit',
            'priority' => 1,
            'is_default' => true,
        ]);

        WorkflowTransition::create([
            'workflow_id' => $workflow->id,
            'from_step_id' => $review->id,
            'to_step_id' => $done->id,
            'action_name' => 'approve',
            'priority' => 1,
            'is_default' => true,
        ]);

        return $workflow->fresh(['steps', 'transitions']);
    }

    // ─── Export tests ────────────────────────────────────────────────────────────

    public function test_export_returns_canonical_json_shape(): void
    {
        $workflow = $this->makeWorkflow();

        $payload = $this->porter->export($workflow);

        $this->assertSame('1.0', $payload['schema_version']);
        $this->assertArrayHasKey('exported_at', $payload);
        $this->assertSame('test_wf', $payload['workflow']['code']);
        $this->assertCount(3, $payload['steps']);
        $this->assertCount(2, $payload['transitions']);
    }

    public function test_export_uses_uuids_not_database_ids(): void
    {
        $workflow = $this->makeWorkflow();

        $payload = $this->porter->export($workflow);

        foreach ($payload['transitions'] as $t) {
            $this->assertArrayHasKey('from_uuid', $t);
            $this->assertArrayHasKey('to_uuid', $t);
            $this->assertArrayNotHasKey('from_step_id', $t);
            $this->assertArrayNotHasKey('to_step_id', $t);
        }
    }

    public function test_export_preserves_canvas_position(): void
    {
        $workflow = $this->makeWorkflow();

        $payload = $this->porter->export($workflow);

        $startStep = collect($payload['steps'])->firstWhere('code', 'start');
        $this->assertSame(['x' => 100, 'y' => 200], $startStep['canvas_position']);
    }

    // ─── Import tests ────────────────────────────────────────────────────────────

    public function test_import_creates_draft_workflow_with_all_steps(): void
    {
        $workflow = $this->makeWorkflow();
        $payload = $this->porter->export($workflow);

        $imported = $this->porter->import($payload, $this->tenant->id);

        $this->assertSame(WorkflowDefinitionStatus::Draft, $imported->status);
        $this->assertCount(3, $imported->steps);
        $this->assertCount(2, $imported->transitions);
    }

    public function test_import_regenerates_uuids(): void
    {
        $workflow = $this->makeWorkflow();
        $originalUuids = $workflow->steps->pluck('uuid')->sort()->values()->all();

        $payload = $this->porter->export($workflow);
        $imported = $this->porter->import($payload, $this->tenant->id);

        $newUuids = $imported->steps->pluck('uuid')->sort()->values()->all();

        $this->assertEmpty(array_intersect($originalUuids, $newUuids), 'Import must regenerate all UUIDs');
    }

    public function test_import_preserves_step_structure(): void
    {
        $workflow = $this->makeWorkflow();
        $payload = $this->porter->export($workflow);

        $imported = $this->porter->import($payload, $this->tenant->id);

        $initialStep = $imported->steps->firstWhere('is_initial', true);
        $this->assertNotNull($initialStep);
        $this->assertSame('start', $initialStep->code);

        $terminalStep = $imported->steps->firstWhere('is_terminal', true);
        $this->assertNotNull($terminalStep);
        $this->assertSame('done', $terminalStep->code);
    }

    public function test_import_preserves_canvas_position(): void
    {
        $workflow = $this->makeWorkflow();
        $payload = $this->porter->export($workflow);

        $imported = $this->porter->import($payload, $this->tenant->id);

        $startStep = $imported->steps->firstWhere('code', 'start');
        $this->assertSame(['x' => 100, 'y' => 200], $startStep->canvas_position);
    }

    public function test_roundtrip_export_import_preserves_structure(): void
    {
        $workflow = $this->makeWorkflow();
        $payload = $this->porter->export($workflow);
        $imported = $this->porter->import($payload, $this->tenant->id);

        $reexported = $this->porter->export($imported);

        $this->assertCount(count($payload['steps']), $reexported['steps']);
        $this->assertCount(count($payload['transitions']), $reexported['transitions']);

        // Step codes preserved
        $originalCodes = collect($payload['steps'])->pluck('code')->sort()->values()->all();
        $reimportedCodes = collect($reexported['steps'])->pluck('code')->sort()->values()->all();
        $this->assertSame($originalCodes, $reimportedCodes);
    }

    // ─── Validate payload tests ──────────────────────────────────────────────────

    public function test_validate_payload_passes_for_valid_payload(): void
    {
        $workflow = $this->makeWorkflow();
        $payload = $this->porter->export($workflow);

        $errors = $this->porter->validatePayload($payload);

        $this->assertEmpty($errors);
    }

    public function test_validate_payload_fails_when_schema_version_missing(): void
    {
        $workflow = $this->makeWorkflow();
        $payload = $this->porter->export($workflow);
        unset($payload['schema_version']);

        $errors = $this->porter->validatePayload($payload);

        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('schema_version', $errors[0]);
    }

    public function test_validate_payload_fails_when_steps_empty(): void
    {
        $workflow = $this->makeWorkflow();
        $payload = $this->porter->export($workflow);
        $payload['steps'] = [];

        $errors = $this->porter->validatePayload($payload);

        $this->assertNotEmpty($errors);
    }

    public function test_validate_payload_fails_for_duplicate_step_uuid(): void
    {
        $workflow = $this->makeWorkflow();
        $payload = $this->porter->export($workflow);

        // Force duplicate UUID
        $payload['steps'][1]['uuid'] = $payload['steps'][0]['uuid'];

        $errors = $this->porter->validatePayload($payload);

        $this->assertTrue(collect($errors)->contains(fn ($e) => str_contains($e, 'duplicate uuid')));
    }

    public function test_validate_payload_fails_for_transition_with_unknown_uuid(): void
    {
        $workflow = $this->makeWorkflow();
        $payload = $this->porter->export($workflow);
        $payload['transitions'][0]['from_uuid'] = 'non-existent-uuid';

        $errors = $this->porter->validatePayload($payload);

        $this->assertNotEmpty($errors);
        $this->assertTrue(collect($errors)->contains(fn ($e) => str_contains($e, 'from_uuid')));
    }

    public function test_import_throws_on_invalid_payload(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->porter->import(
            ['schema_version' => '1.0', 'workflow' => ['code' => 'x'], 'steps' => []],
            $this->tenant->id
        );
    }

    // ─── Pre-publish validation tests ────────────────────────────────────────────

    public function test_publish_fails_when_no_initial_step(): void
    {
        $workflow = $this->makeWorkflow('no_initial');
        $workflow->steps()->update(['is_initial' => false]);

        $this->expectException(WorkflowConfigurationException::class);
        $this->expectExceptionMessageMatches('/initial step/');

        $this->lifecycle->publish($workflow->fresh());
    }

    public function test_publish_fails_when_no_terminal_step(): void
    {
        $workflow = $this->makeWorkflow('no_terminal');
        $workflow->steps()->update(['is_terminal' => false]);

        $this->expectException(WorkflowConfigurationException::class);
        $this->expectExceptionMessageMatches('/terminal step/');

        $this->lifecycle->publish($workflow->fresh());
    }

    public function test_publish_fails_when_orphan_step_exists(): void
    {
        $workflow = $this->makeWorkflow('orphan_wf');

        // Add a step with no incoming transitions
        WorkflowStep::create([
            'workflow_id' => $workflow->id,
            'uuid' => (string) Str::uuid(),
            'code' => 'orphan',
            'name' => 'Orphan Step',
            'step_type' => 'task',
            'gateway_type' => 'none',
            'assignee_type' => 'user',
            'is_initial' => false,
            'is_terminal' => false,
            'sort_order' => 99,
        ]);

        $this->expectException(WorkflowConfigurationException::class);
        $this->expectExceptionMessageMatches('/unreachable/');

        $this->lifecycle->publish($workflow->fresh());
    }

    public function test_publish_succeeds_for_valid_workflow(): void
    {
        $workflow = $this->makeWorkflow('valid_wf');

        $published = $this->lifecycle->publish($workflow->fresh());

        $this->assertSame(WorkflowDefinitionStatus::Active, $published->status);
        $this->assertTrue($published->is_active);
    }
}
