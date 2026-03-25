<?php

namespace Modules\School\Filament\Resources\Students\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\School\Filament\Resources\Students\StudentResource;

class CreateStudent extends CreateRecord
{
    protected static string $resource = StudentResource::class;
}
