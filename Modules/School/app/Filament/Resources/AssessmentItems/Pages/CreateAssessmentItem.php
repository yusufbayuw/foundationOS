<?php

namespace Modules\School\Filament\Resources\AssessmentItems\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\School\Filament\Resources\AssessmentItems\AssessmentItemResource;

class CreateAssessmentItem extends CreateRecord
{
    protected static string $resource = AssessmentItemResource::class;
}
