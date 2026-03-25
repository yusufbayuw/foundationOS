<?php

namespace Modules\Global\Filament\Resources\Countries\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Global\Filament\Resources\Countries\CountryResource;

class CreateCountry extends CreateRecord
{
    protected static string $resource = CountryResource::class;
}
