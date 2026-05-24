<?php

namespace Modules\Cafeteria\Filament\Resources\CafeteriaInspections\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Cafeteria\Filament\Resources\CafeteriaInspections\CafeteriaInspectionResource;

class ViewCafeteriaInspection extends ViewRecord
{
    protected static string $resource = CafeteriaInspectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
