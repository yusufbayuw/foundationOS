<?php

namespace Modules\Workflow\Filament\Resources\WorkflowSteps\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Workflow\Filament\Resources\WorkflowSteps\WorkflowStepResource;

class CreateWorkflowStep extends CreateRecord
{
    protected static string $resource = WorkflowStepResource::class;
}
