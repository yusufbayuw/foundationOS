<?php

namespace Modules\Counseling\Filament\Resources\CounselingCases\Pages;

use Filament\Resources\Pages\ListRecords;
use Modules\Counseling\Filament\Resources\CounselingCases\CounselingCaseResource;

class ListCounselingCases extends ListRecords
{
    protected static string $resource = CounselingCaseResource::class;
}
