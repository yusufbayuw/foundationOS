<?php

namespace Modules\Campus\Filament\Resources\CourseOfferingLecturers\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Campus\Filament\Resources\CourseOfferingLecturers\CourseOfferingLecturerResource;

class ListCourseOfferingLecturers extends ListRecords
{
    protected static string $resource = CourseOfferingLecturerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
