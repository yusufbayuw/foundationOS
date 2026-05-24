<?php

namespace Modules\Workflow\Filament\Resources\WorkflowSteps\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Workflow\Filament\Resources\WorkflowSteps\WorkflowStepResource;

class ListWorkflowSteps extends ListRecords
{
    protected static string $resource = WorkflowStepResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
