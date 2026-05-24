<?php

namespace Modules\Workflow\Filament\Resources\WorkflowInstanceLogs\Pages;

use Filament\Resources\Pages\EditRecord;
use Modules\Workflow\Filament\Resources\WorkflowInstanceLogs\WorkflowInstanceLogResource;

class EditWorkflowInstanceLog extends EditRecord
{
    protected static string $resource = WorkflowInstanceLogResource::class;
}
