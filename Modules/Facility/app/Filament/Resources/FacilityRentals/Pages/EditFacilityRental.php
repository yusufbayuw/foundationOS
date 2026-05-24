<?php

namespace Modules\Facility\Filament\Resources\FacilityRentals\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Facility\Filament\Resources\FacilityRentals\FacilityRentalResource;

class EditFacilityRental extends EditRecord
{
    protected static string $resource = FacilityRentalResource::class;

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
