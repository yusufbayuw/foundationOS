<?php

namespace Modules\Clinic\Filament\Resources\MedicationStocks\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Clinic\Filament\Resources\MedicationStocks\MedicationStockResource;

class ViewMedicationStock extends ViewRecord
{
    protected static string $resource = MedicationStockResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
