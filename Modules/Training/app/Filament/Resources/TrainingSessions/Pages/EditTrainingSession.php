<?php

namespace Modules\Training\Filament\Resources\TrainingSessions\Pages;

use Filament\Resources\Pages\EditRecord;
use Modules\Training\Filament\Resources\TrainingSessions\TrainingSessionResource;

class EditTrainingSession extends EditRecord
{
    protected static string $resource = TrainingSessionResource::class;
}
