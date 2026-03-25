<?php

namespace Modules\Campus\Filament\Resources\CourseOfferings\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Campus\Filament\Resources\CourseOfferings\CourseOfferingResource;

class CreateCourseOffering extends CreateRecord
{
    protected static string $resource = CourseOfferingResource::class;
}
