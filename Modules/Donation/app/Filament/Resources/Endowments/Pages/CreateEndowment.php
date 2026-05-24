<?php

namespace Modules\Donation\Filament\Resources\Endowments\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Donation\Filament\Resources\Endowments\EndowmentResource;

class CreateEndowment extends CreateRecord
{
    protected static string $resource = EndowmentResource::class;
}
