<?php

namespace Modules\Procurement\Filament\Resources\VendorBillItems\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Procurement\Filament\Resources\VendorBillItems\VendorBillItemResource;

class ListVendorBillItems extends ListRecords
{
    protected static string $resource = VendorBillItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
