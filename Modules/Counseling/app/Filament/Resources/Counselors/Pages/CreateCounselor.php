<?php

namespace Modules\Counseling\Filament\Resources\Counselors\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Counseling\Filament\Resources\Counselors\CounselorResource;

class CreateCounselor extends CreateRecord
{
    protected static string $resource = CounselorResource::class;
}
