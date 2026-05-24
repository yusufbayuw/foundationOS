<?php

namespace Modules\Workflow\Filament\Resources\WorkflowAutomatedActions\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Workflow\Filament\Resources\WorkflowAutomatedActions\WorkflowAutomatedActionResource;

class ListWorkflowAutomatedActions extends ListRecords
{
    protected static string $resource = WorkflowAutomatedActionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
