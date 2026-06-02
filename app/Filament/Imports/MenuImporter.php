<?php

namespace App\Filament\Imports;

use Modules\Cafeteria\Models\Menu;

class MenuImporter extends BaseModelImporter
{
    protected static ?string $model = Menu::class;
}
