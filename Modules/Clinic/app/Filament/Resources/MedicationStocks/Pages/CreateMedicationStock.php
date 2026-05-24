<?php

namespace Modules\Clinic\Filament\Resources\MedicationStocks\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Clinic\Filament\Resources\MedicationStocks\MedicationStockResource;

class CreateMedicationStock extends CreateRecord
{
    protected static string $resource = MedicationStockResource::class;
}
