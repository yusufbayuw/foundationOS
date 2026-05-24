<?php

namespace Modules\Workflow\Filament\Resources\WorkflowEvidences\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Workflow\Filament\Resources\WorkflowEvidences\WorkflowEvidenceResource;

class ListWorkflowEvidences extends ListRecords
{
    protected static string $resource = WorkflowEvidenceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
