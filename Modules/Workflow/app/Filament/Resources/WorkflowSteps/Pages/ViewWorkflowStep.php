<?php

namespace Modules\Workflow\Filament\Resources\WorkflowSteps\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Workflow\Filament\Resources\WorkflowSteps\WorkflowStepResource;

class ViewWorkflowStep extends ViewRecord
{
    protected static string $resource = WorkflowStepResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
