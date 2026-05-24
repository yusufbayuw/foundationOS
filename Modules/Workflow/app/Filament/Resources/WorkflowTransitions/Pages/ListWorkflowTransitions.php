<?php

namespace Modules\Workflow\Filament\Resources\WorkflowTransitions\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Workflow\Filament\Resources\WorkflowTransitions\WorkflowTransitionResource;

class ListWorkflowTransitions extends ListRecords
{
    protected static string $resource = WorkflowTransitionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
