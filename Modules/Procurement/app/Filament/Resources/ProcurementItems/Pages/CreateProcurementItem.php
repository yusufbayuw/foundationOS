<?php

namespace Modules\Procurement\Filament\Resources\ProcurementItems\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Procurement\Filament\Resources\ProcurementItems\ProcurementItemResource;

class CreateProcurementItem extends CreateRecord
{
    protected static string $resource = ProcurementItemResource::class;
}
