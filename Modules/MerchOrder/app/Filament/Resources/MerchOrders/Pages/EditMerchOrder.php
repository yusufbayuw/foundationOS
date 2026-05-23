<?php

namespace Modules\MerchOrder\Filament\Resources\MerchOrders\Pages;

use Filament\Resources\Pages\EditRecord;
use Modules\MerchOrder\Filament\Resources\MerchOrders\MerchOrderResource;

class EditMerchOrder extends EditRecord
{
    protected static string $resource = MerchOrderResource::class;
}
