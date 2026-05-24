<?php

namespace Modules\Campus\Filament\Resources\CourseOfferingLecturers\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Campus\Filament\Resources\CourseOfferingLecturers\CourseOfferingLecturerResource;

class CreateCourseOfferingLecturer extends CreateRecord
{
    protected static string $resource = CourseOfferingLecturerResource::class;
}
