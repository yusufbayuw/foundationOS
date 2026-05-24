<?php

namespace Modules\Workflow\Filament\Resources\WorkflowDelegations\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Workflow\Filament\Resources\WorkflowDelegations\WorkflowDelegationResource;

class CreateWorkflowDelegation extends CreateRecord
{
    protected static string $resource = WorkflowDelegationResource::class;
}
