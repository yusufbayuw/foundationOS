<?php

namespace Modules\Sales\Filament\Resources\SalesOrders\Pages;

use Filament\Resources\Pages\EditRecord;
use Modules\Sales\Filament\Resources\SalesOrders\SalesOrderResource;

class EditSalesOrder extends EditRecord
{
    protected static string $resource = SalesOrderResource::class;
}
