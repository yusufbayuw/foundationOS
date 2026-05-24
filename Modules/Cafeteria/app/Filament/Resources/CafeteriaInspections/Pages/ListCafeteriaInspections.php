<?php

namespace Modules\Cafeteria\Filament\Resources\CafeteriaInspections\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Cafeteria\Filament\Resources\CafeteriaInspections\CafeteriaInspectionResource;

class ListCafeteriaInspections extends ListRecords
{
    protected static string $resource = CafeteriaInspectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
