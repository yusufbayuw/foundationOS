<?php

namespace Modules\School\Filament\Resources\SchoolClasses\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\School\Filament\Resources\SchoolClasses\SchoolClassResource;

class CreateSchoolClass extends CreateRecord
{
    protected static string $resource = SchoolClassResource::class;
}
