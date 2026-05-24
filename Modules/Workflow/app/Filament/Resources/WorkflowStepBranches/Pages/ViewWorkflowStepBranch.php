<?php

namespace Modules\Workflow\Filament\Resources\WorkflowStepBranches\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Workflow\Filament\Resources\WorkflowStepBranches\WorkflowStepBranchResource;

class ViewWorkflowStepBranch extends ViewRecord
{
    protected static string $resource = WorkflowStepBranchResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
