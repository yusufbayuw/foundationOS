<?php

namespace Modules\Printing\Filament\Resources\PrintOrders\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Printing\Filament\Resources\PrintOrders\PrintOrderResource;

class CreatePrintOrder extends CreateRecord
{
    protected static string $resource = PrintOrderResource::class;
}
