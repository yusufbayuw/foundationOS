<?php

namespace Modules\Transport\Filament\Resources\BoardingLogs\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Transport\Filament\Resources\BoardingLogs\BoardingLogResource;

class CreateBoardingLog extends CreateRecord
{
    protected static string $resource = BoardingLogResource::class;
}
