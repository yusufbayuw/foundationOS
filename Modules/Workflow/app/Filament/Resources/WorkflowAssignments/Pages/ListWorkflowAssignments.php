<?php

namespace Modules\Workflow\Filament\Resources\WorkflowAssignments\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Workflow\Filament\Resources\WorkflowAssignments\WorkflowAssignmentResource;

class ListWorkflowAssignments extends ListRecords
{
    protected static string $resource = WorkflowAssignmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
