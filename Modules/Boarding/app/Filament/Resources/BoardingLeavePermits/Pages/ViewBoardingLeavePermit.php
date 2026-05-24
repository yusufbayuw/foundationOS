<?php

namespace Modules\Boarding\Filament\Resources\BoardingLeavePermits\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Boarding\Filament\Resources\BoardingLeavePermits\BoardingLeavePermitResource;

class ViewBoardingLeavePermit extends ViewRecord
{
    protected static string $resource = BoardingLeavePermitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
