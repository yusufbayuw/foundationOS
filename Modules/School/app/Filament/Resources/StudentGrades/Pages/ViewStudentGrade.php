<?php

namespace Modules\School\Filament\Resources\StudentGrades\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\School\Filament\Resources\StudentGrades\StudentGradeResource;

class ViewStudentGrade extends ViewRecord
{
    protected static string $resource = StudentGradeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
