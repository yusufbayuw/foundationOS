<?php

namespace Modules\Campus\Filament\Resources\CoursePrerequisites\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Campus\Filament\Resources\CoursePrerequisites\CoursePrerequisiteResource;

class CreateCoursePrerequisite extends CreateRecord
{
    protected static string $resource = CoursePrerequisiteResource::class;
}
