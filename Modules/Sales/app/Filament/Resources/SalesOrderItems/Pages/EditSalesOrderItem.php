<?php

namespace Modules\Sales\Filament\Resources\SalesOrderItems\Pages;

use Filament\Resources\Pages\EditRecord;
use Modules\Sales\Filament\Resources\SalesOrderItems\SalesOrderItemResource;

class EditSalesOrderItem extends EditRecord
{
    protected static string $resource = SalesOrderItemResource::class;
}
