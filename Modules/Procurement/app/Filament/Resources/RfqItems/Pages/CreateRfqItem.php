<?php

namespace Modules\Procurement\Filament\Resources\RfqItems\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Procurement\Filament\Resources\RfqItems\RfqItemResource;

class CreateRfqItem extends CreateRecord
{
    protected static string $resource = RfqItemResource::class;
}
