<?php

namespace Modules\Printing\Filament\Resources\Publications\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Printing\Filament\Resources\Publications\PublicationResource;

class CreatePublication extends CreateRecord
{
    protected static string $resource = PublicationResource::class;
}
