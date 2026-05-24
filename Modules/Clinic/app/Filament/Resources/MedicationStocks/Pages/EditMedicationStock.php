<?php

namespace Modules\Clinic\Filament\Resources\MedicationStocks\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Clinic\Filament\Resources\MedicationStocks\MedicationStockResource;

class EditMedicationStock extends EditRecord
{
    protected static string $resource = MedicationStockResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
