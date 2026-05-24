<?php

namespace Modules\Clinic\Filament\Resources\VaccinationRecords\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Clinic\Filament\Resources\VaccinationRecords\VaccinationRecordResource;

class EditVaccinationRecord extends EditRecord
{
    protected static string $resource = VaccinationRecordResource::class;

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
