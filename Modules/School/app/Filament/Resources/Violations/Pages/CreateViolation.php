<?php

namespace Modules\School\Filament\Resources\Violations\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\School\Filament\Resources\Violations\ViolationResource;

class CreateViolation extends CreateRecord
{
    protected static string $resource = ViolationResource::class;
}
