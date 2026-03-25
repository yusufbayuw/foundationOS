<?php

namespace Modules\Procurement\Filament\Resources\RfqVendors\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Procurement\Filament\Resources\RfqVendors\RfqVendorResource;

class ViewRfqVendor extends ViewRecord
{
    protected static string $resource = RfqVendorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
