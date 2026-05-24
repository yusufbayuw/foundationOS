<?php

namespace Modules\Workflow\Filament\Resources\WorkflowDelegations\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Workflow\Filament\Resources\WorkflowDelegations\WorkflowDelegationResource;

class ListWorkflowDelegations extends ListRecords
{
    protected static string $resource = WorkflowDelegationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
