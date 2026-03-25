<?php

namespace Modules\Procurement\Filament\Resources\PurchaseOrderItems\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Procurement\Filament\Resources\PurchaseOrderItems\PurchaseOrderItemResource;

class CreatePurchaseOrderItem extends CreateRecord
{
    protected static string $resource = PurchaseOrderItemResource::class;
}
