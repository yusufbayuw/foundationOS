<?php

namespace Modules\Workflow\Filament\Resources\WorkflowTransitions\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Workflow\Filament\Resources\WorkflowTransitions\WorkflowTransitionResource;

class CreateWorkflowTransition extends CreateRecord
{
    protected static string $resource = WorkflowTransitionResource::class;
}
