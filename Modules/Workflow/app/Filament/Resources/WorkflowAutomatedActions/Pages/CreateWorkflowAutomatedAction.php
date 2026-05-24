<?php

namespace Modules\Workflow\Filament\Resources\WorkflowAutomatedActions\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Workflow\Filament\Resources\WorkflowAutomatedActions\WorkflowAutomatedActionResource;

class CreateWorkflowAutomatedAction extends CreateRecord
{
    protected static string $resource = WorkflowAutomatedActionResource::class;
}
