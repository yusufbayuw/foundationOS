<?php

namespace Modules\Property\Filament\Resources\Properties\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Property\Filament\Resources\Properties\PropertyResource;

class CreateProperty extends CreateRecord
{
    protected static string $resource = PropertyResource::class;
}
