<?php

namespace Modules\Enrollment\Filament\Resources\AdmissionPeriods\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Enrollment\Filament\Resources\AdmissionPeriods\AdmissionPeriodResource;

class EditAdmissionPeriod extends EditRecord
{
    protected static string $resource = AdmissionPeriodResource::class;

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
