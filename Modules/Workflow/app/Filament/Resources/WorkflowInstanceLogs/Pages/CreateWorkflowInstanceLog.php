<?php

namespace Modules\Workflow\Filament\Resources\WorkflowInstanceLogs\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Workflow\Filament\Resources\WorkflowInstanceLogs\WorkflowInstanceLogResource;

class CreateWorkflowInstanceLog extends CreateRecord
{
    protected static string $resource = WorkflowInstanceLogResource::class;
}
