<?php

namespace Modules\MerchOrder\Filament\Resources\MerchPickups\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\MerchOrder\Filament\Resources\MerchPickups\MerchPickupResource;

class CreateMerchPickup extends CreateRecord
{
    protected static string $resource = MerchPickupResource::class;
}
