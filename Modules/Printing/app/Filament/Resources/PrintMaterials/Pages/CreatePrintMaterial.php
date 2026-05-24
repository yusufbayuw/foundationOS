<?php

namespace Modules\Printing\Filament\Resources\PrintMaterials\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Printing\Filament\Resources\PrintMaterials\PrintMaterialResource;

class CreatePrintMaterial extends CreateRecord
{
    protected static string $resource = PrintMaterialResource::class;
}
