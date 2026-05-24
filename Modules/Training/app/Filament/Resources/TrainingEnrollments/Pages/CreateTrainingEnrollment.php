<?php

namespace Modules\Training\Filament\Resources\TrainingEnrollments\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Training\Filament\Resources\TrainingEnrollments\TrainingEnrollmentResource;

class CreateTrainingEnrollment extends CreateRecord
{
    protected static string $resource = TrainingEnrollmentResource::class;
}
