<?php

namespace Modules\Cms\Filament\Resources\MenuItems\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Cms\Filament\Resources\MenuItems\MenuItemResource;

class CreateMenuItem extends CreateRecord
{
    protected static string $resource = MenuItemResource::class;
}
