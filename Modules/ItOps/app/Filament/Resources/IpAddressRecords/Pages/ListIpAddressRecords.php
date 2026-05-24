<?php

namespace Modules\ItOps\Filament\Resources\IpAddressRecords\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\ItOps\Filament\Resources\IpAddressRecords\IpAddressRecordResource;

class ListIpAddressRecords extends ListRecords
{
    protected static string $resource = IpAddressRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
