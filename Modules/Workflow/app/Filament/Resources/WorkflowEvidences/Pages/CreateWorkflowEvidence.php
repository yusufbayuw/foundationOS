<?php

namespace Modules\Workflow\Filament\Resources\WorkflowEvidences\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Workflow\Filament\Resources\WorkflowEvidences\WorkflowEvidenceResource;

class CreateWorkflowEvidence extends CreateRecord
{
    protected static string $resource = WorkflowEvidenceResource::class;
}
