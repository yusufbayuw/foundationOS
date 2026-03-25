<?php

namespace Modules\School\Filament\Resources\Assessments\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\School\Filament\Resources\Assessments\AssessmentResource;

class CreateAssessment extends CreateRecord
{
    protected static string $resource = AssessmentResource::class;
}
