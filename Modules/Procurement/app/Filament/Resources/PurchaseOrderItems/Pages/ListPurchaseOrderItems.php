<?php

namespace Modules\Procurement\Filament\Resources\PurchaseOrderItems\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Procurement\Filament\Resources\PurchaseOrderItems\PurchaseOrderItemResource;

class ListPurchaseOrderItems extends ListRecords
{
    protected static string $resource = PurchaseOrderItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
