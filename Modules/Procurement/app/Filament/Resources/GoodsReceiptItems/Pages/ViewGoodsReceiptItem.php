<?php

namespace Modules\Procurement\Filament\Resources\GoodsReceiptItems\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Procurement\Filament\Resources\GoodsReceiptItems\GoodsReceiptItemResource;

class ViewGoodsReceiptItem extends ViewRecord
{
    protected static string $resource = GoodsReceiptItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
