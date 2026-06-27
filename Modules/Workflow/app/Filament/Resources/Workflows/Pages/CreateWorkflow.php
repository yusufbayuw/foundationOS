<?php

namespace Modules\Workflow\Filament\Resources\Workflows\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Workflow\Filament\Resources\Workflows\WorkflowResource;

class CreateWorkflow extends CreateRecord
{
    protected static string $resource = WorkflowResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $userId = auth()->id();

        $data['tenant_id'] ??= current_tenant_id();
        $data['created_by'] = $userId;
        $data['updated_by'] = $userId;

        if (($data['status'] ?? null) === 'active' && empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        return $data;
    }
}
