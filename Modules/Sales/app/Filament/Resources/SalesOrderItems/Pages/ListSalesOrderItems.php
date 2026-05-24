<?php

namespace Modules\Sales\Filament\Resources\SalesOrderItems\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Sales\Filament\Resources\SalesOrderItems\SalesOrderItemResource;

class ListSalesOrderItems extends ListRecords
{
    protected static string $resource = SalesOrderItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
