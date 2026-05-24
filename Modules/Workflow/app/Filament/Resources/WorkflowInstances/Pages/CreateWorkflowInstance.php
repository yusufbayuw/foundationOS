<?php

namespace Modules\Workflow\Filament\Resources\WorkflowInstances\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Workflow\Filament\Resources\WorkflowInstances\WorkflowInstanceResource;

class CreateWorkflowInstance extends CreateRecord
{
    protected static string $resource = WorkflowInstanceResource::class;
}
