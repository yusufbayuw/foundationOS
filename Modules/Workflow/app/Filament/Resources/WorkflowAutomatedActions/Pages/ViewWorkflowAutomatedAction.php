<?php

namespace Modules\Workflow\Filament\Resources\WorkflowAutomatedActions\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Workflow\Filament\Resources\WorkflowAutomatedActions\WorkflowAutomatedActionResource;

class ViewWorkflowAutomatedAction extends ViewRecord
{
    protected static string $resource = WorkflowAutomatedActionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
