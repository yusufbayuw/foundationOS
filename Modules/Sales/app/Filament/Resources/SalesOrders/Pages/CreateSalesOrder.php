<?php

namespace Modules\Sales\Filament\Resources\SalesOrders\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Sales\Filament\Resources\SalesOrders\SalesOrderResource;

class CreateSalesOrder extends CreateRecord
{
    protected static string $resource = SalesOrderResource::class;
}
