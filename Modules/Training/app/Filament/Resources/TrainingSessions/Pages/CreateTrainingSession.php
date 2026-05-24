<?php

namespace Modules\Training\Filament\Resources\TrainingSessions\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Training\Filament\Resources\TrainingSessions\TrainingSessionResource;

class CreateTrainingSession extends CreateRecord
{
    protected static string $resource = TrainingSessionResource::class;
}
