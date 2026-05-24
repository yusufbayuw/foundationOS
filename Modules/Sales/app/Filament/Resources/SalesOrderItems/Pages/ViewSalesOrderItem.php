<?php

namespace Modules\Sales\Filament\Resources\SalesOrderItems\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Sales\Filament\Resources\SalesOrderItems\SalesOrderItemResource;

class ViewSalesOrderItem extends ViewRecord
{
    protected static string $resource = SalesOrderItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
