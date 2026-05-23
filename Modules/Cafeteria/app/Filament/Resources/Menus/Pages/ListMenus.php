<?php

namespace Modules\Cafeteria\Filament\Resources\Menus\Pages;

use Filament\Resources\Pages\ListRecords;
use Modules\Cafeteria\Filament\Resources\Menus\MenuResource;

class ListMenus extends ListRecords
{
    protected static string $resource = MenuResource::class;
}
