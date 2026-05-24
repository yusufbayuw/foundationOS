<?php

namespace Modules\Clinic\Filament\Resources\Allergies\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Clinic\Filament\Resources\Allergies\AllergyResource;

class CreateAllergy extends CreateRecord
{
    protected static string $resource = AllergyResource::class;
}
