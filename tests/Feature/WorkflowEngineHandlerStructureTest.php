<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Modules\Workflow\Contracts\WorkflowEngine;
use Modules\Workflow\Models\WorkflowStep;
use Modules\Workflow\Services\DatabaseWorkflowEngine;
use Modules\Workflow\Services\Engine\Handlers\AdvanceWorkflowHandler;
use Modules\Workflow\Services\Engine\Handlers\CancelWorkflowHandler;
use Modules\Workflow\Services\Engine\Handlers\ReassignWorkflowHandler;
use Modules\Workflow\Services\Engine\Handlers\ReturnWorkflowHandler;
use Modules\Workflow\Services\Engine\Support\ActorAuthorizer;
use Modules\Workflow\Services\Engine\Support\PayloadSnapshot;
use Modules\Workflow\Services\Engine\Support\StatusResolver;
use Modules\Workflow\Services\Engine\Support\WorkflowInstanceLocker;
use Tests\TestCase;

class WorkflowEngineHandlerStructureTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_workflow_engine_is_a_thin_facade_over_handlers(): void
    {
        $source = file_get_contents(base_path('Modules/Workflow/app/Services/DatabaseWorkflowEngine.php'));

        $this->assertLessThan(100, substr_count($source, "\n"));
        $this->assertStringContainsString('AdvanceWorkflowHandler', $source);
        $this->assertStringContainsString('ReturnWorkflowHandler', $source);
        $this->assertStringContainsString('CancelWorkflowHandler', $source);
        $this->assertStringContainsString('ReassignWorkflowHandler', $source);
        $this->assertStringNotContainsString('advanceLinear', $source);
        $this->assertStringNotContainsString('authorizeActor', $source);
        $this->assertStringNotContainsString('payloadSnapshot', $source);
    }

    public function test_handler_and_support_classes_are_container_resolvable(): void
    {
        $this->assertInstanceOf(DatabaseWorkflowEngine::class, app(WorkflowEngine::class));
        $this->assertInstanceOf(AdvanceWorkflowHandler::class, app(AdvanceWorkflowHandler::class));
        $this->assertInstanceOf(ReturnWorkflowHandler::class, app(ReturnWorkflowHandler::class));
        $this->assertInstanceOf(CancelWorkflowHandler::class, app(CancelWorkflowHandler::class));
        $this->assertInstanceOf(ReassignWorkflowHandler::class, app(ReassignWorkflowHandler::class));
        $this->assertInstanceOf(ActorAuthorizer::class, app(ActorAuthorizer::class));
        $this->assertInstanceOf(StatusResolver::class, app(StatusResolver::class));
        $this->assertInstanceOf(PayloadSnapshot::class, app(PayloadSnapshot::class));
        $this->assertInstanceOf(WorkflowInstanceLocker::class, app(WorkflowInstanceLocker::class));
    }

    public function test_status_resolver_preserves_terminal_action_semantics(): void
    {
        $resolver = app(StatusResolver::class);

        $terminalStep = new WorkflowStep(['is_terminal' => true]);
        $nonTerminalStep = new WorkflowStep(['is_terminal' => false]);

        $this->assertSame(
            'rejected',
            $resolver->resolve('reject', $nonTerminalStep)->value,
        );
        $this->assertSame(
            'cancelled',
            $resolver->resolve('cancel', $nonTerminalStep)->value,
        );
        $this->assertSame(
            'completed',
            $resolver->resolve('approve', $terminalStep)->value,
        );
        $this->assertSame(
            'running',
            $resolver->resolve('approve', $nonTerminalStep)->value,
        );
    }
}
