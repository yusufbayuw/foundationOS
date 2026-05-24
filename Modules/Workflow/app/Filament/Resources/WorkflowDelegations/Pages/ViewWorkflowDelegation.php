<?php

namespace Modules\Workflow\Filament\Resources\WorkflowDelegations\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Workflow\Filament\Resources\WorkflowDelegations\WorkflowDelegationResource;

class ViewWorkflowDelegation extends ViewRecord
{
    protected static string $resource = WorkflowDelegationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
