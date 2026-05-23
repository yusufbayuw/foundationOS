<?php

namespace Modules\ItOps\Filament\Resources\SoftwareLicenses\Pages;

use Filament\Resources\Pages\ListRecords;
use Modules\ItOps\Filament\Resources\SoftwareLicenses\SoftwareLicenseResource;

class ListSoftwareLicenses extends ListRecords
{
    protected static string $resource = SoftwareLicenseResource::class;
}
