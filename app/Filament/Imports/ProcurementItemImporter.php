<?php

namespace App\Filament\Imports;

use Modules\Procurement\Models\ProcurementItem;

class ProcurementItemImporter extends BaseModelImporter
{
    protected static ?string $model = ProcurementItem::class;
}
