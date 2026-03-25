<?php

namespace Modules\Procurement\Filament\Resources\GoodsReceiptItems\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Procurement\Filament\Resources\GoodsReceiptItems\GoodsReceiptItemResource;

class CreateGoodsReceiptItem extends CreateRecord
{
    protected static string $resource = GoodsReceiptItemResource::class;
}
