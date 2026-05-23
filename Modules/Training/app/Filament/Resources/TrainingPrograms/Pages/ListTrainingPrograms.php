<?php

namespace Modules\Training\Filament\Resources\TrainingPrograms\Pages;

use Filament\Resources\Pages\ListRecords;
use Modules\Training\Filament\Resources\TrainingPrograms\TrainingProgramResource;

class ListTrainingPrograms extends ListRecords
{
    protected static string $resource = TrainingProgramResource::class;
}
