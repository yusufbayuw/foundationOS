<?php

namespace Modules\Workflow\Filament\Resources\WorkflowInstances\Pages;

use Filament\Resources\Pages\ListRecords;
use Modules\Workflow\Filament\Resources\WorkflowInstances\WorkflowInstanceResource;

class ListWorkflowInstances extends ListRecords
{
    protected static string $resource = WorkflowInstanceResource::class;
}
