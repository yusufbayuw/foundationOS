<?php

namespace Modules\ItOps\Filament\Resources\IpAddressRecords\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\ItOps\Filament\Resources\IpAddressRecords\IpAddressRecordResource;

class ViewIpAddressRecord extends ViewRecord
{
    protected static string $resource = IpAddressRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
