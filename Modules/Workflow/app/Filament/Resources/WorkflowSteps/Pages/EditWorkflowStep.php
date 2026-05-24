<?php

namespace Modules\Workflow\Filament\Resources\WorkflowSteps\Pages;

use Filament\Resources\Pages\EditRecord;
use Modules\Workflow\Filament\Resources\WorkflowSteps\WorkflowStepResource;

class EditWorkflowStep extends EditRecord
{
    protected static string $resource = WorkflowStepResource::class;
}
