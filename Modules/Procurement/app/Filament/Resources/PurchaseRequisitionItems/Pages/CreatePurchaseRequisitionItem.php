<?php

namespace Modules\Procurement\Filament\Resources\PurchaseRequisitionItems\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Procurement\Filament\Resources\PurchaseRequisitionItems\PurchaseRequisitionItemResource;

class CreatePurchaseRequisitionItem extends CreateRecord
{
    protected static string $resource = PurchaseRequisitionItemResource::class;
}
