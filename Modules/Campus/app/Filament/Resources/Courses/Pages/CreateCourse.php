<?php

namespace Modules\Campus\Filament\Resources\Courses\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Campus\Filament\Resources\Courses\CourseResource;

class CreateCourse extends CreateRecord
{
    protected static string $resource = CourseResource::class;
}
