<?php

namespace Modules\Procurement\Filament\Resources\PurchaseRequisitionItems\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Procurement\Filament\Resources\PurchaseRequisitionItems\PurchaseRequisitionItemResource;

class EditPurchaseRequisitionItem extends EditRecord
{
    protected static string $resource = PurchaseRequisitionItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
