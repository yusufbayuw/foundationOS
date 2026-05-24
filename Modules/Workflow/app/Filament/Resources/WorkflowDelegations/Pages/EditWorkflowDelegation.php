<?php

namespace Modules\Workflow\Filament\Resources\WorkflowDelegations\Pages;

use Filament\Resources\Pages\EditRecord;
use Modules\Workflow\Filament\Resources\WorkflowDelegations\WorkflowDelegationResource;

class EditWorkflowDelegation extends EditRecord
{
    protected static string $resource = WorkflowDelegationResource::class;
}
