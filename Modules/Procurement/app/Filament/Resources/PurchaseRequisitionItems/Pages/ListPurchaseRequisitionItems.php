<?php

namespace Modules\Procurement\Filament\Resources\PurchaseRequisitionItems\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Procurement\Filament\Resources\PurchaseRequisitionItems\PurchaseRequisitionItemResource;

class ListPurchaseRequisitionItems extends ListRecords
{
    protected static string $resource = PurchaseRequisitionItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
