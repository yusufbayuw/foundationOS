<?php

namespace Modules\Procurement\Filament\Resources\GoodsReceiptItems\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Procurement\Filament\Resources\GoodsReceiptItems\GoodsReceiptItemResource;

class ListGoodsReceiptItems extends ListRecords
{
    protected static string $resource = GoodsReceiptItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
