<?php

namespace Modules\Workflow\Filament\Resources\WorkflowStepBranches\Pages;

use Filament\Resources\Pages\EditRecord;
use Modules\Workflow\Filament\Resources\WorkflowStepBranches\WorkflowStepBranchResource;

class EditWorkflowStepBranch extends EditRecord
{
    protected static string $resource = WorkflowStepBranchResource::class;
}
