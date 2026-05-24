<?php

namespace Modules\Printing\Filament\Resources\PrintMaterials\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Printing\Filament\Resources\PrintMaterials\PrintMaterialResource;

class ViewPrintMaterial extends ViewRecord
{
    protected static string $resource = PrintMaterialResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
