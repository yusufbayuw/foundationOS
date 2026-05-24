<?php

namespace Modules\Workflow\Filament\Resources\WorkflowAssignments\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Workflow\Filament\Resources\WorkflowAssignments\WorkflowAssignmentResource;

class ViewWorkflowAssignment extends ViewRecord
{
    protected static string $resource = WorkflowAssignmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
