<?php

namespace Modules\Training\Filament\Resources\TrainingBatches\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Training\Filament\Resources\TrainingBatches\TrainingBatchResource;

class ListTrainingBatches extends ListRecords
{
    protected static string $resource = TrainingBatchResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
