<?php

namespace Modules\Workflow\Contracts;

interface ProvidesWorkflowContext
{
    public function workflowContext(): array;

    public function workflowSubjectLabel(): string;

    public function workflowSubjectType(): string;
}
