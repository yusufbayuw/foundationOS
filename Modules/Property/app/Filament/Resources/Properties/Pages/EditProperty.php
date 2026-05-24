<?php

namespace Modules\Property\Filament\Resources\Properties\Pages;

use Filament\Resources\Pages\EditRecord;
use Modules\Property\Filament\Resources\Properties\PropertyResource;

class EditProperty extends EditRecord
{
    protected static string $resource = PropertyResource::class;
}
