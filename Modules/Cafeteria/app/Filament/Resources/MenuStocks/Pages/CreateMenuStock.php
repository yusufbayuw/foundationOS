<?php

namespace Modules\Cafeteria\Filament\Resources\MenuStocks\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Cafeteria\Filament\Resources\MenuStocks\MenuStockResource;

class CreateMenuStock extends CreateRecord
{
    protected static string $resource = MenuStockResource::class;
}
