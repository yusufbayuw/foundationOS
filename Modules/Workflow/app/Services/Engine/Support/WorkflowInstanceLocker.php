<?php

namespace Modules\Workflow\Services\Engine\Support;

use Modules\Workflow\Models\WorkflowInstance;

class WorkflowInstanceLocker
{
    /**
     * @param  list<string>  $relations
     */
    public function lock(WorkflowInstance $instance, array $relations = []): WorkflowInstance
    {
        $locked = WorkflowInstance::query()
            ->whereKey($instance->getKey())
            ->lockForUpdate()
            ->first();

        if ($relations !== []) {
            $locked->load($relations);
        }

        return $locked;
    }
}
