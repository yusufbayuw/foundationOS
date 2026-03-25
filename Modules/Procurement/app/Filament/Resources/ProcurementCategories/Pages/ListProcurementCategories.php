<?php

namespace Modules\Procurement\Filament\Resources\ProcurementCategories\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Procurement\Filament\Resources\ProcurementCategories\ProcurementCategoryResource;

class ListProcurementCategories extends ListRecords
{
    protected static string $resource = ProcurementCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
