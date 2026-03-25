<?php

namespace Modules\Procurement\Filament\Resources\PurchaseOrderItems\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Procurement\Filament\Resources\PurchaseOrderItems\PurchaseOrderItemResource;

class ViewPurchaseOrderItem extends ViewRecord
{
    protected static string $resource = PurchaseOrderItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
