<?php

namespace Modules\Workflow\Filament\Resources\WorkflowInstanceLogs\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Workflow\Filament\Resources\WorkflowInstanceLogs\WorkflowInstanceLogResource;

class ListWorkflowInstanceLogs extends ListRecords
{
    protected static string $resource = WorkflowInstanceLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
