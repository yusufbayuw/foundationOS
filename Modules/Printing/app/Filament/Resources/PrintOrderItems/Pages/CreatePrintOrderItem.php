<?php

namespace Modules\Printing\Filament\Resources\PrintOrderItems\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Printing\Filament\Resources\PrintOrderItems\PrintOrderItemResource;

class CreatePrintOrderItem extends CreateRecord
{
    protected static string $resource = PrintOrderItemResource::class;
}
