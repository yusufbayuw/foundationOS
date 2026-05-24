<?php

namespace Modules\Workflow\Filament\Resources\WorkflowAssignments\Pages;

use Filament\Resources\Pages\EditRecord;
use Modules\Workflow\Filament\Resources\WorkflowAssignments\WorkflowAssignmentResource;

class EditWorkflowAssignment extends EditRecord
{
    protected static string $resource = WorkflowAssignmentResource::class;
}
