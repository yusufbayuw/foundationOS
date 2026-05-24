<?php

namespace Modules\Printing\Filament\Resources\Royalties\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Printing\Filament\Resources\Royalties\RoyaltyResource;

class CreateRoyalty extends CreateRecord
{
    protected static string $resource = RoyaltyResource::class;
}
