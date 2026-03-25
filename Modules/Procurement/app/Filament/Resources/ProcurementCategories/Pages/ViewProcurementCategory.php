<?php

namespace Modules\Procurement\Filament\Resources\ProcurementCategories\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Procurement\Filament\Resources\ProcurementCategories\ProcurementCategoryResource;

class ViewProcurementCategory extends ViewRecord
{
    protected static string $resource = ProcurementCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
