<?php

namespace Modules\School\Filament\Resources\StudentAssessmentAnswers\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\School\Filament\Resources\StudentAssessmentAnswers\StudentAssessmentAnswerResource;

class ViewStudentAssessmentAnswer extends ViewRecord
{
    protected static string $resource = StudentAssessmentAnswerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
