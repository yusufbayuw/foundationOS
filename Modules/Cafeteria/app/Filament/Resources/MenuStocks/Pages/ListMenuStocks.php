<?php

namespace Modules\Cafeteria\Filament\Resources\MenuStocks\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Cafeteria\Filament\Resources\MenuStocks\MenuStockResource;

class ListMenuStocks extends ListRecords
{
    protected static string $resource = MenuStockResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
