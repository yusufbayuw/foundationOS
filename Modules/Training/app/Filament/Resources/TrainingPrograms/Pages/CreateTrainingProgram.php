<?php

namespace Modules\Training\Filament\Resources\TrainingPrograms\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Training\Filament\Resources\TrainingPrograms\TrainingProgramResource;

class CreateTrainingProgram extends CreateRecord
{
    protected static string $resource = TrainingProgramResource::class;
}
