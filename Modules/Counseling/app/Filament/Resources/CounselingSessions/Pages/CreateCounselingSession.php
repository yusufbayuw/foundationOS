<?php

namespace Modules\Counseling\Filament\Resources\CounselingSessions\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Counseling\Filament\Resources\CounselingSessions\CounselingSessionResource;

class CreateCounselingSession extends CreateRecord
{
    protected static string $resource = CounselingSessionResource::class;
}
