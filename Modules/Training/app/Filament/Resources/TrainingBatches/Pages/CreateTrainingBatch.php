<?php

namespace Modules\Training\Filament\Resources\TrainingBatches\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Training\Filament\Resources\TrainingBatches\TrainingBatchResource;

class CreateTrainingBatch extends CreateRecord
{
    protected static string $resource = TrainingBatchResource::class;
}
