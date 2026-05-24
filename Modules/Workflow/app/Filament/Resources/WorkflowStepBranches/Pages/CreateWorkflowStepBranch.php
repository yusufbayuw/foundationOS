<?php

namespace Modules\Workflow\Filament\Resources\WorkflowStepBranches\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Workflow\Filament\Resources\WorkflowStepBranches\WorkflowStepBranchResource;

class CreateWorkflowStepBranch extends CreateRecord
{
    protected static string $resource = WorkflowStepBranchResource::class;
}
