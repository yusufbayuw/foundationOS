<?php

namespace Modules\Clinic\Filament\Resources\MedicationStocks\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Clinic\Filament\Resources\MedicationStocks\MedicationStockResource;

class ListMedicationStocks extends ListRecords
{
    protected static string $resource = MedicationStockResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
