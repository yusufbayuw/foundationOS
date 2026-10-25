<?php

namespace Tests\ParallelWorkflow;

use Illuminate\Support\Collection;
use Modules\Core\Models\User;
use Modules\Workflow\Contracts\WorkflowDynamicAssigneeResolver;
use Modules\Workflow\Models\WorkflowInstance;
use Modules\Workflow\Models\WorkflowStep;

/**
 * Test-only resolver shared by separate PHPUnit test files.
 *
 * Standalone PSR-4 autoloading keeps complete and sharded test runs equivalent.
 */
class InlineMultiUserResolver implements WorkflowDynamicAssigneeResolver
{
    public function resolve(WorkflowInstance $instance, WorkflowStep $step): Collection
    {
        $ids = (array) data_get($step->assignee_config, 'user_ids', []);

        return User::query()
            ->whereIn('id', $ids)
            ->get();
    }
}
