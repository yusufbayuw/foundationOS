<?php

namespace Modules\Counseling\Filament\Resources\CounselingCases\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Counseling\Filament\Resources\CounselingCases\CounselingCaseResource;

class CreateCounselingCase extends CreateRecord
{
    protected static string $resource = CounselingCaseResource::class;
}
