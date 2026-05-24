<?php

namespace Modules\Consulting\Filament\Resources\ConsultantTimesheets\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Consulting\Filament\Resources\ConsultantTimesheets\ConsultantTimesheetResource;

class ListConsultantTimesheets extends ListRecords
{
    protected static string $resource = ConsultantTimesheetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
