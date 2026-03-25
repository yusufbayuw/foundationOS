<?php

namespace Modules\Procurement\Filament\Resources\ProcurementCategories\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Procurement\Filament\Resources\ProcurementCategories\ProcurementCategoryResource;

class CreateProcurementCategory extends CreateRecord
{
    protected static string $resource = ProcurementCategoryResource::class;
}
