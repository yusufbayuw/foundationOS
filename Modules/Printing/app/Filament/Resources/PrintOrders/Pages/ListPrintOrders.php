<?php

namespace Modules\Printing\Filament\Resources\PrintOrders\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Printing\Filament\Resources\PrintOrders\PrintOrderResource;

class ListPrintOrders extends ListRecords
{
    protected static string $resource = PrintOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
