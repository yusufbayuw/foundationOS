<?php

namespace Modules\School\Filament\Resources\StudentRiskScores\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\School\Filament\Resources\StudentRiskScores\StudentRiskScoreResource;

class CreateStudentRiskScore extends CreateRecord
{
    protected static string $resource = StudentRiskScoreResource::class;
}
