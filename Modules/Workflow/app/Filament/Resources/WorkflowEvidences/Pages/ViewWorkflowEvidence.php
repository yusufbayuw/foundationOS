<?php

namespace Modules\Workflow\Filament\Resources\WorkflowEvidences\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Workflow\Filament\Resources\WorkflowEvidences\WorkflowEvidenceResource;

class ViewWorkflowEvidence extends ViewRecord
{
    protected static string $resource = WorkflowEvidenceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
