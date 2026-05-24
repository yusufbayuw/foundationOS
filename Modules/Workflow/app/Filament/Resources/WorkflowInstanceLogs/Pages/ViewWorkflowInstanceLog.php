<?php

namespace Modules\Workflow\Filament\Resources\WorkflowInstanceLogs\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Workflow\Filament\Resources\WorkflowInstanceLogs\WorkflowInstanceLogResource;

class ViewWorkflowInstanceLog extends ViewRecord
{
    protected static string $resource = WorkflowInstanceLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
