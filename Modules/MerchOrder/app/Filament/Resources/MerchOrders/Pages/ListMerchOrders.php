<?php

namespace Modules\MerchOrder\Filament\Resources\MerchOrders\Pages;

use Filament\Resources\Pages\ListRecords;
use Modules\MerchOrder\Filament\Resources\MerchOrders\MerchOrderResource;

class ListMerchOrders extends ListRecords
{
    protected static string $resource = MerchOrderResource::class;
}
