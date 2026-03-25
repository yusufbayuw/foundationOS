<?php

namespace Modules\Procurement\Filament\Resources\ProcurementItems\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Procurement\Filament\Resources\ProcurementItems\ProcurementItemResource;

class ListProcurementItems extends ListRecords
{
    protected static string $resource = ProcurementItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
