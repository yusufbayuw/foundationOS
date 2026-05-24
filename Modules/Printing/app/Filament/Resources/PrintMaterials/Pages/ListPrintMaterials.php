<?php

namespace Modules\Printing\Filament\Resources\PrintMaterials\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Printing\Filament\Resources\PrintMaterials\PrintMaterialResource;

class ListPrintMaterials extends ListRecords
{
    protected static string $resource = PrintMaterialResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
