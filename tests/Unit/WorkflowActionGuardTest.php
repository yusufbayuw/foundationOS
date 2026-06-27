<?php

namespace Tests\Unit;

use Modules\Workflow\Exceptions\WorkflowAuthorizationException;
use Modules\Workflow\Models\WorkflowInstance;
use Modules\Workflow\Models\WorkflowStep;
use Modules\Workflow\Support\WorkflowActionGuard;
use Tests\TestCase;

class WorkflowActionGuardTest extends TestCase
{
    public function test_assert_allows_action_from_step_schema(): void
    {
        $instance = new WorkflowInstance([
            'current_step_id' => 10,
            'workflow_snapshot' => ['transitions' => []],
        ]);

        $step = new WorkflowStep([
            'id' => 10,
            'action_schema' => [
                ['name' => 'approve'],
                ['name' => 'reject'],
            ],
        ]);

        app(WorkflowActionGuard::class)->assertAdvanceActionAllowed($instance, $step, 'approve');

        $this->addToAssertionCount(1);
    }

    public function test_assert_rejects_action_not_in_step_schema(): void
    {
        $instance = new WorkflowInstance([
            'current_step_id' => 10,
            'workflow_snapshot' => ['transitions' => []],
        ]);

        $step = new WorkflowStep([
            'id' => 10,
            'action_schema' => [
                ['name' => 'approve'],
            ],
        ]);

        $this->expectException(WorkflowAuthorizationException::class);
        $this->expectExceptionMessage('Action [escalate] is not permitted');

        app(WorkflowActionGuard::class)->assertAdvanceActionAllowed($instance, $step, 'escalate');
    }

    public function test_assert_allows_transition_action_from_snapshot(): void
    {
        $instance = new WorkflowInstance([
            'current_step_id' => 1,
            'workflow_snapshot' => [
                'transitions' => [
                    ['from_step_id' => 1, 'to_step_id' => 2, 'action_name' => 'submit'],
                ],
            ],
        ]);

        app(WorkflowActionGuard::class)->assertAdvanceActionAllowed($instance, null, 'submit');

        $this->addToAssertionCount(1);
    }
}
