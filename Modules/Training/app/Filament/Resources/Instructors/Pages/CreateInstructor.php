<?php

namespace Modules\Training\Filament\Resources\Instructors\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Training\Filament\Resources\Instructors\InstructorResource;

class CreateInstructor extends CreateRecord
{
    protected static string $resource = InstructorResource::class;
}
