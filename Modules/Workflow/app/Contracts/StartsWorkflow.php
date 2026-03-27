<?php

namespace Modules\Workflow\Contracts;

interface StartsWorkflow
{
    public function workflowCode(): string;
}
