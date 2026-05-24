<?php

namespace Modules\Workflow\Filament\Resources\WorkflowAssignments\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Workflow\Filament\Resources\WorkflowAssignments\WorkflowAssignmentResource;

class CreateWorkflowAssignment extends CreateRecord
{
    protected static string $resource = WorkflowAssignmentResource::class;
}
