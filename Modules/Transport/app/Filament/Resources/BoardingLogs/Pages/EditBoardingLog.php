<?php

namespace Modules\Transport\Filament\Resources\BoardingLogs\Pages;

use Filament\Resources\Pages\EditRecord;
use Modules\Transport\Filament\Resources\BoardingLogs\BoardingLogResource;

class EditBoardingLog extends EditRecord
{
    protected static string $resource = BoardingLogResource::class;
}
