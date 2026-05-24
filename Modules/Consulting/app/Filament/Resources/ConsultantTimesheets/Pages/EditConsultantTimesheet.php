<?php

namespace Modules\Consulting\Filament\Resources\ConsultantTimesheets\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Consulting\Filament\Resources\ConsultantTimesheets\ConsultantTimesheetResource;

class EditConsultantTimesheet extends EditRecord
{
    protected static string $resource = ConsultantTimesheetResource::class;

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
