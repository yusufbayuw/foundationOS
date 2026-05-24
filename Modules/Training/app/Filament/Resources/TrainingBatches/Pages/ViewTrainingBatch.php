<?php

namespace Modules\Training\Filament\Resources\TrainingBatches\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Training\Filament\Resources\TrainingBatches\TrainingBatchResource;

class ViewTrainingBatch extends ViewRecord
{
    protected static string $resource = TrainingBatchResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
