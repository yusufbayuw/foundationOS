<?php

namespace Modules\Training\Filament\Resources\TrainingSessions\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Training\Filament\Resources\TrainingSessions\TrainingSessionResource;

class ViewTrainingSession extends ViewRecord
{
    protected static string $resource = TrainingSessionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
