<?php

namespace Modules\Cafeteria\Filament\Resources\CafeteriaTransactions\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Cafeteria\Filament\Resources\CafeteriaTransactions\CafeteriaTransactionResource;

class ListCafeteriaTransactions extends ListRecords
{
    protected static string $resource = CafeteriaTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
