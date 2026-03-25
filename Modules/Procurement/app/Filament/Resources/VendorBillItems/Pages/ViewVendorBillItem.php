<?php

namespace Modules\Procurement\Filament\Resources\VendorBillItems\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Procurement\Filament\Resources\VendorBillItems\VendorBillItemResource;

class ViewVendorBillItem extends ViewRecord
{
    protected static string $resource = VendorBillItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
