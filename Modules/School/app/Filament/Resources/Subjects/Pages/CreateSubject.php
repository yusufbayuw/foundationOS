<?php

namespace Modules\School\Filament\Resources\Subjects\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\School\Filament\Resources\Subjects\SubjectResource;

class CreateSubject extends CreateRecord
{
    protected static string $resource = SubjectResource::class;
}
