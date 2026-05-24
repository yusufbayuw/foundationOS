<?php

namespace Modules\Printing\Filament\Resources\PrintProductions\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Printing\Filament\Resources\PrintProductions\PrintProductionResource;

class ViewPrintProduction extends ViewRecord
{
    protected static string $resource = PrintProductionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
