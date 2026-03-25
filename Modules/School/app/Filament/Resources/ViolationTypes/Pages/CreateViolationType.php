<?php

namespace Modules\School\Filament\Resources\ViolationTypes\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\School\Filament\Resources\ViolationTypes\ViolationTypeResource;

class CreateViolationType extends CreateRecord
{
    protected static string $resource = ViolationTypeResource::class;
}
