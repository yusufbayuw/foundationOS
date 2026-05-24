<?php

namespace Modules\Training\Filament\Resources\TrainingEnrollments\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Training\Filament\Resources\TrainingEnrollments\TrainingEnrollmentResource;

class ViewTrainingEnrollment extends ViewRecord
{
    protected static string $resource = TrainingEnrollmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
