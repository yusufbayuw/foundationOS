<?php

namespace Modules\School\Filament\Resources\StudentAssessmentAnswers\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\School\Filament\Resources\StudentAssessmentAnswers\StudentAssessmentAnswerResource;

class CreateStudentAssessmentAnswer extends CreateRecord
{
    protected static string $resource = StudentAssessmentAnswerResource::class;
}
