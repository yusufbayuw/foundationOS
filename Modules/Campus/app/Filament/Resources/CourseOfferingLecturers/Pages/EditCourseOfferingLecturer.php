<?php

namespace Modules\Campus\Filament\Resources\CourseOfferingLecturers\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Campus\Filament\Resources\CourseOfferingLecturers\CourseOfferingLecturerResource;

class EditCourseOfferingLecturer extends EditRecord
{
    protected static string $resource = CourseOfferingLecturerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
