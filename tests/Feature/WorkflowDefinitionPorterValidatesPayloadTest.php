<?php

namespace Tests\Feature;

use App\Support\CurrentTenant;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Str;
use Modules\Core\Models\SubscriptionPlan;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\User;
use Modules\Workflow\Services\WorkflowDefinitionPorter;
use Tests\TestCase;

class WorkflowDefinitionPorterValidatesPayloadTest extends TestCase
{
    use LazilyRefreshDatabase;

    private WorkflowDefinitionPorter $porter;

    protected function setUp(): void
    {
        parent::setUp();

        $plan = SubscriptionPlan::create([
            'code' => 'porter-validate-plan',
            'name' => 'Porter Validate Plan',
            'included_modules' => ['core', 'workflow'],
        ]);

        $user = User::create([
            'name' => 'Porter Validate User',
            'email' => 'porter-validate@example.com',
            'password' => 'password',
        ]);

        $tenant = Tenant::create([
            'uuid' => (string) Str::uuid(),
            'code' => 'porter-validate-tenant',
            'name' => 'Porter Validate Tenant',
            'subscription_plan_id' => $plan->id,
            'created_by' => $user->id,
        ]);

        app(CurrentTenant::class)->set($tenant);
        $this->porter = app(WorkflowDefinitionPorter::class);
    }

    protected function tearDown(): void
    {
        app(CurrentTenant::class)->forget();
        parent::tearDown();
    }

    public function test_validate_payload_fails_when_schema_version_missing(): void
    {
        $errors = $this->porter->validatePayload([
            'workflow' => ['code' => 'x'],
            'steps' => [['uuid' => (string) Str::uuid(), 'code' => 's', 'name' => 'S', 'is_initial' => true, 'is_terminal' => false]],
            'transitions' => [],
        ]);

        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('schema_version', $errors[0]);
    }

    public function test_validate_payload_fails_when_steps_empty(): void
    {
        $errors = $this->porter->validatePayload([
            'schema_version' => '1.0',
            'workflow' => ['code' => 'x', 'name' => 'X'],
            'steps' => [],
            'transitions' => [],
        ]);

        $this->assertNotEmpty($errors);
    }

    public function test_validate_payload_fails_for_duplicate_step_uuid(): void
    {
        $uuid = (string) Str::uuid();

        $errors = $this->porter->validatePayload([
            'schema_version' => '1.0',
            'workflow' => ['code' => 'x', 'name' => 'X'],
            'steps' => [
                ['uuid' => $uuid, 'code' => 'a', 'name' => 'A', 'is_initial' => true, 'is_terminal' => false],
                ['uuid' => $uuid, 'code' => 'b', 'name' => 'B', 'is_initial' => false, 'is_terminal' => true],
            ],
            'transitions' => [],
        ]);

        $this->assertTrue(collect($errors)->contains(fn ($e) => str_contains($e, 'duplicate uuid')));
    }

    public function test_validate_payload_fails_for_transition_with_unknown_uuid(): void
    {
        $startUuid = (string) Str::uuid();
        $endUuid = (string) Str::uuid();

        $errors = $this->porter->validatePayload([
            'schema_version' => '1.0',
            'workflow' => ['code' => 'x', 'name' => 'X'],
            'steps' => [
                ['uuid' => $startUuid, 'code' => 'start', 'name' => 'Start', 'is_initial' => true, 'is_terminal' => false],
                ['uuid' => $endUuid, 'code' => 'end', 'name' => 'End', 'is_initial' => false, 'is_terminal' => true],
            ],
            'transitions' => [
                ['from_uuid' => 'missing-uuid', 'to_uuid' => $endUuid, 'action_name' => 'go'],
            ],
        ]);

        $this->assertNotEmpty($errors);
        $this->assertTrue(collect($errors)->contains(fn ($e) => str_contains($e, 'from_uuid')));
    }
}
