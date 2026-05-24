<?php

namespace Modules\Boarding\Filament\Resources\BoardingLeavePermits\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Boarding\Filament\Resources\BoardingLeavePermits\BoardingLeavePermitResource;

class CreateBoardingLeavePermit extends CreateRecord
{
    protected static string $resource = BoardingLeavePermitResource::class;
}
