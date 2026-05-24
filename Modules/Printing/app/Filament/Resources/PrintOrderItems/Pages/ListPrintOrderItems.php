<?php

namespace Modules\Printing\Filament\Resources\PrintOrderItems\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Printing\Filament\Resources\PrintOrderItems\PrintOrderItemResource;

class ListPrintOrderItems extends ListRecords
{
    protected static string $resource = PrintOrderItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
