<?php

namespace Modules\Workflow\Filament\Resources\WorkflowStepBranches\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Workflow\Filament\Resources\WorkflowStepBranches\WorkflowStepBranchResource;

class ListWorkflowStepBranches extends ListRecords
{
    protected static string $resource = WorkflowStepBranchResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
