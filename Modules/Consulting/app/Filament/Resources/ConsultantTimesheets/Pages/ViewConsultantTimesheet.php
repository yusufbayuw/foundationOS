<?php

namespace Modules\Consulting\Filament\Resources\ConsultantTimesheets\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Consulting\Filament\Resources\ConsultantTimesheets\ConsultantTimesheetResource;

class ViewConsultantTimesheet extends ViewRecord
{
    protected static string $resource = ConsultantTimesheetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
