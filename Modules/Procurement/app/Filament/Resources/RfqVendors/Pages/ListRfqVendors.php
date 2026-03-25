<?php

namespace Modules\Procurement\Filament\Resources\RfqVendors\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Procurement\Filament\Resources\RfqVendors\RfqVendorResource;

class ListRfqVendors extends ListRecords
{
    protected static string $resource = RfqVendorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
