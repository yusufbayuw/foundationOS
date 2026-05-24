<?php

namespace Modules\Workflow\Filament\Resources\WorkflowTransitions\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Workflow\Filament\Resources\WorkflowTransitions\WorkflowTransitionResource;

class ViewWorkflowTransition extends ViewRecord
{
    protected static string $resource = WorkflowTransitionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
