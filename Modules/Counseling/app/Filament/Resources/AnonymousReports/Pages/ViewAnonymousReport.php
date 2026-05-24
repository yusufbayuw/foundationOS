<?php

namespace Modules\Counseling\Filament\Resources\AnonymousReports\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Counseling\Filament\Resources\AnonymousReports\AnonymousReportResource;

class ViewAnonymousReport extends ViewRecord
{
    protected static string $resource = AnonymousReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
