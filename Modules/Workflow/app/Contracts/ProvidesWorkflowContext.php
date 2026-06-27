<?php

namespace Modules\Workflow\Contracts;

interface ProvidesWorkflowContext
{
    /**
     * @return array<string, mixed>
     */
    public function workflowContext(): array;

    public function workflowSubjectLabel(): string;

    public function workflowSubjectType(): string;
}
