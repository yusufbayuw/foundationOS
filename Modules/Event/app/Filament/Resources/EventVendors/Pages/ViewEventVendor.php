<?php

namespace Modules\Event\Filament\Resources\EventVendors\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Event\Filament\Resources\EventVendors\EventVendorResource;

class ViewEventVendor extends ViewRecord
{
    protected static string $resource = EventVendorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
