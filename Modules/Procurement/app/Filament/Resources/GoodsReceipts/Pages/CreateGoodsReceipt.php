<?php

namespace Modules\Procurement\Filament\Resources\GoodsReceipts\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Procurement\Filament\Resources\GoodsReceipts\GoodsReceiptResource;

class CreateGoodsReceipt extends CreateRecord
{
    protected static string $resource = GoodsReceiptResource::class;
}
