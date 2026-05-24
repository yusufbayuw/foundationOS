<?php

namespace Modules\Counseling\Filament\Resources\AnonymousReports\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Counseling\Filament\Resources\AnonymousReports\AnonymousReportResource;

class ListAnonymousReports extends ListRecords
{
    protected static string $resource = AnonymousReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
