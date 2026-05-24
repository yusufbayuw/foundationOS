<?php

namespace Modules\Campus\Filament\Resources\CourseOfferingLecturers\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Campus\Filament\Resources\CourseOfferingLecturers\CourseOfferingLecturerResource;

class ViewCourseOfferingLecturer extends ViewRecord
{
    protected static string $resource = CourseOfferingLecturerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
