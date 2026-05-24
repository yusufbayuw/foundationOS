<?php

namespace Modules\Sales\Filament\Resources\SalesOrderItems\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Sales\Filament\Resources\SalesOrderItems\SalesOrderItemResource;

class CreateSalesOrderItem extends CreateRecord
{
    protected static string $resource = SalesOrderItemResource::class;
}
