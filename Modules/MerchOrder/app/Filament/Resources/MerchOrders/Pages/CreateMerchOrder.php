<?php

namespace Modules\MerchOrder\Filament\Resources\MerchOrders\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\MerchOrder\Filament\Resources\MerchOrders\MerchOrderResource;

class CreateMerchOrder extends CreateRecord
{
    protected static string $resource = MerchOrderResource::class;
}
