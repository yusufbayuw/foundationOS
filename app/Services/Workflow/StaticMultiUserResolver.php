<?php

namespace App\Services\Workflow;

use Illuminate\Support\Collection;
use Modules\Core\Models\User;
use Modules\Workflow\Contracts\WorkflowDynamicAssigneeResolver;
use Modules\Workflow\Models\WorkflowInstance;
use Modules\Workflow\Models\WorkflowStep;

/**
 * Resolves assignees from a static list of user IDs in assignee_config.
 *
 * Usage: set step assignee_type='resolver', assignee_value=StaticMultiUserResolver::class,
 *        assignee_config=['user_ids' => [1, 2, 3]].
 */
class StaticMultiUserResolver implements WorkflowDynamicAssigneeResolver
{
    public function resolve(WorkflowInstance $instance, WorkflowStep $step): Collection
    {
        $userIds = (array) data_get($step->assignee_config, 'user_ids', []);

        if (empty($userIds)) {
            return collect();
        }

        return User::query()->whereIn('id', $userIds)->get();
    }
}
