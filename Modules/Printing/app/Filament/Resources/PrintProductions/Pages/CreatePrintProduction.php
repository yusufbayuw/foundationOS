<?php

namespace Modules\Printing\Filament\Resources\PrintProductions\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Printing\Filament\Resources\PrintProductions\PrintProductionResource;

class CreatePrintProduction extends CreateRecord
{
    protected static string $resource = PrintProductionResource::class;
}
