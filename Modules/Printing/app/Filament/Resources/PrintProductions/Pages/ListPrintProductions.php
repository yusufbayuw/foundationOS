<?php

namespace Modules\Printing\Filament\Resources\PrintProductions\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Printing\Filament\Resources\PrintProductions\PrintProductionResource;

class ListPrintProductions extends ListRecords
{
    protected static string $resource = PrintProductionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
