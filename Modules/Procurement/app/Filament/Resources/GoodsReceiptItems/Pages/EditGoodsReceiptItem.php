<?php

namespace Modules\Procurement\Filament\Resources\GoodsReceiptItems\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Procurement\Filament\Resources\GoodsReceiptItems\GoodsReceiptItemResource;

class EditGoodsReceiptItem extends EditRecord
{
    protected static string $resource = GoodsReceiptItemResource::class;

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
