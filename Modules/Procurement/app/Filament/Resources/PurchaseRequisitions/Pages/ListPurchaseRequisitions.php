<?php

namespace Modules\Procurement\Filament\Resources\PurchaseRequisitions\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Procurement\Filament\Resources\PurchaseRequisitions\PurchaseRequisitionResource;

class ListPurchaseRequisitions extends ListRecords
{
    protected static string $resource = PurchaseRequisitionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
