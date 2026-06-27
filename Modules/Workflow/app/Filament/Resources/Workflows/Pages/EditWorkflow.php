<?php

namespace Modules\Workflow\Filament\Resources\Workflows\Pages;

use Filament\Resources\Pages\EditRecord;
use Illuminate\Validation\ValidationException;
use Modules\Workflow\Filament\Resources\Workflows\WorkflowResource;
use Modules\Workflow\Models\Workflow;

/**
 * @property Workflow $record
 */
class EditWorkflow extends EditRecord
{
    protected static string $resource = WorkflowResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if ($this->record->status->value === 'active') {
            throw ValidationException::withMessages([
                'status' => 'Workflow aktif bersifat immutable. Buat versi baru untuk perubahan berikutnya.',
            ]);
        }

        $data['updated_by'] = auth()->id();

        if (($data['status'] ?? null) === 'active' && empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        return $data;
    }
}
