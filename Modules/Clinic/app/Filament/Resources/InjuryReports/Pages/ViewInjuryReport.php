<?php

namespace Modules\Clinic\Filament\Resources\InjuryReports\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Clinic\Filament\Resources\InjuryReports\InjuryReportResource;

class ViewInjuryReport extends ViewRecord
{
    protected static string $resource = InjuryReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
