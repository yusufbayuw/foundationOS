<?php

namespace Modules\ItOps\Filament\Resources\SoftwareLicenses\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\ItOps\Filament\Resources\SoftwareLicenses\SoftwareLicenseResource;

class CreateSoftwareLicense extends CreateRecord
{
    protected static string $resource = SoftwareLicenseResource::class;
}
