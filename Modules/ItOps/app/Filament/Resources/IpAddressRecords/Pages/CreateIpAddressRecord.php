<?php

namespace Modules\ItOps\Filament\Resources\IpAddressRecords\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\ItOps\Filament\Resources\IpAddressRecords\IpAddressRecordResource;

class CreateIpAddressRecord extends CreateRecord
{
    protected static string $resource = IpAddressRecordResource::class;
}
