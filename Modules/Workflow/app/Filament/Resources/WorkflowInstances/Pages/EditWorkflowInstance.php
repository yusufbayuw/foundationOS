<?php

namespace Modules\Workflow\Filament\Resources\WorkflowInstances\Pages;

use Filament\Resources\Pages\EditRecord;
use Modules\Workflow\Filament\Resources\WorkflowInstances\WorkflowInstanceResource;

class EditWorkflowInstance extends EditRecord
{
    protected static string $resource = WorkflowInstanceResource::class;
}
