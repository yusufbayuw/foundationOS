<?php

namespace Modules\Counseling\Filament\Resources\AnonymousReports\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Counseling\Filament\Resources\AnonymousReports\AnonymousReportResource;

class CreateAnonymousReport extends CreateRecord
{
    protected static string $resource = AnonymousReportResource::class;
}
