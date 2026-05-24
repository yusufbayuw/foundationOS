<?php

namespace Modules\Event\Filament\Resources\EventVendors\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Event\Filament\Resources\EventVendors\EventVendorResource;

class ListEventVendors extends ListRecords
{
    protected static string $resource = EventVendorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
