<?php

namespace Modules\Workflow\Filament\Resources\WorkflowTransitions\Pages;

use Filament\Resources\Pages\EditRecord;
use Modules\Workflow\Filament\Resources\WorkflowTransitions\WorkflowTransitionResource;

class EditWorkflowTransition extends EditRecord
{
    protected static string $resource = WorkflowTransitionResource::class;
}
