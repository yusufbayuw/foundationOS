<?php

namespace Modules\Procurement\Filament\Resources\PurchaseRequisitions\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Procurement\Filament\Resources\PurchaseRequisitions\PurchaseRequisitionResource;

class CreatePurchaseRequisition extends CreateRecord
{
    protected static string $resource = PurchaseRequisitionResource::class;
}
