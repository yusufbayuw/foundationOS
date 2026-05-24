<?php

namespace Modules\MerchOrder\Filament\Resources\MerchOrderItems\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\MerchOrder\Filament\Resources\MerchOrderItems\MerchOrderItemResource;

class CreateMerchOrderItem extends CreateRecord
{
    protected static string $resource = MerchOrderItemResource::class;
}
