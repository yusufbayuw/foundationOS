<?php

namespace App\Filament\Imports;

use Modules\Inventory\Models\Warehouse;

class WarehouseImporter extends BaseModelImporter
{
    protected static ?string $model = Warehouse::class;
}
