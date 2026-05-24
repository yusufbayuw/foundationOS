<?php

namespace Modules\Boarding\Filament\Resources\BoardingLeavePermits\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Boarding\Filament\Resources\BoardingLeavePermits\BoardingLeavePermitResource;

class ListBoardingLeavePermits extends ListRecords
{
    protected static string $resource = BoardingLeavePermitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
