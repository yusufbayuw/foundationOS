<?php

namespace Modules\Cafeteria\Filament\Resources\MenuStocks\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Cafeteria\Filament\Resources\MenuStocks\MenuStockResource;

class ViewMenuStock extends ViewRecord
{
    protected static string $resource = MenuStockResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
