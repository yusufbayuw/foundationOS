<?php

namespace Modules\Procurement\Filament\Resources\PurchaseRequisitionItems\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Procurement\Filament\Resources\PurchaseRequisitionItems\PurchaseRequisitionItemResource;

class ViewPurchaseRequisitionItem extends ViewRecord
{
    protected static string $resource = PurchaseRequisitionItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
