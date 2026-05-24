<?php

namespace Modules\Clinic\Filament\Resources\InjuryReports\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Clinic\Filament\Resources\InjuryReports\InjuryReportResource;

class ListInjuryReports extends ListRecords
{
    protected static string $resource = InjuryReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
