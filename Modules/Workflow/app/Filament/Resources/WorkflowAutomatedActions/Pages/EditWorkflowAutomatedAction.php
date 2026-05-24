<?php

namespace Modules\Workflow\Filament\Resources\WorkflowAutomatedActions\Pages;

use Filament\Resources\Pages\EditRecord;
use Modules\Workflow\Filament\Resources\WorkflowAutomatedActions\WorkflowAutomatedActionResource;

class EditWorkflowAutomatedAction extends EditRecord
{
    protected static string $resource = WorkflowAutomatedActionResource::class;
}
